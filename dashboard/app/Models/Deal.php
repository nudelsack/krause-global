<?php

namespace App\Models;

use App\Core\Database;

class Deal
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function all($archived = false)
    {
        // Note: SQLite schema doesn't have archived_at column, using status instead
        $sql = $archived 
            ? "SELECT * FROM deals WHERE status = 'archived' ORDER BY updated_at DESC"
            : "SELECT * FROM deals WHERE status != 'archived' ORDER BY updated_at DESC";
        
        return $this->db->fetchAll($sql);
    }

    public function find($id)
    {
        return $this->db->fetchOne('SELECT * FROM deals WHERE id = ?', [$id]);
    }

    public function findByCode($code)
    {
        return $this->db->fetchOne('SELECT * FROM deals WHERE deal_code = ?', [$code]);
    }

    public function create($data)
    {
        // Generate unique deal code
        if (!isset($data['deal_code'])) {
            $data['deal_code'] = $this->generateDealCode($data['deal_type']);
        }

        return $this->db->insert('deals', $data);
    }

    public function update($id, $data)
    {
        return $this->db->update('deals', $data, 'id = :id', ['id' => $id]);
    }

    public function archive($id)
    {
        return $this->db->update('deals', 
            ['archived_at' => date('Y-m-d H:i:s')],
            'id = :id',
            ['id' => $id]
        );
    }

    public function filter($filters)
    {
        $sql = 'SELECT * FROM deals WHERE archived_at IS NULL';
        $params = [];

        if (!empty($filters['deal_type'])) {
            $sql .= ' AND deal_type = :deal_type';
            $params['deal_type'] = $filters['deal_type'];
        }

        if (!empty($filters['status'])) {
            $sql .= ' AND status = :status';
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= ' AND (title LIKE :search OR deal_code LIKE :search OR reference_no LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $sql .= ' ORDER BY updated_at DESC';

        return $this->db->fetchAll($sql, $params);
    }

    public function getParties($dealId)
    {
        $sql = 'SELECT p.*, dp.role_in_deal 
                FROM parties p
                JOIN deal_parties dp ON p.id = dp.party_id
                WHERE dp.deal_id = ?
                ORDER BY dp.role_in_deal';
        
        return $this->db->fetchAll($sql, [$dealId]);
    }

    public function addParty($dealId, $partyId, $role)
    {
        return $this->db->insert('deal_parties', [
            'deal_id' => $dealId,
            'party_id' => $partyId,
            'role_in_deal' => $role,
        ]);
    }

    private function generateDealCode($dealType)
    {
        $prefix = strtoupper(substr($dealType, 0, 3));
        $year = date('Y');
        $random = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        
        return "{$prefix}-{$year}-{$random}";
    }

    public function getDocumentCount($dealId)
    {
        $result = $this->db->fetchOne(
            'SELECT COUNT(*) as count FROM documents WHERE deal_id = ?',
            [$dealId]
        );
        return (int)$result['count'];
    }
}
