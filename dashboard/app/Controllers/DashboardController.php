<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Core\Database;
use App\Models\Deal;
use App\Models\Document;

class DashboardController
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
        $documentModel = new Document($this->db);
        
        // Get statistics
        $allDeals = $dealModel->all();
        $stats = [
            'total_deals' => count($allDeals),
            'by_type' => [],
            'by_status' => [],
            'recent_deals' => array_slice($allDeals, 0, 10),
        ];

        // Calculate stats
        foreach ($allDeals as $deal) {
            // By type
            if (!isset($stats['by_type'][$deal['deal_type']])) {
                $stats['by_type'][$deal['deal_type']] = 0;
            }
            $stats['by_type'][$deal['deal_type']]++;
            
            // By status
            if (!isset($stats['by_status'][$deal['status']])) {
                $stats['by_status'][$deal['status']] = 0;
            }
            $stats['by_status'][$deal['status']]++;
        }
        
        // LOI statistics
        $loiIncoming = $documentModel->getByCategory('loi_incoming');
        $loiOutgoing = $documentModel->getByCategory('loi_outgoing');
        $offersReceived = $documentModel->getByCategory('offer_received');
        $offersSent = $documentModel->getByCategory('offer_sent');
        
        $stats['loi_incoming'] = count($loiIncoming);
        $stats['loi_outgoing'] = count($loiOutgoing);
        $stats['offers_received'] = count($offersReceived);
        $stats['offers_sent'] = count($offersSent);
        
        // Expiring deals
        $today = date('Y-m-d');
        $weekFromNow = date('Y-m-d', strtotime('+7 days'));
        
        $stats['expiring'] = [
            'today' => [],
            'this_week' => [],
            'expired' => []
        ];
        
        foreach ($allDeals as $deal) {
            if (!empty($deal['expiry_date'])) {
                if ($deal['expiry_date'] < $today) {
                    $stats['expiring']['expired'][] = $deal;
                } elseif ($deal['expiry_date'] === $today) {
                    $stats['expiring']['today'][] = $deal;
                } elseif ($deal['expiry_date'] <= $weekFromNow) {
                    $stats['expiring']['this_week'][] = $deal;
                }
            }
        }

        echo $this->view->render('dashboard.index', [
            'user' => $this->auth->user(),
            'stats' => $stats,
            'csrf_token' => $this->auth->generateCsrfToken(),
        ]);
    }
}
