<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table            = 'rent_payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['lease_id', 'amount', 'payment_date', 'payment_method', 'status', 'receipt_number', 'notes', 'doc_path'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'lease_id'       => 'required|integer',
        'amount'         => 'required|numeric',
        'payment_date'   => 'required|valid_date',
        'payment_method' => 'required',
        'status'         => 'required|in_list[Paid,Pending,Overdue]',
        'receipt_number' => 'required',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Fetch payments with details of property & tenant
    public function getPaymentsWithDetails($id = null)
    {
        $builder = $this->db->table($this->table)
            ->select('rent_payments.*, leases.agreement_number, properties.name as property_name, tenants.name as tenant_name, tenants.mobile as tenant_mobile, tenants.email as tenant_email')
            ->join('leases', 'leases.id = rent_payments.lease_id')
            ->join('properties', 'properties.id = leases.property_id')
            ->join('tenants', 'tenants.id = leases.tenant_id');

        if ($id !== null) {
            $builder->where('rent_payments.id', $id);
            return $builder->get()->getRowArray();
        }

        $builder->orderBy('rent_payments.payment_date', 'DESC');
        return $builder->get()->getResultArray();
    }

    // Fetch payments for a specific tenant user ID
    public function getPaymentsByUserId($userId)
    {
        return $this->db->table($this->table)
            ->select('rent_payments.*, leases.agreement_number, properties.name as property_name, tenants.name as tenant_name')
            ->join('leases', 'leases.id = rent_payments.lease_id')
            ->join('properties', 'properties.id = leases.property_id')
            ->join('tenants', 'tenants.id = leases.tenant_id')
            ->where('tenants.user_id', $userId)
            ->orderBy('rent_payments.payment_date', 'DESC')
            ->get()
            ->getResultArray();
    }
}
