<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'title', 'message', 'is_read', 'type'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Helper to get unread notifications for a user
    public function getUnread($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    // Helper to create notification
    public function send($userId, $title, $message, $type = 'system')
    {
        return $this->insert([
            'user_id' => $userId,
            'title'   => $title,
            'message' => $message,
            'is_read' => 0,
            'type'    => $type
        ]);
    }
}
