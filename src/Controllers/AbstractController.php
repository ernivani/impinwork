<?php

// ernicani/controllers/AbstractController.php

namespace Ernicani\Controllers;

use Doctrine\ORM\EntityManager;
use Ernicani\Form\Form;
use Ernicani\Form\FormBuilder;
use Ernicani\Routing\Router;
use App\Entity\User;

abstract class AbstractController
{

   protected $router;
   protected EntityManager $entityManager;

   public function __construct(Router $router, EntityManager $entityManager)
   {
       $this->router = $router;
       $this->entityManager = $entityManager;
   }
   

    public function render($view, $params = [])
    {
        $viewPath = __DIR__ . '/../../../views/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            $params['path'] = function ($routeName, $params = [])
            {
                // Supposons que $router est une instance de votre classe Router disponible globalement
                global $router; // Si vous utilisez un conteneur de services, obtenez $router à partir de là.
            
                try {
                    $path = $this->router->getPathByName($routeName); // Obtenez le modèle de chemin de la route par son nom.
                    
                    // Remplacer les paramètres dans le chemin de la route
                    foreach ($params as $key => $value) {
                        $path = str_replace("{" . $key . "}", $value, $path);
                    }
            
                    return $path;
                } catch (\Exception $e) {
                    // Gérer l'exception ou retourner un chemin par défaut
                    return '/';
                }
            };
            $params['flash'] = $_SESSION['flash'] ?? [];

            extract($params);

            include $viewPath;

            unset($_SESSION['flash']);
        } else {
            echo "Error: View file not found - $viewPath";
        }
    }

    public function generateUrl($routeName)
    {
        return $this->router->getPathByName($routeName);
    }



    protected function createForm(string $type, array $options = []) : Form
    {
        $formBuilder = new FormBuilder();
        $formType = new $type();
        $formType->buildForm($formBuilder, $options);

        $form = $formBuilder->buildForm();


        return $form;
    }

    protected function redirectToRoute(string $routeName, array $params = []): void
    {
        $url = $this->router->getPathByName($routeName);

        foreach ($params as $key => $value) {
            $url = str_replace("{" . $key . "}", $value, $url);
        }
        

        header("Location: $url");
        exit;
    }

    protected function addFlash(string $type, string $message)
    {
        $_SESSION['flash'][$type] = $message;
    }

    protected function isGranted(string $role)
    {
        if (!isset($_SESSION['user'])) {
            return false;
        }

        $user = $this->entityManager->getRepository(User::class)->find($_SESSION['user']);

        if (!$user) {
            return false;
        }

        return in_array($role, $user->getRoles());
    }

    protected function jsonResponse(array $data, int $status = 200)
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($status);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
