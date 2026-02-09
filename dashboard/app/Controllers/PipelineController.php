<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Core\Database;
use App\Models\Deal;

class PipelineController
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

    public function index()
    {
        $dealModel = new Deal($this->db);
        $allDeals = $dealModel->all();
        
        // Pipeline Stages
        $stages = [
            'loi_received' => [
                'title' => 'LOI Eingang',
                'icon' => '📥',
                'color' => '#3B82F6',
                'deals' => []
            ],
            'loi_sent' => [
                'title' => 'LOI Ausgang',
                'icon' => '📤',
                'color' => '#6366F1',
                'deals' => []
            ],
            'offer_received' => [
                'title' => 'Angebot erhalten',
                'icon' => '📩',
                'color' => '#10B981',
                'deals' => []
            ],
            'offer_sent' => [
                'title' => 'Angebot gesendet',
                'icon' => '📨',
                'color' => '#8B5CF6',
                'deals' => []
            ],
            'negotiation' => [
                'title' => 'Verhandlung',
                'icon' => '🤝',
                'color' => '#F59E0B',
                'deals' => []
            ],
            'contract' => [
                'title' => 'Vertrag',
                'icon' => '📝',
                'color' => '#06B6D4',
                'deals' => []
            ],
            'completed' => [
                'title' => 'Abgeschlossen',
                'icon' => '✅',
                'color' => '#22C55E',
                'deals' => []
            ],
            'rejected' => [
                'title' => 'Abgelehnt',
                'icon' => '❌',
                'color' => '#EF4444',
                'deals' => []
            ]
        ];
        
        // Organize deals by stage
        foreach ($allDeals as $deal) {
            $status = $deal['status'];
            if (isset($stages[$status])) {
                $stages[$status]['deals'][] = $deal;
            } else {
                // Default to LOI received if status not recognized
                $stages['loi_received']['deals'][] = $deal;
            }
        }
        
        echo $this->view->render('pipeline.index', [
            'stages' => $stages,
            'csrf_token' => $this->auth->generateCsrfToken(),
        ]);
    }
    
    public function updateStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }
        
        $dealId = $_POST['deal_id'] ?? null;
        $newStatus = $_POST['status'] ?? null;
        
        if (!$dealId || !$newStatus) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing parameters']);
            return;
        }
        
        $dealModel = new Deal($this->db);
        $deal = $dealModel->find($dealId);
        
        if (!$deal) {
            http_response_code(404);
            echo json_encode(['error' => 'Deal not found']);
            return;
        }
        
        // Update status
        $dealModel->update($dealId, ['status' => $newStatus]);
        
        echo json_encode(['success' => true]);
    }
}
