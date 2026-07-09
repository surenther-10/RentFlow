<?php

namespace App\Controllers;

use App\Models\PropertyModel;
use App\Models\TenantModel;
use App\Models\LeaseModel;
use App\Models\PaymentModel;
use App\Models\MaintenanceModel;
use App\Models\NotificationModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $session = session();
        $role = $session->get('role');

        if ($role === 'admin') {
            return redirect()->to('/admin/dashboard');
        } else if ($role === 'owner') {
            return redirect()->to('/owner/dashboard');
        } else if ($role === 'tenant') {
            return redirect()->to('/tenant/dashboard');
        }

        return redirect()->to('/login');
    }

    public function admin()
    {
        return $this->showAdminOwnerDashboard('admin');
    }

    public function owner()
    {
        return $this->showAdminOwnerDashboard('owner');
    }

    protected function showAdminOwnerDashboard($role)
    {
        $session = session();
        $userId = $session->get('id');

        $propertyModel = new PropertyModel();
        $tenantModel = new TenantModel();
        $leaseModel = new LeaseModel();
        $paymentModel = new PaymentModel();
        $maintenanceModel = new MaintenanceModel();
        $notifModel = new NotificationModel();

        $data = [
            'role' => $role,
            'username' => $session->get('username'),
            'alerts' => [],
            'stats' => [],
            'notifications' => $notifModel->getUnread($userId)
        ];

        // 1. Core Metrics
        $data['stats']['total_properties'] = $propertyModel->countAll();
        $data['stats']['occupied_properties'] = $propertyModel->where('availability_status', 'rented')->countAllResults();
        $data['stats']['vacant_properties'] = $propertyModel->where('availability_status', 'available')->countAllResults();
        $data['stats']['maintenance_properties'] = $propertyModel->where('availability_status', 'maintenance')->countAllResults();

        $data['stats']['total_tenants'] = $tenantModel->countAll();
        $data['stats']['pending_tickets'] = $maintenanceModel->where('status', 'Open')->orWhere('status', 'In Progress')->countAllResults();

        // Monthly Revenue (current month's collections)
        $currentMonth = date('Y-m');
        $monthlyRevenue = $paymentModel->selectSum('amount')
                                      ->where('status', 'Paid')
                                      ->like('payment_date', $currentMonth, 'after')
                                      ->first();
        $data['stats']['monthly_revenue'] = $monthlyRevenue['amount'] ?? 0;

        // 2. Dashboard Alerts
        // Alert A: Leases expiring within 30 days
        $thirtyDaysFromNow = date('Y-m-d', strtotime('+30 days'));
        $today = date('Y-m-d');
        $expiringLeases = $leaseModel->select('leases.*, properties.name as property_name, tenants.name as tenant_name')
            ->join('properties', 'properties.id = leases.property_id')
            ->join('tenants', 'tenants.id = leases.tenant_id')
            ->where('leases.end_date <=', $thirtyDaysFromNow)
            ->where('leases.end_date >=', $today)
            ->where('leases.status', 'active')
            ->findAll();

        foreach ($expiringLeases as $lease) {
            $data['alerts'][] = [
                'type' => 'warning',
                'message' => "Lease <strong>{$lease['agreement_number']}</strong> for tenant <strong>{$lease['tenant_name']}</strong> ({$lease['property_name']}) expires on " . date('d M Y', strtotime($lease['end_date'])) . "."
            ];
        }

        // Alert B: Rent due alerts for the current month
        $activeLeases = $leaseModel->select('leases.*, properties.name as property_name, tenants.name as tenant_name')
            ->join('properties', 'properties.id = leases.property_id')
            ->join('tenants', 'tenants.id = leases.tenant_id')
            ->where('leases.status', 'active')
            ->findAll();

        foreach ($activeLeases as $lease) {
            $hasPaid = $paymentModel->where('lease_id', $lease['id'])
                ->like('payment_date', $currentMonth, 'after')
                ->where('status', 'Paid')
                ->first();
            
            if (!$hasPaid) {
                $data['alerts'][] = [
                    'type' => 'danger',
                    'message' => "Rent for <strong>{$lease['tenant_name']}</strong> ({$lease['property_name']}) is <strong>DUE</strong> for " . date('F Y') . "."
                ];
            }
        }

        // 3. Recent activity list
        $data['recent_payments'] = $paymentModel->getPaymentsWithDetails();
        $data['recent_payments'] = array_slice($data['recent_payments'], 0, 5);

        $data['recent_tickets'] = $maintenanceModel->getTicketsWithDetails();
        $data['recent_tickets'] = array_slice($data['recent_tickets'], 0, 5);

        // 4. Chart Datasets (Revenue last 6 months)
        $db = \Config\Database::connect();
        
        // Build month intervals for last 6 months
        $revenueData = [];
        $revenueLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStr = date('Y-m', strtotime("-$i months"));
            $monthLabel = date('M Y', strtotime("-$i months"));
            
            $sumQuery = $db->table('rent_payments')
                           ->selectSum('amount')
                           ->where('status', 'Paid')
                           ->like('payment_date', $monthStr, 'after')
                           ->get()
                           ->getRowArray();
            
            $revenueData[] = $sumQuery['amount'] ?? 0;
            $revenueLabels[] = $monthLabel;
        }
        $data['charts']['revenue_labels'] = $revenueLabels;
        $data['charts']['revenue_data'] = $revenueData;

        // Occupancy count dataset
        $data['charts']['occupancy_data'] = [
            $data['stats']['occupied_properties'],
            $data['stats']['vacant_properties'],
            $data['stats']['maintenance_properties']
        ];

        return view('dashboard/admin_owner', $data);
    }

    public function tenant()
    {
        $session = session();
        $userId = $session->get('id');

        $tenantModel = new TenantModel();
        $leaseModel = new LeaseModel();
        $paymentModel = new PaymentModel();
        $maintenanceModel = new MaintenanceModel();
        $notifModel = new NotificationModel();

        $data = [
            'role' => 'tenant',
            'username' => $session->get('username'),
            'alerts' => [],
            'stats' => [],
            'notifications' => $notifModel->getUnread($userId)
        ];

        $tenant = $tenantModel->where('user_id', $userId)->first();
        if (!$tenant) {
            return view('dashboard/tenant_error', ['message' => 'No tenant profile associated with your user. Please contact the administrator.']);
        }

        $data['tenant'] = $tenant;

        $leases = $leaseModel->getLeasesByUserId($userId);
        $activeLease = null;
        foreach ($leases as $lease) {
            if ($lease['status'] === 'active') {
                $activeLease = $lease;
                break;
            }
        }

        $data['active_lease'] = $activeLease;

        if ($activeLease) {
            $currentYearMonth = date('Y-m');
            $paidThisMonth = $paymentModel->where('lease_id', $activeLease['id'])
                ->like('payment_date', $currentYearMonth, 'after')
                ->where('status', 'Paid')
                ->first();

            $data['rent_paid_this_month'] = (bool)$paidThisMonth;

            if (!$paidThisMonth) {
                $data['alerts'][] = [
                    'type' => 'danger',
                    'message' => "Your monthly rent of <strong>₹" . number_format($activeLease['monthly_rent'], 2) . "</strong> is due for " . date('F Y') . ". Please make a payment."
                ];
            }

            // Expiry alert
            $thirtyDaysFromNow = date('Y-m-d', strtotime('+30 days'));
            $today = date('Y-m-d');
            if ($activeLease['end_date'] <= $thirtyDaysFromNow && $activeLease['end_date'] >= $today) {
                $data['alerts'][] = [
                    'type' => 'warning',
                    'message' => "Your lease expires on " . date('d M Y', strtotime($activeLease['end_date'])) . ". Please contact the owner for renewal."
                ];
            }

            $data['payments'] = $paymentModel->getPaymentsByUserId($userId);
            $data['tickets'] = $maintenanceModel->getTicketsByUserId($userId);
        } else {
            $data['rent_paid_this_month'] = false;
            $data['payments'] = [];
            $data['tickets'] = [];
        }

        return view('dashboard/tenant', $data);
    }

    public function markNotificationsRead()
    {
        $userId = session()->get('id');
        $notifModel = new NotificationModel();
        $notifModel->where('user_id', $userId)->update(null, ['is_read' => 1]);
        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    public function search()
    {
        $q = $this->request->getVar('q');
        if (empty($q) || strlen($q) < 2) {
            return $this->response->setJSON([]);
        }

        $session = session();
        $role = $session->get('role');

        $db = \Config\Database::connect();
        $results = [];

        // Preload tenant profile if authenticated role is tenant
        $tenant = null;
        if ($role === 'tenant') {
            $tenant = $db->table('tenants')->where('user_id', $session->get('id'))->get()->getRowArray();
            if (!$tenant) {
                return $this->response->setJSON([]);
            }
        }

        // 1. Properties
        if ($role === 'admin' || $role === 'owner') {
            $properties = $db->table('properties')
                ->select('id, name, type, city')
                ->like('name', $q)
                ->orLike('city', $q)
                ->orLike('address', $q)
                ->limit(5)
                ->get()
                ->getResultArray();
            
            if (!empty($properties)) {
                foreach ($properties as $p) {
                    $results[] = [
                        'category' => 'Properties',
                        'title' => $p['name'] . ' (' . $p['type'] . ' - ' . $p['city'] . ')',
                        'url' => base_url('properties?search=' . urlencode($p['name'])),
                        'icon' => 'fa-building'
                    ];
                }
            }
        } else if ($role === 'tenant' && $tenant) {
            $activeLease = $db->table('leases')
                ->where('tenant_id', $tenant['id'])
                ->where('status', 'active')
                ->get()
                ->getRowArray();

            if ($activeLease) {
                $property = $db->table('properties')
                    ->where('id', $activeLease['property_id'])
                    ->groupStart()
                        ->like('name', $q)
                        ->orLike('city', $q)
                        ->orLike('address', $q)
                    ->groupEnd()
                    ->get()
                    ->getRowArray();

                if ($property) {
                    $results[] = [
                        'category' => 'Properties',
                        'title' => $property['name'] . ' (' . $property['type'] . ' - ' . $property['city'] . ')',
                        'url' => base_url('dashboard'),
                        'icon' => 'fa-building'
                    ];
                }
            }
        }

        // 2. Tenants
        if ($role === 'admin' || $role === 'owner') {
            $tenants = $db->table('tenants')
                ->select('id, name, email, mobile')
                ->like('name', $q)
                ->orLike('email', $q)
                ->orLike('mobile', $q)
                ->limit(5)
                ->get()
                ->getResultArray();

            if (!empty($tenants)) {
                foreach ($tenants as $t) {
                    $results[] = [
                        'category' => 'Tenants',
                        'title' => $t['name'] . ' (' . $t['email'] . ')',
                        'url' => base_url('tenants?search=' . urlencode($t['name'])),
                        'icon' => 'fa-user-tie'
                    ];
                }
            }
        }

        // 3. Leases
        if ($role === 'admin' || $role === 'owner') {
            $leases = $db->table('leases')
                ->select('leases.id, leases.agreement_number, properties.name as property_name, tenants.name as tenant_name')
                ->join('properties', 'properties.id = leases.property_id')
                ->join('tenants', 'tenants.id = leases.tenant_id')
                ->like('leases.agreement_number', $q)
                ->orLike('properties.name', $q)
                ->orLike('tenants.name', $q)
                ->limit(5)
                ->get()
                ->getResultArray();

            if (!empty($leases)) {
                foreach ($leases as $l) {
                    $results[] = [
                        'category' => 'Leases',
                        'title' => $l['agreement_number'] . ' - ' . $l['tenant_name'] . ' (' . $l['property_name'] . ')',
                        'url' => base_url('leases'),
                        'icon' => 'fa-file-signature'
                    ];
                }
            }
        } else if ($role === 'tenant' && $tenant) {
            $lease = $db->table('leases')
                ->select('leases.id, leases.agreement_number, properties.name as property_name, tenants.name as tenant_name')
                ->join('properties', 'properties.id = leases.property_id')
                ->join('tenants', 'tenants.id = leases.tenant_id')
                ->where('leases.tenant_id', $tenant['id'])
                ->groupStart()
                    ->like('leases.agreement_number', $q)
                    ->orLike('properties.name', $q)
                ->groupEnd()
                ->get()
                ->getRowArray();

            if ($lease) {
                $results[] = [
                    'category' => 'Leases',
                    'title' => $lease['agreement_number'] . ' - ' . $lease['tenant_name'] . ' (' . $lease['property_name'] . ')',
                    'url' => base_url('dashboard'),
                    'icon' => 'fa-file-signature'
                ];
            }
        }

        // 4. Rent Payments
        $paymentsQuery = $db->table('rent_payments')
            ->select('rent_payments.id, rent_payments.receipt_number, rent_payments.amount, tenants.name as tenant_name')
            ->join('leases', 'leases.id = rent_payments.lease_id')
            ->join('tenants', 'tenants.id = leases.tenant_id');
            
        if ($role === 'tenant' && $tenant) {
            $paymentsQuery->where('leases.tenant_id', $tenant['id']);
        }

        $payments = $paymentsQuery->groupStart()
            ->like('rent_payments.receipt_number', $q)
            ->orLike('tenants.name', $q)
            ->groupEnd()
            ->limit(5)
            ->get()
            ->getResultArray();

        if (!empty($payments)) {
            foreach ($payments as $pay) {
                $results[] = [
                    'category' => 'Rent Collections',
                    'title' => 'Receipt ' . $pay['receipt_number'] . ' - ₹' . number_format($pay['amount'], 2) . ' (' . $pay['tenant_name'] . ')',
                    'url' => base_url('rent/receipt/' . $pay['id']),
                    'icon' => 'fa-credit-card'
                ];
            }
        }

        // 5. Maintenance Tickets
        $ticketsQuery = $db->table('maintenance_tickets')
            ->select('maintenance_tickets.id, maintenance_tickets.title, maintenance_tickets.status, tenants.name as tenant_name')
            ->join('tenants', 'tenants.id = maintenance_tickets.tenant_id');

        if ($role === 'tenant' && $tenant) {
            $ticketsQuery->where('maintenance_tickets.tenant_id', $tenant['id']);
        }

        $tickets = $ticketsQuery->groupStart()
            ->like('maintenance_tickets.title', $q)
            ->orLike('maintenance_tickets.description', $q)
            ->groupEnd()
            ->limit(5)
            ->get()
            ->getResultArray();

        if (!empty($tickets)) {
            foreach ($tickets as $tick) {
                $results[] = [
                    'category' => 'Maintenance Tickets',
                    'title' => $tick['title'] . ' (' . $tick['status'] . ')',
                    'url' => base_url('maintenance/details/' . $tick['id']),
                    'icon' => 'fa-screwdriver-wrench'
                ];
            }
        }
        return $this->response->setJSON($results);
    }
}
