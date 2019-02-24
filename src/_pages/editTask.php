<?php
declare(strict_types=1);

include __DIR__ . '/_header.php';

if (!$user_login) {
    header('HTTP/1.1 401 Unauthorized');
    echo '401 Unauthorized';
    exit();
}
if (ADMIN_LOGIN !== $user_login) {
    header('HTTP/1.1 403 Forbidden');
    echo '403 Forbidden';
    exit();
}

$task_id = '';

if (preg_match('@^/tasks/(?<task_id>[a-z0-9]+)[/]?$@', $_SERVER['REDIRECT_URL'], $matches)) {
    foreach ($matches as $name => $value) {
        if (gettype($name) === 'string') {
            $$name = $value;
        }
    }
}

if (!array_key_exists($task_id, $tasks)) {
    header('HTTP/1.1 404 Not Found');
    echo <<<HTML
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/?page={$curr_page}&sortBy={$sort_by}&orderBy={$order_by}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Задача {$task_id}</li>
          </ol>
        </nav>
        <div class="alert alert-warning" role="alert">
            Задача не найдена
        </div>
HTML;
    include __DIR__ . '/_footer.php';
    exit();
}

$task = $tasks[$task_id];
$status = '';

if ('POST' === $http_method) {
    $task['status'] = ($_POST['status'] ?? '0') === '1' ? TASK_STATUS_DONE : TASK_STATUS_NEW;
    $task['description'] = $_POST['description'] ?? '';

    $tasks[$task['id']] = $task;
    $upd_json = json_encode($tasks, JSON_UNESCAPED_UNICODE);

    if (!file_put_contents($persist_storage, $upd_json, LOCK_EX)) {
        header('HTTP/1.1 500 Internal Server Error');
        echo 'Error: Could not update the task!';
        exit();
    }

    $status = <<<HTML
            <div class="alert alert-success" role="alert">
                Задача успешно обновлена
            </div>
HTML;
}

$stat = TASK_STATUS_DONE === $task['status'] ? 'checked' : '';
$name = htmlspecialchars($task['user_name'] ?? '', ENT_HTML5, 'UTF-8');
$mail = htmlspecialchars($task['user_email'] ?? '', ENT_HTML5, 'UTF-8');
$desc = htmlspecialchars($task['description'] ?? '', ENT_HTML5, 'UTF-8');

echo <<<HTML
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/?page={$curr_page}&sortBy={$sort_by}&orderBy={$order_by}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Задача {$task_id}</li>
          </ol>
        </nav>
        
        {$status}

        <form action="?action=editTask&taskId={$task_id}&page={$curr_page}&sortBy={$sort_by}&orderBy={$order_by}" method="post" style="max-width: 400px">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="status" value="1" id="status" {$stat}>
            <label class="form-check-label" for="status">Выполнено</label>
          </div>
          <div class="form-group">
            <label for="user_name">Имя</label>
            <input readonly type="text" class="form-control" id="user_name" placeholder="Имя" name="user_name" value="{$name}">
          </div>
          <div class="form-group">
            <label for="user_email">E-mail</label>
            <input readonly type="email" class="form-control" id="user_email" placeholder="E-mail" name="user_email" value="{$mail}">
          </div>
          <div class="form-group">
            <label for="description">Описание</label>
            <textarea class="form-control" id="description" rows="3" placeholder="Описание" name="description">{$desc}</textarea>
          </div>
          <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
HTML;

include __DIR__ . '/_footer.php';
