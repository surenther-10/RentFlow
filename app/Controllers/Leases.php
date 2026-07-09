<?php

namespace App\Controllers;

use App\Models\LeaseModel;
use App\Models\PropertyModel;
use App\Models\TenantModel;

class Leases extends BaseController
{
    protected $leaseModel;
    protected $propertyModel;
    protected $tenantModel;

    public function __construct()
    {
        $this->leaseModel = new LeaseModel();
        $this->propertyModel = new PropertyModel();
        $this->tenantModel = new TenantModel();
    }

    public function index()
    {
        $data['leases'] = $this->leaseModel->getLeasesWithDetails();
        $data['properties'] = $this->propertyModel->findAll();
        // Available properties for new leases
        $data['available_properties'] = $this->propertyModel->where('availability_status', 'available')->findAll();
        $data['tenants'] = $this->tenantModel->findAll();
        return view('leases/index', $data);
    }

    public function details($id)
    {
        $lease = $this->leaseModel->getLeasesWithDetails($id);
        if (!$lease) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Lease not found']);
        }
        return $this->response->setJSON(['status' => 'success', 'data' => $lease]);
    }

    public function store()
    {
        $rules = [
            'agreement_number' => 'required|max_length[100]|is_unique[leases.agreement_number]',
            'property_id'      => 'required|integer',
            'tenant_id'        => 'required|integer',
            'start_date'       => 'required|valid_date',
            'end_date'         => 'required|valid_date',
            'security_deposit' => 'required|numeric',
            'monthly_rent'     => 'required|numeric',
            'status'           => 'required|in_list[active,expired,terminated]',
            'doc'              => 'permit_empty|max_size[doc,4096]|ext_in[doc,pdf,jpg,jpeg,png]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $docFile = $this->request->getFile('doc');
        $docName = null;
        if ($docFile && $docFile->isValid() && !$docFile->hasMoved()) {
            $docName = $docFile->getRandomName();
            $docFile->move(FCPATH . 'uploads/leases', $docName);
        }

        $propertyId = $this->request->getPost('property_id');
        $status = $this->request->getPost('status');

        $data = [
            'agreement_number' => $this->request->getPost('agreement_number'),
            'property_id'      => $propertyId,
            'tenant_id'        => $this->request->getPost('tenant_id'),
            'start_date'       => $this->request->getPost('start_date'),
            'end_date'         => $this->request->getPost('end_date'),
            'security_deposit' => $this->request->getPost('security_deposit'),
            'monthly_rent'     => $this->request->getPost('monthly_rent'),
            'status'           => $status,
            'doc_path'         => $docName ? 'uploads/leases/' . $docName : null,
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        $this->leaseModel->insert($data);

        if ($status === 'active') {
            $this->propertyModel->update($propertyId, ['availability_status' => 'rented']);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Failed to create lease.');
        }

        return redirect()->to('/leases')->with('success', 'Lease agreement created successfully!');
    }

    public function update($id)
    {
        $lease = $this->leaseModel->find($id);
        if (!$lease) {
            return redirect()->to('/leases')->with('error', 'Lease not found.');
        }

        $rules = [
            'agreement_number' => "required|max_length[100]|is_unique[leases.agreement_number,id,{$id}]",
            'property_id'      => 'required|integer',
            'tenant_id'        => 'required|integer',
            'start_date'       => 'required|valid_date',
            'end_date'         => 'required|valid_date',
            'security_deposit' => 'required|numeric',
            'monthly_rent'     => 'required|numeric',
            'status'           => 'required|in_list[active,expired,terminated]',
            'doc'              => 'permit_empty|max_size[doc,4096]|ext_in[doc,pdf,jpg,jpeg,png]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $docFile = $this->request->getFile('doc');
        $docName = $lease['doc_path'];
        if ($docFile && $docFile->isValid() && !$docFile->hasMoved()) {
            if ($lease['doc_path'] && file_exists(FCPATH . $lease['doc_path'])) {
                @unlink(FCPATH . $lease['doc_path']);
            }
            $docName = 'uploads/leases/' . $docFile->getRandomName();
            $docFile->move(FCPATH . 'uploads/leases', basename($docName));
        }

        $propertyId = $this->request->getPost('property_id');
        $status = $this->request->getPost('status');

        $data = [
            'agreement_number' => $this->request->getPost('agreement_number'),
            'property_id'      => $propertyId,
            'tenant_id'        => $this->request->getPost('tenant_id'),
            'start_date'       => $this->request->getPost('start_date'),
            'end_date'         => $this->request->getPost('end_date'),
            'security_deposit' => $this->request->getPost('security_deposit'),
            'monthly_rent'     => $this->request->getPost('monthly_rent'),
            'status'           => $status,
            'doc_path'         => $docName,
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        $this->leaseModel->update($id, $data);

        // Update Property availability status
        if ($status === 'active') {
            $this->propertyModel->update($propertyId, ['availability_status' => 'rented']);
        } else {
            // Check other active leases on this property
            $otherActive = $this->leaseModel->where('property_id', $propertyId)
                                            ->where('status', 'active')
                                            ->where('id !=', $id)
                                            ->first();
            if (!$otherActive) {
                $this->propertyModel->update($propertyId, ['availability_status' => 'available']);
            }
        }

        $db->transComplete();

        return redirect()->to('/leases')->with('success', 'Lease updated successfully!');
    }

    public function attemptRenew($id)
    {
        $oldLease = $this->leaseModel->find($id);
        if (!$oldLease) {
            return redirect()->to('/leases')->with('error', 'Lease not found.');
        }

        $rules = [
            'agreement_number' => 'required|max_length[100]|is_unique[leases.agreement_number]',
            'start_date'       => 'required|valid_date',
            'end_date'         => 'required|valid_date',
            'security_deposit' => 'required|numeric',
            'monthly_rent'     => 'required|numeric',
            'doc'              => 'permit_empty|max_size[doc,4096]|ext_in[doc,pdf,jpg,jpeg,png]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $docFile = $this->request->getFile('doc');
        $docName = null;
        if ($docFile && $docFile->isValid() && !$docFile->hasMoved()) {
            $docName = $docFile->getRandomName();
            $docFile->move(FCPATH . 'uploads/leases', $docName);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Mark old lease as expired
        $this->leaseModel->update($id, ['status' => 'expired']);

        // 2. Insert new lease
        $newData = [
            'agreement_number' => $this->request->getPost('agreement_number'),
            'property_id'      => $oldLease['property_id'],
            'tenant_id'        => $oldLease['tenant_id'],
            'start_date'       => $this->request->getPost('start_date'),
            'end_date'         => $this->request->getPost('end_date'),
            'security_deposit' => $this->request->getPost('security_deposit'),
            'monthly_rent'     => $this->request->getPost('monthly_rent'),
            'status'           => 'active',
            'doc_path'         => $docName ? 'uploads/leases/' . $docName : $oldLease['doc_path'],
        ];
        $this->leaseModel->insert($newData);

        // Update Property availability status to rented
        $this->propertyModel->update($oldLease['property_id'], ['availability_status' => 'rented']);

        $db->transComplete();

        return redirect()->to('/leases')->with('success', 'Lease agreement renewed successfully!');
    }

    public function delete($id)
    {
        $lease = $this->leaseModel->find($id);
        if (!$lease) {
            return redirect()->to('/leases')->with('error', 'Lease not found.');
        }

        $propertyId = $lease['property_id'];

        $db = \Config\Database::connect();
        $db->transStart();

        if ($lease['doc_path'] && file_exists(FCPATH . $lease['doc_path'])) {
            @unlink(FCPATH . $lease['doc_path']);
        }

        $this->leaseModel->delete($id);

        $otherActive = $this->leaseModel->where('property_id', $propertyId)
                                        ->where('status', 'active')
                                        ->first();
        if (!$otherActive) {
            $this->propertyModel->update($propertyId, ['availability_status' => 'available']);
        }

        $db->transComplete();

        return redirect()->to('/leases')->with('success', 'Lease deleted successfully.');
    }
}
