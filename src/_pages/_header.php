<?php
declare(strict_types=1);

include_once __DIR__ . '/_init.php';

$log_in = '<a class="btn btn-outline-primary" href="/login">Войти</a>';
if ($user_login) {
    $log_in = <<<HTML
        <a class="p-2 text-dark" href="#">Вы вошли как <b>{$user_login}</b></a>
        <a class="btn btn-outline-warning" href="/logout">Выйти</a>
HTML;
}

echo <<<HTML
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <title>Demo Bejee</title>
</head>
<body>
<div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom shadow-sm">
    <h5 class="my-0 mr-md-auto font-weight-normal">Demo Bejee</h5>
    <nav class="my-2 my-md-0 mr-md-3">
    </nav>
    {$log_in}
</div>
<div class="container">
HTML;
