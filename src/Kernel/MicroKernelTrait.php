<?php

// ernicani/Kernel/MicroKernelTrait.php

namespace Ernicani\Kernel;

use Ernicani\Routing\Router;
use Ernicani\Routing\Route;


use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use PDO;

trait MicroKernelTrait
{
    private  $router;
    private $debug;
    private EntityManager $entityManager;

    public function boot()
    {
        $this->loadEnvironment();
        $this->loadDoctrine();
        $this->router = new Router();
        $this->loadRoutes();
        $this->handleRequest($_SERVER['REQUEST_URI']);
    }

    public function loadDoctrine() 
    {
        $isDevMode = true;
        $paths = [__DIR__ . '/../../../src/Entity'];

        $config = Setup::createAnnotationMetadataConfiguration(
            $paths,
            true, // Enable development mode
            __DIR__ . '/proxies', // Optional proxy directory path
            null, // Custom cache implementation (optional)
            false // Do not use simple annotation reader
        );
        
        $options = array(
        );
        if ($_ENV['SSL_CERT_PATH']) {
            $options = array(
                PDO::MYSQL_ATTR_SSL_CA => $_ENV['SSL_CERT_PATH'] ,
              );
        }

        $dbParams = [
            'driver' => 'pdo_mysql',
            'user' => $_ENV['DB_USERNAME'],
            'password' => $_ENV['DB_PASSWORD'],
            'dbname' => $_ENV['DB_NAME'],
            'host' => $_ENV['DB_HOST'],
            'driverOptions' => $options,
        ];
        $connection = DriverManager::getConnection($dbParams, $config);
        $this->entityManager = new EntityManager($connection, $config);
    }

    public function loadEnvironment()
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../');
        $dotenv->load();
    }

    public function handleRequest($uri)
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        [$action, $params] = $this->router->match($uri, $requestMethod);
    
        if ($action) {
            $this->executeAction(array_merge([$action], [$params]));
        } else {
            $this->redirectTo404();
        }
    }

    
    private function redirectTo404()
    {
        $uri = '/404';
        $requestMethod = 'GET';
        [$action, $params] = $this->router->match($uri, $requestMethod);
        if ($action) {
            $this->executeAction(array_merge([$action], [$params]));
        } else {
            http_response_code(404);
            echo "404 Not Found\n";
        }
    }
    
    

    private function loadRoutes()
    {
        $controllerFiles = glob(__DIR__ . '/../../../src/Controller/*.php');

        foreach ($controllerFiles as $file) {
            $controllerClass = 'App\\Controller\\' . basename($file, '.php');
            $this->addRoutesFromClass($controllerClass);
        }
    }

    private function addRoutesFromClass($class)
    {
        $reflectionClass = new \ReflectionClass($class);

        if ($reflectionClass->isSubclassOf('Ernicani\Controllers\AbstractController')) {
            $methods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $method) {
                $attributes = $method->getAttributes(Route::class);

                foreach ($attributes as $attribute) {
                    $args = $attribute->getArguments();
                    $routePath = $args['path'] ?? null;
                    $routeName = $args['name'] ?? null;
                    $routeMethods = $args['methods'] ?? ['GET'];

                    if ($routePath && $routeName) {
                        $action = [$class, $method->getName()];
                        $this->router->addRoute($routePath, $action, $routeName, $routeMethods);
                    }
                }
            }
        }
    }

    private function executeAction($actionWithParams)
    {
        // $actionWithParams[0] est l'action, $actionWithParams[1] sont les paramètres
        if (is_array($actionWithParams[0]) && is_string($actionWithParams[0][0])) {
            $controller = new $actionWithParams[0][0]($this->router, $this->entityManager);
            $method = $actionWithParams[0][1];
    
            if (method_exists($controller, $method)) {
                // Appeler la méthode sur le contrôleur avec les paramètres
                call_user_func_array([$controller, $method], $actionWithParams[1]);
            } else {
                echo "Method $method not found in controller.\n";
            }
        } else {
            echo "Invalid action format\n";
        }
    }
    
}
