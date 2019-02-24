<?php
declare(strict_types=1);

ob_start();

include __DIR__ . '/../vendor/autoload.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = $_SERVER['REDIRECT_URL'] ?? '/';

$route = (new App\Router())
    ->get('/', function (): void {
        include __DIR__ . '/../src/_pages/index.php';
    })
    ->any('/login', function (): void {
        include __DIR__ . '/../src/_pages/login.php';
    })
    ->get('/logout', function (): void {
        setcookie('login');
        header('Location: /', true, 302);
    })
    ->post('/tasks', function (): void {
        include __DIR__ . '/../src/_pages/addNewTask.php';
    })
    ->any('/tasks/[a-z0-9]+[/]?', function (): void {
        include __DIR__ . '/../src/_pages/editTask.php';
    })
    ->route($method, $path);

$route();
