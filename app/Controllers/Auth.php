<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\TenantModel;
use App\Models\OwnerModel;

class Auth extends BaseController
{
    public function login()
    {
        $session = session();
        helper('cookie');
        if (!$session->get('isLoggedIn')) {
            $rememberCookie = get_cookie('remember_token');
            if ($rememberCookie) {
                $parts = explode(':', $rememberCookie);
                if (count($parts) === 2) {
                    list($userId, $cookieHash) = $parts;
                    $userModel = new UserModel();
                    $user = $userModel->find($userId);
                    if ($user && md5($user['password']) === $cookieHash && $user['status'] === 'active') {
                        $ses_data = [
                            'id'            => $user['id'],
                            'username'      => $user['username'],
                            'email'         => $user['email'],
                            'role'          => $user['role'],
                            'profile_photo' => $user['profile_photo'],
                            'isLoggedIn'    => true
                        ];
                        $session->set($ses_data);
                        return redirect()->to('/dashboard')->with('success', 'Welcome back, ' . $user['username'] . '!');
                    }
                }
            }
        } else {
            return redirect()->to('/dashboard');
        }
        return view('auth/login');
    }

    public function attemptLogin()
    {
        $session = session();
        $model = new UserModel();

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember');

        $user = $model->where('username', $username)
                      ->orWhere('email', $username)
                      ->first();

        if ($user) {
            if (password_verify($password, $user['password'])) {
                if ($user['status'] !== 'active') {
                    return redirect()->back()->withInput()->with('error', 'Your account is inactive. Please contact the administrator.');
                }

                $ses_data = [
                    'id'            => $user['id'],
                    'username'      => $user['username'],
                    'email'         => $user['email'],
                    'role'          => $user['role'],
                    'profile_photo' => $user['profile_photo'],
                    'isLoggedIn'    => true
                ];
                $session->set($ses_data);

                if ($remember) {
                    helper('cookie');
                    // Store cookie for 30 days
                    set_cookie('remember_token', $user['id'] . ':' . md5($user['password']), 3600 * 24 * 30);
                }

                return redirect()->to('/dashboard')->with('success', 'Logged in successfully! Welcome back, ' . $user['username'] . '.');
            } else {
                return redirect()->back()->withInput()->with('error', 'Invalid password.');
            }
        } else {
            return redirect()->back()->withInput()->with('error', 'Username or Email not found.');
        }
    }

