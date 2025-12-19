<?php

namespace App;

require './vendor/autoload.php';

use App\util\Router;
use App\util\AppLogger;
use App\util\UtilityFunctions;
use App\controllers\SiteController;
use App\controllers\UserController;
use App\controllers\ContactController;
use Dotenv\Dotenv;
use Exception;


final class Server
{
    public $logger;
    public $twig;
    public function __construct()
    {
        $this->loadEnvironment();
        $this->initializeLogger();
    }
    private function loadEnvironment()
    {
        // load environment variables from file
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load();
    }
    private function initializeLogger()
    {
        // initialize application logger
        $this->logger = new AppLogger();
    }
    private function initializeRouter()
    {

        $data = ['title' => 'My Contacts'];
        // get the request uri
        $baseUrl = $_ENV['APP_ROOT_DIR'] ?? '//';
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = str_ireplace($baseUrl, '', $path);
        $request = (string) $uri;
        // get the request method
        $requestMethod = strtoupper($_SERVER['REQUEST_METHOD']) ?? 'GET'; //default to GET if not found
        // default to login page
        $templatePage = 'pages/login.html.twig';

        $router = new Router();
        // check if user is logged in
        $isAuthenticated = UtilityFunctions::isAuthenticated();

        $router->add('GET', '/', [SiteController::class, $isAuthenticated ? 'home' : 'login']);
        $router->add('GET', '/home', [SiteController::class, $isAuthenticated ? 'home' : 'login']);
        $router->add('GET', '/login', [SiteController::class, $isAuthenticated ? 'home' : 'login']);
        $router->add('GET', '/signup', [SiteController::class, 'signup']);
        $router->add('GET', '/forgot', [SiteController::class, 'forgot']);
        $router->add('GET', '/about', [SiteController::class, 'about']);
        $router->add('POST', '/login', [UserController::class, 'login']);
        $router->add('POST', '/logout', [UserController::class, 'logout']);
        $router->add('POST', '/register', [UserController::class, 'create']);
        $router->add('POST', '/forgot', [UserController::class, 'forgotPassword']);
        if ($isAuthenticated) {
            $router->add('GET', '/myaccount', [SiteController::class, 'myaccount']);
            $router->add('POST', '/myaccount', [UserController::class, 'update']);

            $router->add('GET', '/changepassword', [SiteController::class, 'changepassword']);
            $router->add('POST', '/changepassword', [UserController::class, 'changePassword']);

            $router->add('POST', '/contacts', [ContactController::class, 'read']);

            $router->add('GET', '/contact', [SiteController::class, 'contact']);
            $router->add('POST', '/contact', [ContactController::class, 'create']);
            $router->add('DELETE', '/contact', [ContactController::class, 'delete']);
            $router->add('PUT', '/contact', [ContactController::class, 'update']);

            $router->add('POST', '/upload', [ContactController::class, 'upload']);
            $router->add('POST', '/deleteavatar', [ContactController::class, 'deleteavatar']);
        }
        if ($requestMethod === "GET") {
            // get the template page and page data - $templatePage & $pageData
            extract($router->dispatch($request));
            if ($isAuthenticated) {
                //fetch loggedin user deails
                $data['user'] = UtilityFunctions::getSessionUser();
            }
            $this->logger->logInfo('Application bootstrapped');
            $template = $this->twig->load($templatePage);
            if (!empty($pageData)) {
                $data['row'] = $pageData;
            }
            echo $template->render($data);
        } else {
            $router->dispatch($request);
        }
    }
    public function run()
    {
        try {
            // twig template configuration
            $loader = new \Twig\Loader\FilesystemLoader('views');
            $this->twig = new \Twig\Environment($loader, [
                'debug' => true,
                'cache' => 'cache',
            ]);
            $this->twig->addExtension(new \Twig\Extension\DebugExtension());
            $this->initializeRouter();
        } catch (\Twig\Error\LoaderError $e) {
            $this->logger->logError($e->getMessage());
            $template = $this->twig->load('pages/error.html.twig');
            echo $template->render();
        } catch (Exception $e) {
            $this->logger->logError($e->getMessage());
            $template = $this->twig->load('pages/error.html.twig');
            echo $template->render();
        }
    }
}
// initialize and run the server
$server = new Server();
$server->run();
