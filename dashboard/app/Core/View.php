<?php

namespace App\Core;

class View
{
    private $config;
    private $viewPath;
    private $layoutPath;

    public function __construct($config)
    {
        $this->config = $config;
        $this->viewPath = __DIR__ . '/../Views/';
        $this->layoutPath = __DIR__ . '/../Views/layouts/';
    }

    public function render($view, $data = [], $layout = 'main')
    {
        $data['config'] = $this->config;
        extract($data);
        
        ob_start();
        
        $viewFile = $this->viewPath . str_replace('.', '/', $view) . '.php';
        
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View not found: {$view}");
        }
        
        include $viewFile;
        $content = ob_get_clean();
        
        if ($layout) {
            $layoutFile = $this->layoutPath . $layout . '.php';
            
            if (!file_exists($layoutFile)) {
                throw new \RuntimeException("Layout not found: {$layout}");
            }
            
            ob_start();
            include $layoutFile;
            return ob_get_clean();
        }
        
        return $content;
    }

    public function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function redirect($url)
    {
        header("Location: {$url}");
        exit;
    }
}
