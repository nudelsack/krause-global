<?php

namespace App\Models;

use App\Core\Database;

class Party
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function all()
    {
        return $this->db->fetchAll('SELECT * FROM parties ORDER BY company_name ASC');
    }

    public function find($id)
    {
        return $this->db->fetchOne('SELECT * FROM parties WHERE id = ?', [$id]);
    }

    public function create($data)
    {
        return $this->db->insert('parties', $data);
    }

    public function update($id, $data)
    {
        return $this->db->update('parties', $data, 'id = :id', ['id' => $id]);
    }

    public function getContacts($partyId)
    {
        return $this->db->fetchAll(
            'SELECT * FROM contacts WHERE party_id = ? ORDER BY full_name ASC',
            [$partyId]
        );
    }

    public function addContact($partyId, $data)
    {
        $data['party_id'] = $partyId;
        return $this->db->insert('contacts', $data);
    }

    public function search($term)
    {
        $sql = 'SELECT * FROM parties 
                WHERE company_name LIKE :term 
                   OR country LIKE :term
                ORDER BY company_name ASC
                LIMIT 20';
        
        return $this->db->fetchAll($sql, ['term' => '%' . $term . '%']);
    }
}
