<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table            = 'maintenance_comments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['ticket_id', 'user_id', 'comment', 'created_at'];

    // Validation
    protected $validationRules      = [
        'ticket_id' => 'required|integer',
        'user_id'   => 'required|integer',
        'comment'   => 'required',
    ];

    // Helper to get ticket comments with user details
    public function getCommentsByTicket($ticketId)
    {
        return $this->db->table($this->table)
            ->select('maintenance_comments.*, users.username, users.role')
            ->join('users', 'users.id = maintenance_comments.user_id')
            ->where('maintenance_comments.ticket_id', $ticketId)
            ->orderBy('maintenance_comments.created_at', 'ASC')
            ->get()
            ->getResultArray();
    }
}
