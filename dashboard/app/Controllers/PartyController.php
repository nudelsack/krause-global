<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Core\Database;
use App\Models\Party;

class PartyController
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
        $partyModel = new Party($this->db);
        
        $search = $_GET['search'] ?? '';
        $parties = $search ? $partyModel->search($search) : $partyModel->all();

        echo $this->view->render('parties.index', [
            'parties' => $parties,
            'search' => $search,
            'csrf_token' => $this->auth->generateCsrfToken(),
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view->redirect('/dashboard/parties');
            return;
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!$this->auth->validateCsrfToken($token)) {
            die('CSRF validation failed');
        }

        $partyModel = new Party($this->db);

        $data = [
            'party_type' => $_POST['party_type'] ?? 'company',
            'company_name' => $_POST['company_name'] ?? '',
            'country' => $_POST['country'] ?? null,
            'address_text' => $_POST['address_text'] ?? null,
            'website' => $_POST['website'] ?? null,
        ];

        $partyId = $partyModel->create($data);
        
        $_SESSION['success'] = 'Party created successfully';
        $this->view->redirect('/dashboard/parties/' . $partyId);
    }

    public function show($id)
    {
        $partyModel = new Party($this->db);
        $party = $partyModel->find($id);
        
        if (!$party) {
            http_response_code(404);
            echo 'Party not found';
            return;
        }

        $contacts = $partyModel->getContacts($id);

        echo $this->view->render('parties.show', [
            'party' => $party,
            'contacts' => $contacts,
            'csrf_token' => $this->auth->generateCsrfToken(),
        ]);
    }
}
