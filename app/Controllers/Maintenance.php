<?php

namespace App\Controllers;

use App\Models\MaintenanceModel;
use App\Models\CommentModel;
use App\Models\PropertyModel;
use App\Models\TenantModel;

class Maintenance extends BaseController
{
    protected $maintenanceModel;
    protected $commentModel;
    protected $propertyModel;
    protected $tenantModel;

    public function __construct()
    {
        $this->maintenanceModel = new MaintenanceModel();
        $this->commentModel = new CommentModel();
        $this->propertyModel = new PropertyModel();
        $this->tenantModel = new TenantModel();
    }

    public function index()
    {
        $session = session();
        $role = $session->get('role');
        $userId = $session->get('id');

        if ($role === 'admin' || $role === 'owner') {
            $data['tickets'] = $this->maintenanceModel->getTicketsWithDetails();
            $data['properties'] = $this->propertyModel->findAll();
        } else {
            // Find tenant ID
            $tenant = $this->tenantModel->where('user_id', $userId)->first();
            if ($tenant) {
                $data['tickets'] = $this->maintenanceModel->getTicketsByUserId($userId);
                
                // Find property they reside in
                $db = \Config\Database::connect();
                $lease = $db->table('leases')
                    ->where('tenant_id', $tenant['id'])
                    ->where('status', 'active')
                    ->get()
                    ->getRowArray();
                $data['tenant_property'] = $lease ? $this->propertyModel->find($lease['property_id']) : null;
            } else {
                $data['tickets'] = [];
                $data['tenant_property'] = null;
            }
            $data['properties'] = [];
        }

        return view('maintenance/index', $data);
    }

    public function details($id)
    {
        $ticket = $this->maintenanceModel->getTicketsWithDetails($id);
        if (!$ticket) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Ticket not found']);
        }

        $comments = $this->commentModel->getCommentsByTicket($id);
        $ticket['comments'] = $comments;

        return $this->response->setJSON(['status' => 'success', 'data' => $ticket]);
    }

    public function store()
    {
        $session = session();
        $userId = $session->get('id');

        $tenant = $this->tenantModel->where('user_id', $userId)->first();
        if (!$tenant && $session->get('role') === 'tenant') {
            return redirect()->to('/maintenance')->with('error', 'No tenant profile associated with your user.');
        }

        $rules = [
            'property_id' => 'required|integer',
            'title'       => 'required|min_length[3]|max_length[255]',
            'description' => 'required',
            'attachment'  => 'permit_empty|max_size[attachment,4096]|ext_in[attachment,pdf,jpg,jpeg,png,webp]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('attachment');
        $fileName = null;
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/maintenance', $fileName);
        }

        $data = [
            'tenant_id'           => $session->get('role') === 'tenant' ? $tenant['id'] : $this->request->getPost('tenant_id'),
            'property_id'         => $this->request->getPost('property_id'),
            'title'               => $this->request->getPost('title'),
            'description'         => $this->request->getPost('description'),
            'status'              => 'Open',
            'assigned_technician' => null,
            'attachment_path'     => $fileName ? 'uploads/maintenance/' . $fileName : null,
        ];

        $this->maintenanceModel->insert($data);

        return redirect()->to('/maintenance')->with('success', 'Maintenance ticket logged successfully!');
    }

    public function assign($id)
    {
        $ticket = $this->maintenanceModel->find($id);
        if (!$ticket) {
            return redirect()->to('/maintenance')->with('error', 'Ticket not found.');
        }

        $technician = $this->request->getPost('assigned_technician');
        
        $this->maintenanceModel->update($id, [
            'assigned_technician' => $technician,
            'status'              => 'In Progress'
        ]);

        return redirect()->to('/maintenance')->with('success', 'Technician assigned successfully.');
    }

    public function updateStatus($id)
    {
        $ticket = $this->maintenanceModel->find($id);
        if (!$ticket) {
            return redirect()->to('/maintenance')->with('error', 'Ticket not found.');
        }

        $status = $this->request->getPost('status');
        
        $this->maintenanceModel->update($id, ['status' => $status]);

        return redirect()->to('/maintenance')->with('success', 'Ticket status updated to: ' . $status);
    }

    public function addComment($id)
    {
        $session = session();
        $userId = $session->get('id');

        $rules = [
            'comment' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Comment cannot be blank.']);
        }

        $commentData = [
            'ticket_id'  => $id,
            'user_id'    => $userId,
            'comment'    => $this->request->getPost('comment'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->commentModel->insert($commentData);

        // Fetch user info to render comment immediately in view
        $username = $session->get('username');
        $role = $session->get('role');

        return $this->response->setJSON([
            'status'     => 'success', 
            'comment'    => esc($commentData['comment']),
            'username'   => esc($username),
            'role'       => esc($role),
            'created_at' => date('d M h:i A')
        ]);
    }

    public function delete($id)
    {
        $ticket = $this->maintenanceModel->find($id);
        if (!$ticket) {
            return redirect()->to('/maintenance')->with('error', 'Ticket not found.');
        }

        if ($ticket['attachment_path'] && file_exists(FCPATH . $ticket['attachment_path'])) {
            @unlink(FCPATH . $ticket['attachment_path']);
        }

        $this->maintenanceModel->delete($id);

        return redirect()->to('/maintenance')->with('success', 'Maintenance ticket deleted successfully.');
    }
}
