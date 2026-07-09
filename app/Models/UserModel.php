<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['username', 'email', 'password', 'role', 'role_id', 'status', 'profile_photo'];

    public function __construct()
    {
        parent::__construct();
        $this->ensureDefaultAdmin();
    }

    protected function ensureDefaultAdmin()
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);
        $admin = $builder->where('username', 'admin')
                         ->orWhere('role', 'admin')
                         ->get()
                         ->getRowArray();
        if (!$admin) {
            $builder->insert([
                'username'   => 'admin',
                'email'      => 'admin@rentflow.com',
                'password'   => password_hash('password123', PASSWORD_BCRYPT),
                'role'       => 'admin',
                'role_id'    => 1,
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