    public function register()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/register');
    }

    public function attemptRegister()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'username'      => 'required|alpha_numeric_space|min_length[3]|max_length[100]|is_unique[users.username]',
            'email'         => 'required|valid_email|max_length[100]|is_unique[users.email]',
            'password'      => 'required|min_length[6]',
            'role'          => 'required|in_list[owner,tenant]',
            'name'          => 'required|min_length[3]|max_length[255]',
            'mobile'        => 'required|min_length[10]|max_length[20]',
            'address'       => 'required',
            'profile_photo' => 'permit_empty|is_image[profile_photo]|max_size[profile_photo,5120]|ext_in[profile_photo,jpg,jpeg,png,webp]',
            'doc'           => 'permit_empty|max_size[doc,4096]|ext_in[doc,pdf,jpg,jpeg,png]',
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
        }

        $userModel = new UserModel();
        $tenantModel = new TenantModel();
        $ownerModel = new OwnerModel();

        // Start database transaction
        $db = \Config\Database::connect();
        $db->transStart();

        // Create user record
        $userData = [
            'username'      => $this->request->getPost('username'),
            'email'         => $this->request->getPost('email'),
            'password'      => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'role'          => $this->request->getPost('role'),
            'role_id'       => $this->request->getPost('role') === 'owner' ? 2 : 3,
            'status'        => 'active',
            'profile_photo' => $photoPath
        ];
        
        $userModel->insert($userData);
        $userId = $userModel->getInsertID();

        if ($userData['role'] === 'tenant') {
            if ($docName) {
                $docFile->move(FCPATH . 'uploads/documents', $docName);
            }

            $tenantData = [
                'user_id'        => $userId,
                'name'           => $this->request->getPost('name'),
                'mobile'         => $this->request->getPost('mobile'),
                'email'          => $userData['email'],
                'address'        => $this->request->getPost('address'),
                'profile_photo'  => null,
                'doc_path'       => $docName ? 'uploads/documents/' . $docName : null,
            ];
            $tenantModel->insert($tenantData);
        } else if ($userData['role'] === 'owner') {
            if ($docName) {
                $docFile->move(FCPATH . 'uploads/owners/docs', $docName);
            }

            $ownerData = [
                'user_id'        => $userId,
                'name'           => $this->request->getPost('name'),
                'mobile'         => $this->request->getPost('mobile'),
                'email'          => $userData['email'],
                'address'        => $this->request->getPost('address'),
                'profile_photo'  => null,
                'doc_path'       => $docName ? 'uploads/owners/docs/' . $docName : null,
            ];
            $ownerModel->insert($ownerData);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Registration failed. Please try again.');
        }

        return redirect()->to('/login')->with('success', 'Registration successful! You can now log in.');
    }

    public function logout()
    {
        helper('cookie');
        delete_cookie('remember_token');
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Logged out successfully.');
    }

    public function forgotPassword()
    {
        return view('auth/forgot_password');
    }

    public function attemptForgotPassword()
    {
        $email = $this->request->getPost('email');
        $model = new UserModel();
        $user = $model->where('email', $email)->first();

        if ($user) {
            // In a real system, send email. Here, we'll simulate it.
            return redirect()->back()->with('success', 'A password reset link has been simulated & sent to your email address.');
        } else {
            return redirect()->back()->with('error', 'Email address not found.');
        }
    }

    public function changePassword()
    {
        return view('auth/change_password');
    }

    public function attemptChangePassword()
    {
        $session = session();
        $model = new UserModel();

        $oldPassword = $this->request->getPost('old_password');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_new_password');

        if ($newPassword !== $confirmPassword) {
            return redirect()->back()->with('error', 'New passwords do not match.');
        }

        $user = $model->find($session->get('id'));

        if (password_verify($oldPassword, $user['password'])) {
            $model->update($session->get('id'), [
                'password' => password_hash($newPassword, PASSWORD_BCRYPT)
            ]);
            return redirect()->to('/dashboard')->with('success', 'Password updated successfully!');
        } else {
            return redirect()->back()->with('error', 'Incorrect current password.');
        }
    }

    public function profile()
    {
        $session = session();
        $userId = $session->get('id');
        $role = $session->get('role');

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        $profile = null;
        if ($role === 'tenant') {
            $tenantModel = new TenantModel();
            $profile = $tenantModel->where('user_id', $userId)->first();
        } else if ($role === 'owner') {
            $ownerModel = new OwnerModel();
            $profile = $ownerModel->where('user_id', $userId)->first();
        }

        $data = [
            'user'    => $user,
            'profile' => $profile,
            'role'    => $role
        ];

        return view('auth/profile', $data);
    }

    public function attemptUpdateProfile()
    {
        $session = session();
        $userId = $session->get('id');
        $role = $session->get('role');

        $userModel = new UserModel();

        if ($role === 'admin') {
            $rules = [
                'username'      => "required|alpha_numeric_space|min_length[3]|max_length[100]|is_unique[users.username,id,{$userId}]",
                'email'         => "required|valid_email|max_length[100]|is_unique[users.email,id,{$userId}]",
                'profile_photo' => 'permit_empty|is_image[profile_photo]|max_size[profile_photo,5120]|ext_in[profile_photo,jpg,jpeg,png,webp]',
            ];
        } else {
            $rules = [
                'username'      => "required|alpha_numeric_space|min_length[3]|max_length[100]|is_unique[users.username,id,{$userId}]",
                'email'         => "required|valid_email|max_length[100]|is_unique[users.email,id,{$userId}]",
                'name'          => 'required|min_length[3]|max_length[255]',
                'mobile'        => 'required|min_length[10]|max_length[20]',
                'address'       => 'required',
                'profile_photo' => 'permit_empty|is_image[profile_photo]|max_size[profile_photo,5120]|ext_in[profile_photo,jpg,jpeg,png,webp]',
                'doc'           => 'permit_empty|max_size[doc,4096]|ext_in[doc,pdf,jpg,jpeg,png]',
            ];
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $photoFile = $this->request->getFile('profile_photo');
        $user = $userModel->find($userId);
        $photoPath = $user['profile_photo'] ?? null;
        if ($photoFile && $photoFile->isValid() && !$photoFile->hasMoved()) {
            if (!empty($user['profile_photo']) && file_exists(FCPATH . $user['profile_photo'])) {
                @unlink(FCPATH . $user['profile_photo']);
            }
            $newName = $photoFile->getRandomName();
            $photoFile->move(FCPATH . 'uploads/profile', $newName);
            $photoPath = 'uploads/profile/' . $newName;
            $session->set('profile_photo', $photoPath);
        }

        $userModel->update($userId, [
            'username'      => $this->request->getPost('username'),
            'email'         => $this->request->getPost('email'),
            'profile_photo' => $photoPath
        ]);

        $session->set('username', $this->request->getPost('username'));
        $session->set('email', $this->request->getPost('email'));

        if ($role === 'tenant') {
            $tenantModel = new TenantModel();
            $profile = $tenantModel->where('user_id', $userId)->first();
            
            $docFile = $this->request->getFile('doc');
            $docName = $profile['doc_path'] ?? null;
            if ($docFile && $docFile->isValid() && !$docFile->hasMoved()) {
                if ($profile['doc_path'] && file_exists(FCPATH . $profile['doc_path'])) {
                    @unlink(FCPATH . $profile['doc_path']);
                }
                $docName = 'uploads/documents/' . $docFile->getRandomName();
                $docFile->move(FCPATH . 'uploads/documents', basename($docName));
            }

            $tenantData = [
                'name'          => $this->request->getPost('name'),
                'mobile'        => $this->request->getPost('mobile'),
                'email'         => $this->request->getPost('email'),
                'address'       => $this->request->getPost('address'),
                'profile_photo' => null,
                'doc_path'      => $docName,
            ];

            if ($profile) {
                $tenantModel->update($profile['id'], $tenantData);
            } else {
                $tenantData['user_id'] = $userId;
                $tenantModel->insert($tenantData);
            }

        } else if ($role === 'owner') {
            $ownerModel = new OwnerModel();
            $profile = $ownerModel->where('user_id', $userId)->first();

            $docFile = $this->request->getFile('doc');
            $docName = $profile['doc_path'] ?? null;
            if ($docFile && $docFile->isValid() && !$docFile->hasMoved()) {
                if ($profile['doc_path'] && file_exists(FCPATH . $profile['doc_path'])) {
                    @unlink(FCPATH . $profile['doc_path']);
                }
                $docName = 'uploads/owners/docs/' . $docFile->getRandomName();
                $docFile->move(FCPATH . 'uploads/owners/docs', basename($docName));
            }

            $ownerData = [
                'name'          => $this->request->getPost('name'),
                'mobile'        => $this->request->getPost('mobile'),
                'email'         => $this->request->getPost('email'),
                'address'       => $this->request->getPost('address'),
                'profile_photo' => null,
                'doc_path'      => $docName,
            ];

            if ($profile) {
                $ownerModel->update($profile['id'], $ownerData);
            } else {
                $ownerData['user_id'] = $userId;
                $ownerModel->insert($ownerData);
            }
        }

        $db->transComplete();

        return redirect()->to('/profile')->with('success', 'Profile updated successfully!');
    }
}
