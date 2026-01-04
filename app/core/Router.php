<?php
class Router
{
    protected $routes = [];

    public function add($pattern, $callable)
    {
        $this->routes[$pattern] = $callable;
    }

    public function dispatch()
    {
        $controller = $_GET['controller'] ?? 'PageController';
        $action = $_GET['action'] ?? 'index';

        // Basic guardrail: only allow alphanumeric/underscore controller & action names
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $controller) || !preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $action)) {
            header("HTTP/1.0 400 Bad Request");
            echo "Invalid route";
            return;
        }

        // Require controller naming convention to reduce accidental class exposure
        if (substr($controller, -10) !== 'Controller') {
            header("HTTP/1.0 404 Not Found");
            echo "Route not found";
            return;
        }

        if (class_exists($controller)) {
            $c = new $controller();
            if (method_exists($c, $action) && is_callable([$c, $action])) {
                return $c->{$action}();
            }
        }

        header("HTTP/1.0 404 Not Found");
        echo "Route not found";
    }
}
