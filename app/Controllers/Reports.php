<?php

namespace App\Controllers;

use App\Models\PropertyModel;
use App\Models\TenantModel;
use App\Models\LeaseModel;
use App\Models\PaymentModel;
use App\Models\MaintenanceModel;

class Reports extends BaseController
{
    protected $propertyModel;
    protected $tenantModel;
    protected $leaseModel;
    protected $paymentModel;
    protected $maintenanceModel;

    public function __construct()
    {
        $this->propertyModel = new PropertyModel();
        $this->tenantModel = new TenantModel();
        $this->leaseModel = new LeaseModel();
        $this->paymentModel = new PaymentModel();
        $this->maintenanceModel = new MaintenanceModel();
    }

    public function index()
    {
        $session = session();
        if ($session->get('role') !== 'admin' && $session->get('role') !== 'owner') {
            return redirect()->to('/dashboard')->with('error', 'Only owners and administrators can access reports.');
        }

        // Summary Data
        $data['total_properties'] = $this->propertyModel->countAll();
        $data['rented_count'] = $this->propertyModel->where('availability_status', 'rented')->countAllResults();
        $data['available_count'] = $this->propertyModel->where('availability_status', 'available')->countAllResults();
        $data['maintenance_count'] = $this->propertyModel->where('availability_status', 'maintenance')->countAllResults();

        $data['occupancy_rate'] = $data['total_properties'] > 0 ? round(($data['rented_count'] / $data['total_properties']) * 100, 1) : 0;

        $totalRent = $this->paymentModel->selectSum('amount')->where('status', 'Paid')->first();
        $data['total_collected'] = $totalRent['amount'] ?? 0;

        $pendingRent = $this->paymentModel->selectSum('amount')->where('status', 'Pending')->first();
        $data['total_pending'] = $pendingRent['amount'] ?? 0;

        $overdueRent = $this->paymentModel->selectSum('amount')->where('status', 'Overdue')->first();
        $data['total_overdue'] = $overdueRent['amount'] ?? 0;

        $data['ticket_total'] = $this->maintenanceModel->countAll();
        $data['ticket_open'] = $this->maintenanceModel->where('status', 'Open')->countAllResults();
        $data['ticket_inprogress'] = $this->maintenanceModel->where('status', 'In Progress')->countAllResults();
        $data['ticket_completed'] = $this->maintenanceModel->where('status', 'Completed')->countAllResults();
        $data['ticket_closed'] = $this->maintenanceModel->where('status', 'Closed')->countAllResults();

        // Monthly collections (Group by Month)
        $db = \Config\Database::connect();
        $data['monthly_collections'] = $db->table('rent_payments')
            ->select("DATE_FORMAT(payment_date, '%Y-%m') as month, SUM(amount) as total_amount, COUNT(id) as transaction_count")
            ->where('status', 'Paid')
            ->groupBy('month')
            ->orderBy('month', 'DESC')
            ->get()
            ->getResultArray();

        return view('reports/index', $data);
    }

    public function exportRent()
    {
        $payments = $this->paymentModel->getPaymentsWithDetails();

        $filename = 'Rent_Collection_Report_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Receipt Number', 'Tenant Name', 'Property', 'Agreement Number', 'Amount Paid (INR)', 'Payment Date', 'Payment Method', 'Status', 'Notes']);

        foreach ($payments as $p) {
            fputcsv($output, [
                $p['receipt_number'],
                $p['tenant_name'],
                $p['property_name'],
                $p['agreement_number'],
                $p['amount'],
                $p['payment_date'],
                $p['payment_method'],
                $p['status'],
                $p['notes']
            ]);
        }

        fclose($output);
        exit;
    }

    public function exportOccupancy()
    {
        $properties = $this->propertyModel->findAll();

        $filename = 'Property_Occupancy_Report_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Property Name', 'Property Type', 'Rent Amount (INR)', 'Rooms', 'Availability Status', 'Address']);

        foreach ($properties as $p) {
            fputcsv($output, [
                $p['name'],
                $p['type'],
                $p['rent_amount'],
                $p['rooms'],
                ucfirst($p['availability_status']),
                $p['address']
            ]);
        }

        fclose($output);
        exit;
    }

    public function exportTenants()
    {
        // Join tenant details with active lease property
        $db = \Config\Database::connect();
        $tenants = $db->table('tenants')
            ->select('tenants.*, leases.agreement_number, properties.name as property_name, properties.rent_amount')
            ->join('leases', 'leases.tenant_id = tenants.id AND leases.status = "active"', 'left')
            ->join('properties', 'properties.id = leases.property_id', 'left')
            ->get()
            ->getResultArray();

        $filename = 'Tenant_Report_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Tenant Name', 'Mobile', 'Email', 'Aadhaar Number', 'PAN Number', 'Assigned Property', 'Agreement Number', 'Monthly Rent (INR)', 'Address']);

        foreach ($tenants as $t) {
            fputcsv($output, [
                $t['name'],
                $t['mobile'],
                $t['email'],
                $t['aadhaar_number'],
                $t['pan_number'],
                $t['property_name'] ?? 'Not Assigned',
                $t['agreement_number'] ?? 'N/A',
                $t['rent_amount'] ?? 'N/A',
                $t['address']
            ]);
        }

        fclose($output);
        exit;
    }

    public function exportMaintenance()
    {
        $tickets = $this->maintenanceModel->getTicketsWithDetails();

        $filename = 'Maintenance_Report_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Ticket ID', 'Property Name', 'Tenant Name', 'Tenant Mobile', 'Issue Title', 'Description', 'Status', 'Assigned Technician', 'Raised Date']);

        foreach ($tickets as $t) {
            fputcsv($output, [
                'TKT-' . str_pad($t['id'], 5, '0', STR_PAD_LEFT),
                $t['property_name'],
                $t['tenant_name'],
                $t['tenant_mobile'],
                $t['title'],
                $t['description'],
                $t['status'],
                $t['assigned_technician'] ?? 'None',
                $t['created_at']
            ]);
        }

        fclose($output);
        exit;
    }

    public function exportRevenue()
    {
        $db = \Config\Database::connect();
        $payments = $db->table('rent_payments')
            ->select("DATE_FORMAT(payment_date, '%Y-%m') as month, SUM(amount) as total_amount, COUNT(id) as transaction_count")
            ->where('status', 'Paid')
            ->groupBy('month')
            ->orderBy('month', 'DESC')
            ->get()
            ->getResultArray();

        $filename = 'Revenue_Report_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Month Period', 'Total Revenue Collected (INR)', 'Total Transactions Count']);

        foreach ($payments as $p) {
            fputcsv($output, [
                $p['month'],
                $p['total_amount'],
                $p['transaction_count']
            ]);
        }

        fclose($output);
        exit;
    }
}
