<?php

namespace App\Controllers;

use App\Models\TenantModel;
use App\Models\UserModel;
use App\Models\PropertyModel;
use App\Models\LeaseModel;

class Tenants extends BaseController
{
    protected $tenantModel;
    protected $userModel;
    protected $propertyModel;
    protected $leaseModel;

    public function __construct()
    {
        $this->tenantModel = new TenantModel();
        $this->userModel = new UserModel();
        $this->propertyModel = new PropertyModel();
        $this->leaseModel = new LeaseModel();
    }

    public function index()
    {
        $this->migrateOrphanTenants();

        // Join tenant with active lease property details
        $db = \Config\Database::connect();
        $data['tenants'] = $db->table('tenants')
            ->select('tenants.*, users.profile_photo, properties.name as property_name, properties.id as property_id, leases.agreement_number')
            ->join('users', 'users.id = tenants.user_id', 'left')
            ->join('leases', 'leases.tenant_id = tenants.id AND leases.status = "active"', 'left')
            ->join('properties', 'properties.id = leases.property_id', 'left')
            ->get()
            ->getResultArray();

        // All tenant users for edit dropdown
        $data['all_tenant_users'] = $this->userModel->where('role', 'tenant')->findAll();

        // Get unassigned tenant users
        $usedUserIds = $this->tenantModel->select('user_id')->where('user_id IS NOT NULL', null, false)->findAll();
        $usedIds = array_column($usedUserIds, 'user_id');

        if (!empty($usedIds)) {
            $data['available_users'] = $this->userModel->where('role', 'tenant')
                                                      ->whereNotIn('id', $usedIds)
                                                      ->findAll();
        } else {
            $data['available_users'] = $this->userModel->where('role', 'tenant')->findAll();
        }

        // Get available properties for assignment
        $data['available_properties'] = $this->propertyModel->where('availability_status', 'available')->findAll();
        $data['properties'] = $this->propertyModel->findAll();

        return view('tenants/index', $data);
    }

    public function details($id)
    {
        $tenant = $this->tenantModel->find($id);
        if (!$tenant) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tenant not found']);
        }
        
        if ($tenant['user_id']) {
            $user = $this->userModel->find($tenant['user_id']);
            $tenant['profile_photo'] = $user['profile_photo'] ?? null;
        } else {
            $tenant['profile_photo'] = null;
        }

        // Find if they have an active lease
        $activeLease = $this->leaseModel->where('tenant_id', $id)->where('status', 'active')->first();
        $tenant['active_lease'] = $activeLease;

