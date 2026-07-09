<?php

namespace App\Models;

use CodeIgniter\Model;

class MaintenanceModel extends Model
{
    protected $table            = 'maintenance_tickets';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['tenant_id', 'property_id', 'title', 'description', 'status', 'assigned_technician', 'attachment_path'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'tenant_id'   => 'required|integer',
        'property_id' => 'required|integer',
        'title'       => 'required|min_length[3]|max_length[255]',
        'description' => 'required',
        'status'      => 'required|in_list[Open,In Progress,Completed,Closed]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Fetch tickets with detail joins
    public function getTicketsWithDetails($id = null)
    {
        $builder = $this->db->table($this->table)
            ->select('maintenance_tickets.*, properties.name as property_name, tenants.name as tenant_name, tenants.mobile as tenant_mobile')
            ->join('properties', 'properties.id = maintenance_tickets.property_id')
            ->join('tenants', 'tenants.id = maintenance_tickets.tenant_id');

        if ($id !== null) {
            $builder->where('maintenance_tickets.id', $id);
            return $builder->get()->getRowArray();
        }

        $builder->orderBy('maintenance_tickets.created_at', 'DESC');
        return $builder->get()->getResultArray();
    }

    // Fetch tickets for a specific tenant user ID
    public function getTicketsByUserId($userId)
    {
        return $this->db->table($this->table)
            ->select('maintenance_tickets.*, properties.name as property_name, tenants.name as tenant_name')
            ->join('properties', 'properties.id = maintenance_tickets.property_id')
            ->join('tenants', 'tenants.id = maintenance_tickets.tenant_id')
            ->where('tenants.user_id', $userId)
            ->orderBy('maintenance_tickets.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }
}
