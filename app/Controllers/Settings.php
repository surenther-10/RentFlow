<?php

namespace App\Controllers;

class Settings extends BaseController
{
    public function index()
    {
        $session = session();
        if ($session->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Permission Denied');
        }

        $db = \Config\Database::connect();
        $settings = $db->table('settings')->get()->getResultArray();
        
        $data['settings'] = [];
        foreach ($settings as $s) {
            $data['settings'][$s['key']] = $s['value'];
        }

        return view('settings/index', $data);
    }

    public function update()
    {
        $session = session();
        if ($session->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Permission Denied');
        }

        $db = \Config\Database::connect();
        
        $postData = $this->request->getPost();
        unset($postData[csrf_token()]); // remove token

        $db->transStart();
        foreach ($postData as $key => $value) {
            // Check if key exists
            $exists = $db->table('settings')->where('key', $key)->get()->getRow();
            if ($exists) {
                $db->table('settings')->where('key', $key)->update([
                    'value'      => $value,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                $db->table('settings')->insert([
                    'key'        => $key,
                    'value'      => $value,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
        $db->transComplete();

        return redirect()->to('/admin/settings')->with('success', 'Application settings updated successfully!');
    }
}
