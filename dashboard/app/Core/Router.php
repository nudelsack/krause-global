<?php

namespace App\Core;

class Router
{
    private $routes = [];
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function get($path, $handler)
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post($path, $handler)
    {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute($method, $path, $handler)
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
        ];
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];
        
        // Remove query string
        $uri = strtok($uri, '?');
        
        // Remove /dashboard prefix if present
        $uri = preg_replace('#^/dashboard#', '', $uri);
        
        // Ensure leading slash
        if (empty($uri) || $uri[0] !== '/') {
            $uri = '/' . $uri;
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = $this->convertToRegex($route['path']);
            
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove full match
                
                $handler = $route['handler'];
                
                if (is_callable($handler)) {
                    return call_user_func_array($handler, $matches);
                }
                
                if (is_string($handler) && strpos($handler, '@') !== false) {
                    list($controller, $method) = explode('@', $handler);
                    $controller = "App\\Controllers\\{$controller}";
                    
                    if (class_exists($controller)) {
                        $instance = new $controller($this->config);
                        if (method_exists($instance, $method)) {
                            return call_user_func_array([$instance, $method], $matches);
                        }
                    }
                }
                
                throw new \RuntimeException("Handler not found for route: {$route['path']}");
            }
        }

        // No route found
        http_response_code(404);
        echo '404 Not Found';
    }

    private function convertToRegex($path)
    {
        // Convert {param} to named regex capture group
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
}
