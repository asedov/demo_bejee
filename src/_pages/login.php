<?php
declare(strict_types=1);

include __DIR__ . '/_header.php';

$message = '';
$login = null;

if ('POST' === $http_method) {
    $login = $_POST['login'] ?? null;
    $passw = $_POST['password'] ?? null;

    if (ADMIN_LOGIN === $login && ADMIN_PASSW === $passw) {
        setcookie('login', $login);
        header('Location: /', true, 302);
        exit();
    } else {
        $message = <<<HTML
                <div class="alert alert-danger" role="alert">
                    Ошибка: Пользователь не найден или указан неправильный пароль!
                </div>
HTML;
    }
}

echo <<<HTML
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Войти</li>
          </ol>
        </nav>
        
        {$message}

        <form action="/login" method="post" style="max-width: 400px">
          <div class="form-group">
            <label for="login">Логин</label>
            <input type="text" class="form-control" id="login" placeholder="Введите логин" name="login" value="{$login}">
          </div>
          <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" class="form-control" id="password" placeholder="Пароль" name="password">
          </div>
          <button type="submit" class="btn btn-primary">Войти</button>
        </form>
HTML;

include __DIR__ . '/_footer.php';