        return $this->response->setJSON(['status' => 'success', 'data' => $tenant]);
    }

    public function store()
    {
        $rules = [
            'name'           => 'required|min_length[3]|max_length[255]',
            'mobile'         => 'required|min_length[10]|max_length[20]',
            'email'          => 'required|valid_email|max_length[100]',
            'aadhaar_number' => 'permit_empty|min_length[12]|max_length[20]',
            'pan_number'     => 'permit_empty|min_length[10]|max_length[20]',
            'address'        => 'required',
            'profile_photo'  => 'permit_empty|is_image[profile_photo]|max_size[profile_photo,5120]|ext_in[profile_photo,jpg,jpeg,png,webp]',
            'doc'            => 'permit_empty|max_size[doc,4096]|ext_in[doc,pdf,jpg,jpeg,png]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $photoFile = $this->request->getFile('profile_photo');
        $photoPath = null;
        if ($photoFile && $photoFile->isValid() && !$photoFile->hasMoved()) {
            $photoName = $photoFile->getRandomName();
            $photoFile->move(FCPATH . 'uploads/profile', $photoName);
            $photoPath = 'uploads/profile/' . $photoName;
        }

        $docFile = $this->request->getFile('doc');
        $docName = null;
        if ($docFile && $docFile->isValid() && !$docFile->hasMoved()) {
            $docName = $docFile->getRandomName();
            $docFile->move(FCPATH . 'uploads/documents', $docName);
        }

        $name = $this->request->getPost('name');
        $baseUsername = strtolower(str_replace(' ', '', $name));
        $baseUsername = preg_replace('/[^a-z0-9]/', '', $baseUsername);
        if (empty($baseUsername)) {
            $baseUsername = 'tenant';
        }
        
        $username = $baseUsername;
        $counter = 1;
        while ($this->userModel->where('username', $username)->first()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        $tempPassword = 'RentFlow-' . mt_rand(1000, 9999);

        $db = \Config\Database::connect();
        $db->transStart();

        $userData = [
            'username'      => $username,
            'email'         => $this->request->getPost('email'),
            'password'      => password_hash($tempPassword, PASSWORD_BCRYPT),
            'role'          => 'tenant',
            'role_id'       => 3,
            'status'        => 'active',
            'profile_photo' => $photoPath
        ];

        $this->userModel->insert($userData);
        $userId = $this->userModel->getInsertID();

        // 2. Save Tenant Profile
        $tenantData = [
            'user_id'        => $userId,
            'name'           => $name,
            'mobile'         => $this->request->getPost('mobile'),
            'email'          => $this->request->getPost('email'),
            'aadhaar_number' => $this->request->getPost('aadhaar_number'),
            'pan_number'     => $this->request->getPost('pan_number'),
            'address'        => $this->request->getPost('address'),
            'profile_photo'  => null,
            'doc_path'       => $docName ? 'uploads/documents/' . $docName : null,
        ];
        $this->tenantModel->insert($tenantData);
        $tenantId = $this->tenantModel->getInsertID();

        // 3. Assign Property (Auto create basic Lease if property_id selected)
        $propertyId = $this->request->getPost('property_id');
        if (!empty($propertyId)) {
            $property = $this->propertyModel->find($propertyId);
            if ($property) {
                $leaseData = [
                    'agreement_number' => 'LEASE-' . date('Y') . '-' . mt_rand(10000, 99999),
                    'property_id'      => $propertyId,
                    'tenant_id'        => $tenantId,
                    'start_date'       => date('Y-m-d'),
                    'end_date'         => date('Y-m-d', strtotime('+1 year -1 day')),
                    'security_deposit' => $property['rent_amount'] * 2,
                    'monthly_rent'     => $property['rent_amount'],
                    'status'           => 'active',
                ];
                $this->leaseModel->insert($leaseData);
                $this->propertyModel->update($propertyId, ['availability_status' => 'rented']);
            }
        }

        $db->transComplete();

        $successMsg = "Tenant profile and user account created successfully!<br>Username: <strong>{$username}</strong><br>Temporary Password: <strong>{$tempPassword}</strong><br>Please prompt the tenant to change their password on first login.";
        return redirect()->to('/tenants')->with('success', $successMsg);
    }

    public function update($id)
    {
        $tenant = $this->tenantModel->find($id);
        if (!$tenant) {
            return redirect()->to('/tenants')->with('error', 'Tenant not found.');
        }

        $rules = [
            'name'           => 'required|min_length[3]|max_length[255]',
            'mobile'         => 'required|min_length[10]|max_length[20]',
            'email'          => 'required|valid_email|max_length[100]',
            'aadhaar_number' => 'permit_empty|min_length[12]|max_length[20]',
            'pan_number'     => 'permit_empty|min_length[10]|max_length[20]',
            'address'        => 'required',
            'profile_photo'  => 'permit_empty|is_image[profile_photo]|max_size[profile_photo,5120]|ext_in[profile_photo,jpg,jpeg,png,webp]',
            'doc'            => 'permit_empty|max_size[doc,4096]|ext_in[doc,pdf,jpg,jpeg,png]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userId = $tenant['user_id'];

        $db = \Config\Database::connect();
        $db->transStart();

        $photoFile = $this->request->getFile('profile_photo');
        if ($photoFile && $photoFile->isValid() && !$photoFile->hasMoved()) {
            if (!empty($userId)) {
                $user = $this->userModel->find($userId);
                if ($user && !empty($user['profile_photo']) && file_exists(FCPATH . $user['profile_photo'])) {
                    @unlink(FCPATH . $user['profile_photo']);
                }
            }
            $newName = $photoFile->getRandomName();
            $photoFile->move(FCPATH . 'uploads/profile', $newName);
            $photoPath = 'uploads/profile/' . $newName;

            if (!empty($userId)) {
                $this->userModel->update($userId, ['profile_photo' => $photoPath]);
            }
        }

        if (!empty($userId)) {
            $this->userModel->update($userId, [
                'email' => $this->request->getPost('email')
            ]);
        }

        $docFile = $this->request->getFile('doc');
        $docName = $tenant['doc_path'];
        if ($docFile && $docFile->isValid() && !$docFile->hasMoved()) {
            if ($tenant['doc_path'] && file_exists(FCPATH . $tenant['doc_path'])) {
                @unlink(FCPATH . $tenant['doc_path']);
            }
            $docName = 'uploads/documents/' . $docFile->getRandomName();
            $docFile->move(FCPATH . 'uploads/documents', basename($docName));
        }

        $data = [
            'user_id'        => !empty($userId) ? $userId : null,
            'name'           => $this->request->getPost('name'),
            'mobile'         => $this->request->getPost('mobile'),
            'email'          => $this->request->getPost('email'),
            'aadhaar_number' => $this->request->getPost('aadhaar_number'),
            'pan_number'     => $this->request->getPost('pan_number'),
            'address'        => $this->request->getPost('address'),
            'profile_photo'  => null,
            'doc_path'       => $docName,
        ];

        $this->tenantModel->update($id, $data);
        $db->transComplete();

        return redirect()->to('/tenants')->with('success', 'Tenant profile updated successfully!');
    }

    public function delete($id)
    {
        $tenant = $this->tenantModel->find($id);
        if (!$tenant) {
            return redirect()->to('/tenants')->with('error', 'Tenant not found.');
        }

        // Delete photo from user record if exists
        if ($tenant['user_id']) {
            $user = $this->userModel->find($tenant['user_id']);
            if ($user && !empty($user['profile_photo']) && file_exists(FCPATH . $user['profile_photo'])) {
                @unlink(FCPATH . $user['profile_photo']);
            }
        }
        if ($tenant['doc_path'] && file_exists(FCPATH . $tenant['doc_path'])) {
            @unlink(FCPATH . $tenant['doc_path']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        if ($tenant['user_id']) {
            $this->userModel->delete($tenant['user_id']);
        }
        $this->tenantModel->delete($id);

        $db->transComplete();

        return redirect()->to('/tenants')->with('success', 'Tenant and linked user account deleted successfully!');
    }

    protected function migrateOrphanTenants()
    {
        $db = \Config\Database::connect();
        $tenants = $this->tenantModel->findAll();

        foreach ($tenants as $tenant) {
            $userExists = false;
            if ($tenant['user_id']) {
                $user = $this->userModel->find($tenant['user_id']);
                if ($user) {
                    $userExists = true;
                }
            }

            if (!$userExists) {
                // Generate a unique username
                $baseUsername = strtolower(str_replace(' ', '', $tenant['name']));
                $baseUsername = preg_replace('/[^a-z0-9]/', '', $baseUsername);
                if (empty($baseUsername)) {
                    $baseUsername = 'tenant';
                }
                
                $username = $baseUsername;
                $counter = 1;
                while ($this->userModel->where('username', $username)->first()) {
                    $username = $baseUsername . $counter;
                    $counter++;
                }

                // Generate a unique email if empty or duplicate
                $email = $tenant['email'];
                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) || $this->userModel->where('email', $email)->first()) {
                    $email = $username . '@rentflow.com';
                }

                $db->transStart();
                
                $userData = [
                    'username'      => $username,
                    'email'         => $email,
                    'password'      => password_hash('password123', PASSWORD_BCRYPT),
                    'role'          => 'tenant',
                    'role_id'       => 3,
                    'status'        => 'active',
                    'profile_photo' => $tenant['profile_photo'] ?? null
                ];

                $this->userModel->insert($userData);
                $userId = $this->userModel->getInsertID();

                $this->tenantModel->update($tenant['id'], [
                    'user_id' => $userId,
                    'email'   => $email
                ]);

                $db->transComplete();
            }
        }
    }
}
