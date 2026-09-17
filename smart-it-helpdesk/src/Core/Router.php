<?php
namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, callable|string $handler): void { $this->add('GET', $path, $handler); }
    public function post(string $path, callable|string $handler): void { $this->add('POST', $path, $handler); }

    private function add(string $method, string $path, callable|string $handler): void
    {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        $this->routes[] = ['method' => $method, 'pattern' => '#^' . $pattern . '$#', 'handler' => $handler];
    }

    public function dispatch(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
        $base = '/smart-it-helpdesk/public';
        if (str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }
        $uri = ($uri === '' || $uri === false) ? '/' : $uri;
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $handler = $route['handler'];

                if (is_callable($handler)) {
                    call_user_func_array($handler, $params);
                    return;
                }

                if (is_string($handler) && str_contains($handler, '@')) {
                    [$class, $action] = explode('@', $handler);
                    $controller = new $class();
                    call_user_func_array([$controller, $action], $params);
                    return;
                }
            }
        }

        http_response_code(404);
        echo "<h1 style='font-family: sans-serif; text-align: center; margin-top: 50px;'>404 Not Found</h1>";
    }
}
