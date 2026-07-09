<?php

namespace App\Models;

use CodeIgniter\Model;

class TenantModel extends Model
{
    protected $table            = 'tenants';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'name', 'mobile', 'email', 'aadhaar_number', 'pan_number', 'address', 'profile_photo', 'doc_path'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'name'   => 'required|min_length[3]|max_length[255]',
        'mobile' => 'required|min_length[10]|max_length[20]',
        'email'  => 'required|valid_email|max_length[100]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
