<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

include __DIR__ . '/../vendor/autoload.php';

$router = new App\Router();
$router
    ->get('/', function (Request $request, Response $response): Response {
        return include __DIR__ . '/../src/_pages/index.php';
    })
    ->post('/tasks', function (Request $request, Response $response): Response {
        return include __DIR__ . '/../src/_pages/addNewTask.php';
    })
    ->any('/tasks/(?<taskId>[a-z0-9]+)[/]?', function (Request $request, Response $response): Response {
        return include __DIR__ . '/../src/_pages/editTask.php';
    })
    ->any('/login', function (Request $request, Response $response): Response {
        return include __DIR__ . '/../src/_pages/login.php';
    })
    ->get('/logout', function (Request $request, Response $response): Response {
        setcookie('login');

        return $response
            ->withStatus(302)
            ->withHeader('Location', '/');
    });

$response = $router->run(
    Zend\Diactoros\ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES),
    new Zend\Diactoros\Response()
);

foreach ($response->getHeaders() as $header => $values) {
    foreach ($values as $value) {
        header("{$header}: {$value}", false);
    }
}

http_response_code($response->getStatusCode());

echo (string)$response->getBody();
