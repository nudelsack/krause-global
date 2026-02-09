<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Core\Database;
use App\Models\Deal;
use App\Models\Document;
use App\Services\ExportService;
use App\Models\AuditLog;

class ExportController
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

    public function exportDossier($dealId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view->redirect("/dashboard/deals/{$dealId}");
            return;
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!$this->auth->validateCsrfToken($token)) {
            die('CSRF validation failed');
        }

        $dealModel = new Deal($this->db);
        $documentModel = new Document($this->db);
        $auditLog = new AuditLog($this->db);
        
        $exportService = new ExportService($this->config, $dealModel, $documentModel);

        try {
            $zipFilename = $exportService->exportDealDossier($dealId);
            
            // Log export
            $user = $this->auth->user();
            $auditLog->log($user['id'], 'deal_exported', 'deal', $dealId, [
                'filename' => $zipFilename,
            ]);
            
            // Download the file
            $exportPath = $this->getAbsolutePath($this->config['paths']['exports']);
            $filePath = $exportPath . '/' . $zipFilename;
            
            if (file_exists($filePath)) {
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . $zipFilename . '"');
                header('Content-Length: ' . filesize($filePath));
                readfile($filePath);
                
                // Optional: Delete file after download
                // unlink($filePath);
                
                exit;
            }
            
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Export failed: ' . $e->getMessage();
            $this->view->redirect("/dashboard/deals/{$dealId}");
        }
    }

    private function getAbsolutePath($path)
    {
        if ($path[0] === '/') {
            return $path;
        }
        return __DIR__ . '/../../' . $path;
    }
}
