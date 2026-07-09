<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;

class Users extends BaseController
{
    protected $userModel;
    protected $roleModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
    }

    public function index()
    {
        $session = session();
        if ($session->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Permission Denied');
        }

        // Get users joined with roles
        $db = \Config\Database::connect();
        $data['users'] = $db->table('users')
            ->select('users.*, roles.name as role_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->get()
            ->getResultArray();

        $data['roles'] = $this->roleModel->findAll();

        return view('users/index', $data);
    }

    public function details($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not found']);
        }
        unset($user['password']); // Safety first!
        return $this->response->setJSON(['status' => 'success', 'data' => $user]);
    }

    public function store()
    {
        $rules = [
            'username' => 'required|alpha_numeric_space|min_length[3]|max_length[100]|is_unique[users.username]',
            'email'    => 'required|valid_email|max_length[100]|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'role_id'  => 'required|integer',
            'status'   => 'required|in_list[active,inactive]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get Role name
        $role = $this->roleModel->find($this->request->getPost('role_id'));
        if (!$role) {
            return redirect()->back()->withInput()->with('error', 'Invalid role selected.');
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'role'     => $role['name'],
            'role_id'  => $role['id'],
            'status'   => $this->request->getPost('status')
        ];

        if (!$this->userModel->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors() ?: ['Failed to register user.']);
        }

        return redirect()->to('/admin/users')->with('success', 'User account registered successfully!');
    }

    public function update($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found.');
        }

        $rules = [
            'username' => "required|alpha_numeric_space|min_length[3]|max_length[100]|is_unique[users.username,id,{$id}]",
            'email'    => "required|valid_email|max_length[100]|is_unique[users.email,id,{$id}]",
            'role_id'  => 'required|integer',
            'status'   => 'required|in_list[active,inactive]',
            'password' => 'permit_empty|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $role = $this->roleModel->find($this->request->getPost('role_id'));
        if (!$role) {
            return redirect()->back()->withInput()->with('error', 'Invalid role selected.');
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'role'     => $role['name'],
            'role_id'  => $role['id'],
            'status'   => $this->request->getPost('status')
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        if (!$this->userModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors() ?: ['Failed to update user account.']);
        }

        return redirect()->to('/admin/users')->with('success', 'User account updated successfully!');
    }

    public function delete($id)
    {
        // Don't let admin delete their own account
        if ($id == session()->get('id')) {
            return redirect()->to('/admin/users')->with('error', 'You cannot delete your own account.');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        if ($user['role'] === 'tenant') {
            $tenantModel = new \App\Models\TenantModel();
            $tenantModel->where('user_id', $id)->delete();
        }

        $this->userModel->delete($id);

        $db->transComplete();

        return redirect()->to('/admin/users')->with('success', 'User account deleted successfully.');
    }
}
