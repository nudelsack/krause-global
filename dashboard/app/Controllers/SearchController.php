<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Core\Database;
use App\Models\Document;

class SearchController
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
        $query = $_GET['q'] ?? '';
        $dealType = $_GET['deal_type'] ?? null;
        $category = $_GET['category'] ?? null;
        
        $results = [];
        
        if ($query) {
            $documentModel = new Document($this->db);
            $results = $documentModel->searchDocuments($query, $dealType, $category);
        }

        $dealTypes = require __DIR__ . '/../../config/deal_types.php';

        echo $this->view->render('search.index', [
            'query' => $query,
            'deal_type' => $dealType,
            'category' => $category,
            'results' => $results,
            'deal_types' => $dealTypes,
            'csrf_token' => $this->auth->generateCsrfToken(),
        ]);
    }

    public function search()
    {
        $this->index();
    }
}
