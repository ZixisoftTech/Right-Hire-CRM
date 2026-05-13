<?php

namespace App\Core;

class Router
{
    private $routes = [];

    public function add($method, $route, $action, $middleware = [])
    {
        // Convert route variables {var} to regex
        $routeRegex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[a-zA-Z0-9_-]+)', $route);
        $routeRegex = "#^" . $routeRegex . "$#";

        $this->routes[] = [
            'method' => $method,
            'route' => $routeRegex,
            'action' => $action,
            'middleware' => $middleware
        ];
    }

    public function dispatch($url)
    {
        $url = strtok($url, '?');
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['route'], $url, $matches)) {

                // Execute middleware
                foreach ($route['middleware'] as $middleware) {
                    $middlewareInstance = new $middleware();
                    $middlewareInstance->handle();
                }

                // Parse action
                list($controller, $methodName) = explode('@', $route['action']);
                $controllerClass = "App\\Controllers\\" . $controller;

                if (class_exists($controllerClass)) {
                    $controllerInstance = new $controllerClass();
                    if (method_exists($controllerInstance, $methodName)) {
                        // Filter numeric keys from matches
                        $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                        call_user_func_array([$controllerInstance, $methodName], $params);
                        return;
                    }
                }
            }
        }

        // 404
        http_response_code(404);
        echo "404 Not Found";
    }
}
