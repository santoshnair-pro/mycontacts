<?php

namespace App\util;

final class Router
{
    private array $routes = [];

    public function dispatch(string $path): array | string | null
    {
        $path   = $this->normalizePath($path);
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        foreach ($this->routes as $route) {
            if (!preg_match("#^{$route['path']}$#", $path) || $route['method'] !== $method) {
                continue;
            }
            [$class, $function] = $route['controller'];
            $controllerInstance = new $class();
            return $controllerInstance->{$function}();
        }
        return ['templatePage' => 'pages/404.html.twig', 'pageData' => []];
    }

    public function add(string $method, string $path, array $controller)
    {
        $path           = $this->normalizePath($path);
        $this->routes[] = ['path' => $path, 'method' => strtoupper($method), 'controller' => $controller, 'middlewares' => []];
    }

    private function normalizePath(string $path): string
    {
        $path = trim($path, '/');
        $path = "/{$path}/";
        $path = preg_replace('#[/]{2,}#', '/', $path);
        return $path;
    }
}
