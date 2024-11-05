<?php 

// ernicani/Routing/Router.php

namespace Ernicani\Routing;

class Router
{
    private $routes = [];

    public function addRoute(string $path, $action, string $name, array $methods = ['GET']) {
        $this->routes[$name] = new Route($path, $action, $name, $methods);
    }

    private function normaliz_url(string $url): string {
        return explode('?', $url)[0];
    }

    public function match(string $uri, string $requestMethod): array {
        foreach ($this->routes as $route) {
            if ($route->matches($this->normaliz_url($uri), $requestMethod)) {
                return [$route->getAction(), $route->getParams()];
            }
        }

        return [null, null];
    }

    public function getPathByName($name)
    {

        if (!isset($this->routes[$name])) {
            throw new \Exception("No route with the name $name");
        }
        return $this->routes[$name]->getPath();
    }
}
