<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Core\Database;
use App\Models\Deal;

class WorkflowController
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

    public function show($dealId)
    {
        $dealModel = new Deal($this->db);
        $deal = $dealModel->find($dealId);
        
        if (!$deal) {
            http_response_code(404);
            echo 'Deal not found';
            return;
        }

        // For MVP, we'll show a simple checklist
        // In production, this would load from workflow_templates
        $dealTypes = require __DIR__ . '/../../config/deal_types.php';
        $requiredDocs = $dealTypes[$deal['deal_type']]['document_categories'] ?? [];

        echo $this->view->render('workflow.show', [
            'deal' => $deal,
            'required_docs' => $requiredDocs,
            'csrf_token' => $this->auth->generateCsrfToken(),
        ]);
    }

    public function updateStep($dealId)
    {
        // Placeholder for workflow step updates
        $this->view->redirect("/dashboard/deals/{$dealId}/workflow");
    }
}
