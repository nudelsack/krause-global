<?php

namespace App\Models;

use App\Core\Database;

class AuditLog
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function log($userId, $action, $objectType, $objectId, $payload = [])
    {
        return $this->db->insert('audit_log', [
            'actor_user_id' => $userId,
            'action' => $action,
            'object_type' => $objectType,
            'object_id' => $objectId,
            'payload_json' => json_encode($payload),
        ]);
    }

    public function getByObject($objectType, $objectId, $limit = 50)
    {
        $sql = 'SELECT al.*, u.username
                FROM audit_log al
                LEFT JOIN users u ON al.actor_user_id = u.id
                WHERE al.object_type = ? AND al.object_id = ?
                ORDER BY al.created_at DESC
                LIMIT ?';
        
        return $this->db->fetchAll($sql, [$objectType, $objectId, $limit]);
    }

    public function getByDeal($dealId, $limit = 50)
    {
        return $this->getByObject('deal', $dealId, $limit);
    }
}
