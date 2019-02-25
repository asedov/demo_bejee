<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

include __DIR__ . '/../vendor/autoload.php';

$router = new App\Router();
$router
    ->get('/', function (Request $request, Response $response): Response {
        return (new App\Controller\IndexController())->dispatch($request, $response);
    })
    ->post('/tasks', function (Request $request, Response $response): Response {
        return (new App\Controller\TaskAddController())->dispatch($request, $response);
    })
    ->any('/tasks/(?<taskId>[a-z0-9]+)[/]?', function (Request $request, Response $response): Response {
        return (new App\Controller\TaskEditController())->dispatch($request, $response);
    })
    ->any('/login', function (Request $request, Response $response): Response {
        return (new App\Controller\LoginController())->dispatch($request, $response);
    })
    ->get('/logout', function (Request $request, Response $response): Response {
        return (new App\Controller\LogoutController())->dispatch($request, $response);
    })
    ->notFound(function (Request $request, Response $response): Response {
        return (new App\Controller\NotFoundController())->dispatch($request, $response);
    });

$request = Zend\Diactoros\ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
$response = new Zend\Diactoros\Response();

$response = $router->run($request, $response);

foreach ($response->getHeaders() as $header => $values) {
    foreach ($values as $value) {
        header("{$header}: {$value}", false);
    }
}

http_response_code($response->getStatusCode());

echo (string)$response->getBody();
