<?php 
// ernicani/Routing/Route.php



namespace Ernicani\Routing;

class Route {
    private string $path;
    private $action;
    private string $name;
    private array $params = [];
    private array $methods;

    public function __construct(string $path, $action, string $name, array $methods = ['GET']) {
        $this->path = $path;
        $this->action = $action;
        $this->name = $name;
        $this->methods = $methods;
    }

    public function matches(string $pathInfo, string $requestMethod): bool {
        if (!in_array($requestMethod, $this->methods)) {
            return false;
        }

        // Existing matching logic...
        $pattern = preg_replace('#\{([a-z]+)\}#', '([^/]+)', $this->path);
        $pattern = "#^$pattern$#";

        if (preg_match($pattern, $pathInfo, $matches)) {
            array_shift($matches); // Remove the complete match from the array
            $this->params = $matches; // Store captured parameters
            return true;
        }

        return false;
    }

    public function getAction() {
        return $this->action;
    }

    public function getParams(): array {
        return $this->params;
    }

    public function getPath(): string {
        return $this->path;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getMethod(): array {
        return $this->methods;
    }

    // Add this method to set or update a parameter
    public function setParam(string $key, string $value): void {
        $this->params[$key] = $value;
    }
}
