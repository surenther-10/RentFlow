<?php

namespace App\Models;

use CodeIgniter\Model;

class LeaseModel extends Model
{
    protected $table            = 'leases';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['agreement_number', 'property_id', 'tenant_id', 'start_date', 'end_date', 'security_deposit', 'monthly_rent', 'doc_path', 'status'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'agreement_number' => 'required|max_length[100]',
        'property_id'      => 'required|integer',
        'tenant_id'        => 'required|integer',
        'start_date'       => 'required|valid_date',
        'end_date'         => 'required|valid_date',
        'security_deposit' => 'required|numeric',
        'monthly_rent'     => 'required|numeric',
        'status'           => 'required|in_list[active,expired,terminated]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Fetch leases with join details
    public function getLeasesWithDetails($id = null)
    {
        $builder = $this->db->table($this->table)
            ->select('leases.*, properties.name as property_name, properties.address as property_address, properties.type as property_type, properties.city, properties.state, properties.pincode, tenants.name as tenant_name, tenants.mobile as tenant_mobile, tenants.email as tenant_email')
            ->join('properties', 'properties.id = leases.property_id')
            ->join('tenants', 'tenants.id = leases.tenant_id');

        if ($id !== null) {
            $builder->where('leases.id', $id);
            return $builder->get()->getRowArray();
        }

        return $builder->get()->getResultArray();
    }

    // Fetch lease details for a specific tenant user ID
    public function getLeasesByUserId($userId)
    {
        return $this->db->table($this->table)
            ->select('leases.*, properties.name as property_name, properties.address as property_address, properties.type as property_type, tenants.name as tenant_name')
            ->join('properties', 'properties.id = leases.property_id')
            ->join('tenants', 'tenants.id = leases.tenant_id')
            ->where('tenants.user_id', $userId)
            ->get()
            ->getResultArray();
    }
}
