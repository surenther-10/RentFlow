<?php

namespace App\Models;

use CodeIgniter\Model;

class PropertyModel extends Model
{
    protected $table            = 'properties';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'type', 'address', 'city', 'state', 'pincode', 'rent_amount', 'rooms', 'description', 'availability_status', 'image'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'name'                => 'required|min_length[3]|max_length[255]',
        'type'                => 'required',
        'address'             => 'required',
        'city'                => 'permit_empty|max_length[100]',
        'state'               => 'permit_empty|max_length[100]',
        'pincode'             => 'permit_empty|max_length[20]',
        'rent_amount'         => 'required|numeric',
        'rooms'               => 'required|integer',
        'description'         => 'permit_empty',
        'availability_status' => 'required|in_list[available,rented,maintenance]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
