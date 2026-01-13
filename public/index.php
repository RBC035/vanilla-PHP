<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Config\Env;
use App\Helpers\Response;
use App\Middleware\AuthMiddleware;

Env::load(__DIR__ . '/../.env');

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$routes = require_once __DIR__ . '/../app/Routes/api.php';

$found = false;
$publicRoutes = ['/auth/login', '/auth/signup', '/otp/request-otp', '/otp/verify-otp'];

$isFileDownload = strpos($uri, '/progress/file/') === 0;

if (isset($routes[$method])) {
    foreach ($routes[$method] as $route => $handler) {
        $pattern = $route;

        if (preg_match('/\{([a-zA-Z0-9_]+):(.+?)\}/', $pattern)) {
            $pattern = preg_replace_callback('/\{([a-zA-Z0-9_]+):(.+?)\}/', function ($matches) {
                return '(' . $matches[2] . ')';
            }, $pattern);
        } else {
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9._\-]+)', $pattern);
        }

        $pattern = "#^" . $pattern . "$#";

        if (preg_match($pattern, $uri, $matches)) {
            array_shift($matches);

            if (!in_array($uri, $publicRoutes) && !$isFileDownload) {
                AuthMiddleware::authenticate();
            }

            [$controllerClass, $action] = $handler;
            $fullPath = "App\\Controllers\\" . $controllerClass;

            if (class_exists($fullPath)) {
                $controller = new $fullPath();
                call_user_func_array([$controller, $action], $matches);
                $found = true;
                break;
            }
        }
    }
}

if (!$found) {
    Response::error("Route $uri not found", 404);
}
