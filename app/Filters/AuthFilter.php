<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        helper('cookie');

        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            $rememberCookie = get_cookie('remember_token');
            if ($rememberCookie) {
                $parts = explode(':', $rememberCookie);
                if (count($parts) === 2) {
                    list($userId, $cookieHash) = $parts;
                    $userModel = new \App\Models\UserModel();
                    $user = $userModel->find($userId);
                    if ($user && md5($user['password']) === $cookieHash && $user['status'] === 'active') {
                        $ses_data = [
                            'id'         => $user['id'],
                            'username'   => $user['username'],
                            'email'      => $user['email'],
                            'role'       => $user['role'],
                            'isLoggedIn' => true
                        ];
                        $session->set($ses_data);
                        return; // proceed to route
                    }
                }
            }
            return redirect()->to('/login')->with('error', 'You must be logged in to access this page.');
        }

        if ($arguments) {
            $userRole = $session->get('role');
            if (!in_array($userRole, $arguments)) {
                return redirect()->to('/dashboard')->with('error', 'Permission Denied');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing here
    }
}
