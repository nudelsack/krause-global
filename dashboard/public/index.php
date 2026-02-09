<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Core\Router;
use App\Core\Auth;
use App\Core\View;

// Load configuration
$config = require __DIR__ . '/../config/config.php';

// Initialize database
$db = Database::getInstance($config['db']);

// Initialize auth
$auth = new Auth($config, $db);

// Initialize view
$view = new View($config);

// Initialize router
$router = new Router($config);

// Public routes
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->post('/logout', 'AuthController@logout');

// Protected routes - Dashboard
$router->get('/', 'DashboardController@index');
$router->get('/dashboard', 'DashboardController@index');

// Pipeline
$router->get('/pipeline', 'PipelineController@index');
$router->post('/pipeline/update-status', 'PipelineController@updateStatus');

// Deals
$router->get('/deals', 'DealController@index');
$router->get('/deals/create', 'DealController@create');
$router->post('/deals/store', 'DealController@store');
$router->get('/deals/{id}', 'DealController@show');
$router->get('/deals/{id}/edit', 'DealController@edit');
$router->post('/deals/{id}/update', 'DealController@update');
$router->post('/deals/{id}/archive', 'DealController@archive');

// LOI
$router->get('/loi/incoming', 'DocumentController@loiIncoming');
$router->get('/loi/outgoing', 'DocumentController@loiOutgoing');

// Offers
$router->get('/offers/received', 'DocumentController@offersReceived');
$router->get('/offers/sent', 'DocumentController@offersSent');

// Documents
$router->get('/documents', 'DocumentController@list');
$router->get('/documents/upload', 'DocumentController@uploadForm');
$router->post('/documents/store', 'DocumentController@uploadStore');
$router->get('/deals/{id}/documents', 'DocumentController@index');
$router->post('/deals/{id}/documents/upload', 'DocumentController->upload');
$router->get('/documents/{id}/download', 'DocumentController@download');
$router->get('/documents/{id}/preview', 'DocumentController@preview');
$router->post('/documents/{id}/extract', 'DocumentController@extract');
$router->post('/documents/{id}/delete', 'DocumentController@delete');

// Parties
$router->get('/parties', 'PartyController@index');
$router->post('/parties/store', 'PartyController@store');
$router->get('/parties/{id}', 'PartyController@show');

// Workflow
$router->get('/deals/{id}/workflow', 'WorkflowController@show');
$router->post('/deals/{id}/workflow/update', 'WorkflowController@updateStep');

// Search
$router->get('/search', 'SearchController@index');
$router->post('/search', 'SearchController@search');

// Export
$router->post('/deals/{id}/export', 'ExportController@exportDossier');

// Dispatch
try {
    $router->dispatch();
} catch (\Exception $e) {
    if ($config['app']['debug']) {
        echo '<pre>' . $e->getMessage() . "\n" . $e->getTraceAsString() . '</pre>';
    } else {
        http_response_code(500);
        echo 'Internal Server Error';
    }
}
