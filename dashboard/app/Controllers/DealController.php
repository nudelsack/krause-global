<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Core\Database;
use App\Models\Deal;
use App\Models\Party;
use App\Models\AuditLog;

class DealController
{
    private $config;
    private $auth;
    private $view;
    private $db;
    private $dealTypes;

    public function __construct($config)
    {
        global $auth, $view, $db;
        $this->config = $config;
        $this->auth = $auth;
        $this->view = $view;
        $this->db = $db;
        $this->dealTypes = require __DIR__ . '/../../config/deal_types.php';
        
        $this->auth->requireAuth();
    }

    public function index()
    {
        $dealModel = new Deal($this->db);
        
        $filters = [
            'deal_type' => $_GET['deal_type'] ?? '',
            'status' => $_GET['status'] ?? '',
            'search' => $_GET['search'] ?? '',
        ];

        $deals = empty($filters['deal_type']) && empty($filters['status']) && empty($filters['search'])
            ? $dealModel->all()
            : $dealModel->filter($filters);

        echo $this->view->render('deals.index', [
            'deals' => $deals,
            'filters' => $filters,
            'deal_types' => $this->dealTypes,
            'csrf_token' => $this->auth->generateCsrfToken(),
        ]);
    }

    public function create()
    {
        echo $this->view->render('deals.create', [
            'deal_types' => $this->dealTypes,
            'csrf_token' => $this->auth->generateCsrfToken(),
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view->redirect('/dashboard/deals');
            return;
        }

        // Validate CSRF
        $token = $_POST['csrf_token'] ?? '';
        if (!$this->auth->validateCsrfToken($token)) {
            die('CSRF validation failed');
        }

        $dealModel = new Deal($this->db);
        $auditLog = new AuditLog($this->db);

        $data = [
            'title' => $_POST['title'] ?? '',
            'deal_type' => $_POST['deal_type'] ?? '',
            'deal_subtype' => $_POST['deal_subtype'] ?? null,
            'status' => $_POST['status'] ?? 'inquiry',
            'incoterms' => $_POST['incoterms'] ?? null,
            'origin' => $_POST['origin'] ?? null,
            'destination' => $_POST['destination'] ?? null,
            'quantity' => $_POST['quantity'] ?? null,
            'quantity_unit' => $_POST['quantity_unit'] ?? null,
            'price' => $_POST['price'] ?? null,
            'currency' => $_POST['currency'] ?? 'USD',
            'reference_no' => $_POST['reference_no'] ?? null,
            'notes' => $_POST['notes'] ?? null,
        ];

        $dealId = $dealModel->create($data);
        
        // Log creation
        $user = $this->auth->user();
        $auditLog->log($user['id'], 'deal_created', 'deal', $dealId, $data);

        $this->view->redirect('/dashboard/deals/' . $dealId);
    }

    public function show($id)
    {
        $dealModel = new Deal($this->db);
        $deal = $dealModel->find($id);
        
        if (!$deal) {
            http_response_code(404);
            echo 'Deal not found';
            return;
        }

        $parties = $dealModel->getParties($id);
        $documentCount = $dealModel->getDocumentCount($id);
        $auditLog = new AuditLog($this->db);
        $recentActivity = $auditLog->getByDeal($id, 10);

        echo $this->view->render('deals.show', [
            'deal' => $deal,
            'parties' => $parties,
            'document_count' => $documentCount,
            'recent_activity' => $recentActivity,
            'deal_types' => $this->dealTypes,
            'csrf_token' => $this->auth->generateCsrfToken(),
        ]);
    }

    public function edit($id)
    {
        $dealModel = new Deal($this->db);
        $deal = $dealModel->find($id);
        
        if (!$deal) {
            http_response_code(404);
            echo 'Deal not found';
            return;
        }

        echo $this->view->render('deals.edit', [
            'deal' => $deal,
            'deal_types' => $this->dealTypes,
            'csrf_token' => $this->auth->generateCsrfToken(),
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view->redirect('/dashboard/deals/' . $id);
            return;
        }

        // Validate CSRF
        $token = $_POST['csrf_token'] ?? '';
        if (!$this->auth->validateCsrfToken($token)) {
            die('CSRF validation failed');
        }

        $dealModel = new Deal($this->db);
        $auditLog = new AuditLog($this->db);

        $data = [
            'title' => $_POST['title'] ?? '',
            'status' => $_POST['status'] ?? '',
            'incoterms' => $_POST['incoterms'] ?? null,
            'origin' => $_POST['origin'] ?? null,
            'destination' => $_POST['destination'] ?? null,
            'quantity' => $_POST['quantity'] ?? null,
            'quantity_unit' => $_POST['quantity_unit'] ?? null,
            'price' => $_POST['price'] ?? null,
            'currency' => $_POST['currency'] ?? 'USD',
            'reference_no' => $_POST['reference_no'] ?? null,
            'notes' => $_POST['notes'] ?? null,
        ];

        $dealModel->update($id, $data);
        
        // Log update
        $user = $this->auth->user();
        $auditLog->log($user['id'], 'deal_updated', 'deal', $id, $data);

        $this->view->redirect('/dashboard/deals/' . $id);
    }

    public function archive($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view->redirect('/dashboard/deals/' . $id);
            return;
        }

        // Validate CSRF
        $token = $_POST['csrf_token'] ?? '';
        if (!$this->auth->validateCsrfToken($token)) {
            die('CSRF validation failed');
        }

        $dealModel = new Deal($this->db);
        $auditLog = new AuditLog($this->db);
        
        $dealModel->archive($id);
        
        // Log archival
        $user = $this->auth->user();
        $auditLog->log($user['id'], 'deal_archived', 'deal', $id);

        $this->view->redirect('/dashboard/deals');
    }
}
