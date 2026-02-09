<?php

namespace App\Models;

use App\Core\Database;

class Document
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getByDeal($dealId)
    {
        $sql = 'SELECT d.*, 
                       (SELECT COUNT(*) FROM document_versions WHERE document_id = d.id) as version_count,
                       (SELECT uploaded_at FROM document_versions WHERE document_id = d.id ORDER BY version_no DESC LIMIT 1) as last_uploaded
                FROM documents d
                WHERE d.deal_id = ?
                ORDER BY d.created_at DESC';
        
        return $this->db->fetchAll($sql, [$dealId]);
    }

    public function find($id)
    {
        return $this->db->fetchOne('SELECT * FROM documents WHERE id = ?', [$id]);
    }

    public function create($data)
    {
        return $this->db->insert('documents', $data);
    }

    public function getLatestVersion($documentId)
    {
        $sql = 'SELECT * FROM document_versions 
                WHERE document_id = ? 
                ORDER BY version_no DESC 
                LIMIT 1';
        
        return $this->db->fetchOne($sql, [$documentId]);
    }

    public function getAllVersions($documentId)
    {
        $sql = 'SELECT * FROM document_versions 
                WHERE document_id = ? 
                ORDER BY version_no DESC';
        
        return $this->db->fetchAll($sql, [$documentId]);
    }

    public function addVersion($documentId, $data)
    {
        // Get next version number
        $currentVersion = $this->getLatestVersion($documentId);
        $versionNo = $currentVersion ? $currentVersion['version_no'] + 1 : 1;
        
        $data['document_id'] = $documentId;
        $data['version_no'] = $versionNo;
        
        return $this->db->insert('document_versions', $data);
    }

    public function getExtractedText($versionId)
    {
        return $this->db->fetchOne(
            'SELECT * FROM extracted_texts WHERE document_version_id = ?',
            [$versionId]
        );
    }

    public function saveExtractedText($versionId, $text, $method)
    {
        $existing = $this->getExtractedText($versionId);
        
        if ($existing) {
            return $this->db->update('extracted_texts',
                [
                    'extracted_text' => $text,
                    'extraction_method' => $method,
                    'extracted_at' => date('Y-m-d H:i:s'),
                ],
                'document_version_id = :id',
                ['id' => $versionId]
            );
        } else {
            return $this->db->insert('extracted_texts', [
                'document_version_id' => $versionId,
                'extracted_text' => $text,
                'extraction_method' => $method,
            ]);
        }
    }

    public function searchDocuments($query, $dealType = null, $category = null)
    {
        $sql = 'SELECT d.*, deals.title as deal_title, deals.deal_code
                FROM documents d
                JOIN deals ON d.deal_id = deals.id
                LEFT JOIN document_versions dv ON d.id = dv.document_id
                LEFT JOIN extracted_texts et ON dv.id = et.document_version_id
                WHERE deals.archived_at IS NULL
                AND (MATCH(et.extracted_text) AGAINST(:query IN NATURAL LANGUAGE MODE)
                     OR MATCH(et.manual_text) AGAINST(:query IN NATURAL LANGUAGE MODE)
                     OR d.title LIKE :like_query
                     OR deals.deal_code LIKE :like_query)';
        
        $params = [
            'query' => $query,
            'like_query' => '%' . $query . '%',
        ];

        if ($dealType) {
            $sql .= ' AND deals.deal_type = :deal_type';
            $params['deal_type'] = $dealType;
        }

        if ($category) {
            $sql .= ' AND d.category = :category';
            $params['category'] = $category;
        }

        $sql .= ' GROUP BY d.id ORDER BY d.created_at DESC LIMIT 100';

        return $this->db->fetchAll($sql, $params);
    }
    
    public function all()
    {
        $sql = 'SELECT d.*, 
                       (SELECT COUNT(*) FROM document_versions WHERE document_id = d.id) as version_count,
                       (SELECT uploaded_at FROM document_versions WHERE document_id = d.id ORDER BY version_no DESC LIMIT 1) as last_uploaded
                FROM documents d
                ORDER BY d.created_at DESC';
        
        return $this->db->fetchAll($sql);
    }
    
    public function getByCategory($category)
    {
        $sql = 'SELECT d.*, 
                       (SELECT COUNT(*) FROM document_versions WHERE document_id = d.id) as version_count,
                       (SELECT uploaded_at FROM document_versions WHERE document_id = d.id ORDER BY version_no DESC LIMIT 1) as last_uploaded
                FROM documents d
                WHERE d.category = ?
                ORDER BY d.created_at DESC';
        
        return $this->db->fetchAll($sql, [$category]);
    }
}
