<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Core\Database;
use App\Models\Document;
use App\Models\Deal;
use App\Models\AuditLog;
use App\Services\FileUploadService;
use App\Services\ExtractionService;

class DocumentController
{
    private $config;
    private $auth;
    private $view;
    private $db;

    public function __construct($config)
    {
        global $auth, $view, $db;
        $this->config = $config;
        $this->auth = $auth;
        $this->view = $view;
        $this->db = $db;
        
        $this->auth->requireAuth();
    }

    public function index($dealId)
    {
        $dealModel = new Deal($this->db);
        $documentModel = new Document($this->db);
        
        $deal = $dealModel->find($dealId);
        if (!$deal) {
            http_response_code(404);
            echo 'Deal not found';
            return;
        }

        $documents = $documentModel->getByDeal($dealId);
        $dealTypes = require __DIR__ . '/../../config/deal_types.php';
        $categories = $dealTypes[$deal['deal_type']]['document_categories'] ?? [];

        echo $this->view->render('documents.index', [
            'deal' => $deal,
            'documents' => $documents,
            'categories' => $categories,
            'csrf_token' => $this->auth->generateCsrfToken(),
        ]);
    }

    public function upload($dealId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view->redirect("/dashboard/deals/{$dealId}/documents");
            return;
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!$this->auth->validateCsrfToken($token)) {
            die('CSRF validation failed');
        }

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'File upload failed';
            $this->view->redirect("/dashboard/deals/{$dealId}/documents");
            return;
        }

        $uploadService = new FileUploadService($this->config);
        $documentModel = new Document($this->db);
        $auditLog = new AuditLog($this->db);

        $category = $_POST['category'] ?? 'other';
        $title = $_POST['title'] ?? $_FILES['file']['name'];
        $sourceType = $_POST['source_type'] ?? 'incoming';

        try {
            // Upload file
            $fileData = $uploadService->upload($_FILES['file'], $dealId, $category);
            
            // Create or find document
            $existingDocs = $documentModel->getByDeal($dealId);
            $documentId = null;
            
            foreach ($existingDocs as $doc) {
                if ($doc['category'] === $category && $doc['title'] === $title) {
                    $documentId = $doc['id'];
                    break;
                }
            }
            
            if (!$documentId) {
                $documentId = $documentModel->create([
                    'deal_id' => $dealId,
                    'category' => $category,
                    'title' => $title,
                    'source_type' => $sourceType,
                ]);
            }
            
            // Add version
            $versionId = $documentModel->addVersion($documentId, $fileData);
            
            // Extract text if PDF
            if ($fileData['mime_type'] === 'application/pdf') {
                $extractionService = new ExtractionService($this->config);
                $storagePath = __DIR__ . '/../../' . $fileData['stored_path'];
                $result = $extractionService->extractFromPdf($storagePath);
                
                if ($result['success'] && $result['text']) {
                    $documentModel->saveExtractedText($versionId, $result['text'], $result['method']);
                    
                    // Extract fields from LOI/Offer and auto-update deal
                    $fields = $extractionService->extractFieldsFromLOI($result['text']);
                    if (!empty($fields['expiry_date'])) {
                        $dealModel = new Deal($this->db);
                        $dealModel->update($dealId, [
                            'expiry_date' => $fields['expiry_date']
                        ]);
                        
                        // Log automatic expiry date detection
                        $auditLog->log($user['id'], 'expiry_date_auto_detected', 'deal', $dealId, [
                            'expiry_date' => $fields['expiry_date'],
                            'document_id' => $documentId,
                        ]);
                    }
                }
            }
            
            // Log upload
            $user = $this->auth->user();
            $auditLog->log($user['id'], 'document_uploaded', 'document', $documentId, [
                'category' => $category,
                'filename' => $fileData['original_filename'],
            ]);
            
            $_SESSION['success'] = 'Document uploaded successfully';
            
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        $this->view->redirect("/dashboard/deals/{$dealId}/documents");
    }

    public function download($id)
    {
        $documentModel = new Document($this->db);
        $document = $documentModel->find($id);
        
        if (!$document) {
            http_response_code(404);
            echo 'Document not found';
            return;
        }

        $version = $documentModel->getLatestVersion($id);
        if (!$version) {
            http_response_code(404);
            echo 'No versions found';
            return;
        }

        $filePath = __DIR__ . '/../../' . $version['stored_path'];
        if (!file_exists($filePath)) {
            http_response_code(404);
            echo 'File not found';
            return;
        }

        header('Content-Type: ' . $version['mime_type']);
        header('Content-Disposition: attachment; filename="' . $version['original_filename'] . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view->redirect('/dashboard/documents');
            return;
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!$this->auth->validateCsrfToken($token)) {
            die('CSRF validation failed');
        }

        $documentModel = new Document($this->db);
        $document = $documentModel->find($id);
        
        if (!$document) {
            $_SESSION['error'] = 'Dokument nicht gefunden';
            $this->view->redirect('/dashboard/documents');
            return;
        }

        // Dateien löschen
        $versions = $documentModel->getAllVersions($id);
        foreach ($versions as $version) {
            $filePath = __DIR__ . '/../../' . $version['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Datenbank: Versionen und Dokument werden durch CASCADE gelöscht
        $stmt = $this->db->query('DELETE FROM documents WHERE id = ?', [$id]);
        
        // Log
        $user = $this->auth->user();
        $auditLog = new AuditLog($this->db);
        $auditLog->log($user['id'], 'document_deleted', 'document', $id, [
            'title' => $document['title'],
            'category' => $document['category']
        ]);

        $_SESSION['success'] = 'Dokument erfolgreich gelöscht';
        
        // Zurück zur passenden Kategorie
        $categoryMap = [
            'loi_incoming' => '/dashboard/loi/incoming',
            'loi_outgoing' => '/dashboard/loi/outgoing',
            'offer_received' => '/dashboard/offers/received',
            'offer_sent' => '/dashboard/offers/sent',
        ];
        
        $redirect = $categoryMap[$document['category']] ?? '/dashboard/documents';
        $this->view->redirect($redirect);
    }

    public function preview($id)
    {
        $documentModel = new Document($this->db);
        $document = $documentModel->find($id);
        
        if (!$document) {
            http_response_code(404);
            echo 'Document not found';
            return;
        }

        $version = $documentModel->getLatestVersion($id);
        $extractedText = $documentModel->getExtractedText($version['id']);

        echo $this->view->render('documents.preview', [
            'document' => $document,
            'version' => $version,
            'extracted_text' => $extractedText,
            'csrf_token' => $this->auth->generateCsrfToken(),
        ]);
    }
    
    public function list()
    {
        $documentModel = new Document($this->db);
        $documents = $documentModel->all();
        
        echo $this->view->render('documents.list', [
            'title' => 'Alle Dokumente',
            'documents' => $documents
        ]);
    }
    
    public function loiIncoming()
    {
        $documentModel = new Document($this->db);
        $documents = $documentModel->getByCategory('loi_incoming');
        
        echo $this->view->render('documents.loi', [
            'title' => 'Angebote erhalten',
            'subtitle' => 'Von Käufern erhaltene LOIs',
            'documents' => $documents,
            'category' => 'incoming',
            'csrf_token' => $this->auth->generateCsrfToken(),
        ]);
    }
    
    public function loiOutgoing()
    {
        $documentModel = new Document($this->db);
        $documents = $documentModel->getByCategory('loi_outgoing');
        
        echo $this->view->render('documents.loi', [
            'title' => 'LOI Ausgang',
            'subtitle' => 'An Lieferanten gesendete Absichtserklärungen',
            'documents' => $documents,
            'category' => 'outgoing',
            'icon' => '📤',
            'csrf_token' => $this->auth->generateCsrfToken(),
        ]);
    }
    
    public function offersReceived()
    {
        $documentModel = new Document($this->db);
        $documents = $documentModel->getByCategory('offer_received');
        
        echo $this->view->render('documents.loi', [
            'title' => 'Angebote erhalten',
            'subtitle' => 'Von Lieferanten erhaltene konkrete Angebote',
            'documents' => $documents,
            'category' => 'offer_received',
            'icon' => '📩',
            'csrf_token' => $this->auth->generateCsrfToken(),
        ]);
    }
    
    public function offersSent()
    {
        $documentModel = new Document($this->db);
        $documents = $documentModel->getByCategory('offer_sent');
        
        echo $this->view->render('documents.loi', [
            'title' => 'Angebote gesendet',
            'subtitle' => 'An Käufer gesendete konkrete Angebote',
            'documents' => $documents,
            'category' => 'offer_sent',
            'icon' => '📨',
            'csrf_token' => $this->auth->generateCsrfToken(),
        ]);
    }
    
    public function uploadForm()
    {
        $dealModel = new Deal($this->db);
        $deals = $dealModel->all();
        
        echo $this->view->render('documents.upload', [
            'title' => 'Dokument hochladen',
            'deals' => $deals
        ]);
    }
    
    public function uploadStore()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view->redirect('/dashboard/documents/upload');
            return;
        }
        
        // Validierung
        if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Bitte wählen Sie eine Datei aus.';
            $this->view->redirect('/dashboard/documents/upload');
            return;
        }
        
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $category = $_POST['category'] ?? '';
        
        if (empty($title)) {
            $_SESSION['error'] = 'Bitte geben Sie einen Titel ein.';
            $this->view->redirect('/dashboard/documents/upload');
            return;
        }
        
        // Datei-Informationen
        $file = $_FILES['document'];
        $originalName = $file['name'];
        $tmpPath = $file['tmp_name'];
        $fileSize = $file['size'];
        $mimeType = mime_content_type($tmpPath);
        
        // Erlaubte Dateitypen
        $allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'image/jpeg',
            'image/png',
            'image/jpg'
        ];
        
        if (!in_array($mimeType, $allowedTypes)) {
            $_SESSION['error'] = 'Dateityp nicht erlaubt. Erlaubt sind: PDF, Word, Excel, JPG, PNG';
            $this->view->redirect('/dashboard/documents/upload');
            return;
        }
        
        // Max 10MB
        if ($fileSize > 10 * 1024 * 1024) {
            $_SESSION['error'] = 'Datei ist zu groß. Maximal 10 MB erlaubt.';
            $this->view->redirect('/dashboard/documents/upload');
            return;
        }
        
        // Speicherort erstellen
        $uploadsDir = dirname(__DIR__, 2) . '/storage/uploads';
        if (!file_exists($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }
        
        // Eindeutiger Dateiname
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filePath = $uploadsDir . '/' . $filename;
        
        // Datei verschieben
        if (!move_uploaded_file($tmpPath, $filePath)) {
            $_SESSION['error'] = 'Fehler beim Hochladen der Datei.';
            $this->view->redirect('/dashboard/documents/upload');
            return;
        }
        
        // In Datenbank speichern
        try {
            $user = $this->auth->user();
            $dealId = $_POST['deal_id'] ?? null;
            
            $documentModel = new Document($this->db);
            $uploadService = new FileUploadService($this->config);
            
            // Document erstellen
            $documentId = $documentModel->create([
                'deal_id' => $dealId,
                'title' => $title,
                'category' => $category,
                'source_type' => 'incoming'
            ]);
            
            // File-Daten für Version (SHA256 Hash berechnen)
            $sha256 = hash_file('sha256', $filePath);
            
            $fileData = [
                'file_path' => 'storage/uploads/' . $filename,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'sha256_hash' => $sha256,
            ];
            
            // Version hinzufügen
            $versionId = $documentModel->addVersion($documentId, $fileData);
            
            // PDF Text extrahieren und Ablaufdatum automatisch erkennen
            if ($mimeType === 'application/pdf' && $dealId) {
                $extractionService = new ExtractionService($this->config);
                $result = $extractionService->extractFromPdf($filePath);
                
                if ($result['success'] && $result['text']) {
                    // Text speichern
                    $documentModel->saveExtractedText($versionId, $result['text'], $result['method']);
                    
                    // Felder extrahieren
                    $fields = $extractionService->extractFieldsFromLOI($result['text']);
                    
                    // Automatisch Ablaufdatum setzen
                    if (!empty($fields['expiry_date'])) {
                        $dealModel = new Deal($this->db);
                        $dealModel->update($dealId, [
                            'expiry_date' => $fields['expiry_date']
                        ]);
                        
                        // Log
                        $auditLog = new AuditLog($this->db);
                        $auditLog->log($user['id'], 'expiry_date_auto_detected', 'deal', $dealId, [
                            'expiry_date' => $fields['expiry_date'],
                            'document_id' => $documentId,
                            'extracted_from' => $originalName
                        ]);
                        
                        $_SESSION['info'] = "Ablaufdatum automatisch erkannt: " . date('d.m.Y', strtotime($fields['expiry_date']));
                    }
                }
            }
            
            $_SESSION['success'] = 'Dokument erfolgreich hochgeladen!';
            $this->view->redirect('/dashboard/documents');
            
        } catch (\Exception $e) {
            // Bei Fehler: Datei löschen
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $_SESSION['error'] = 'Fehler beim Speichern: ' . $e->getMessage();
            $this->view->redirect('/dashboard/documents/upload');
        }
    }}