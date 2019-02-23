<?php
declare(strict_types=1);

const ADMIN_LOGIN = 'admin';
const ADMIN_PASSW = 'admin';

const TASK_STATUS_NEW = 0;
const TASK_STATUS_DONE = 1;

const SORT_BY_CREATED_AT = 'created_at';
const SORT_BY_STATUS = 'status';
const SORT_BY_USER_NAME = 'user_name';
const SORT_BY_USER_EMAIL = 'user_email';

const SORT_ORDER_ASK = 'asc';
const SORT_ORDER_DESC = 'desc';

const TASKS_PER_PAGE = 3;

$persist_storage = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tasks.json';

$http_method = $_SERVER['REQUEST_METHOD'];

$action = $_GET['action'] ?? 'index';
$task_id = $_GET['taskId'] ?? false;
$sort_by = $_GET['sortBy'] ?? SORT_BY_CREATED_AT;
$order_by = $_GET['orderBy'] ?? SORT_ORDER_DESC;
$curr_page = (int)($_GET['page'] ?? 1);

$user_login = $_COOKIE['login'] ?? false;

$log_in = '<a class="btn btn-outline-primary" href="?action=login">Войти</a>';
if ($user_login) {
    $log_in = <<<HTML
        <a class="p-2 text-dark" href="#">Вы вошли как <b>{$user_login}</b></a>
        <a class="btn btn-outline-warning" href="?action=logout">Выйти</a>
HTML;
}

$html_header = <<<HTML
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

const HTML_FOOTER = <<<'HTML'
</div>
<br /><br />
</body></html>
HTML;

if (!file_exists($persist_storage)) {
    if (!file_put_contents($persist_storage, '[]', LOCK_EX)) {
        header('HTTP/1.1 500 Internal Server Error');
        echo 'Error: Could not create persistance storage!';
        exit();
    }
}

$storage = file_get_contents($persist_storage);
$tasks = json_decode($storage, true);

if ('index' === $action) {
    echo $html_header;

    echo <<<HTML
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
          </ol>
        </nav>
HTML;


    if (count($tasks) > 0) {
        usort($tasks, function (array $a, array $b) use ($sort_by, $order_by) {
            if (SORT_BY_CREATED_AT === $sort_by) {
                if ($a['created_at'] === $b['created_at']) {
                    return 0;
                }
                if (SORT_ORDER_ASK === $order_by) {
                    return ($a['created_at'] < $b['created_at']) ? -1 : 1;
                } else {
                    return ($a['created_at'] > $b['created_at']) ? -1 : 1;
                }
            } elseif (SORT_BY_STATUS === $sort_by) {
                if ($a['status'] === $b['status']) {
                    return 0;
                }
                if (SORT_ORDER_ASK === $order_by) {
                    return ($a['status'] < $b['status']) ? -1 : 1;
                } else {
                    return ($a['status'] > $b['status']) ? -1 : 1;
                }
            } elseif (SORT_BY_USER_NAME === $sort_by) {
                if ($a['user_name'] === $b['user_name']) {
                    return 0;
                }
                if (SORT_ORDER_ASK === $order_by) {
                    return strcmp($a['user_name'], $b['user_name']);
                } else {
                    return strcmp($a['user_name'], $b['user_name']) * -1;
                }
            } elseif (SORT_BY_USER_EMAIL === $sort_by) {
                if ($a['user_email'] === $b['user_email']) {
                    return 0;
                }
                if (SORT_ORDER_ASK === $order_by) {
                    return strcmp($a['user_email'], $b['user_email']);
                } else {
                    return strcmp($a['user_email'], $b['user_email']) * -1;
                }
            }

            return 0;
        });

        $pages = (int)ceil(count($tasks) / TASKS_PER_PAGE);
        $curr_page = max($curr_page, 1);
        $curr_page = min($curr_page, $pages);

        $offset = ($curr_page - 1) * TASKS_PER_PAGE;

        $tasks_page = array_slice($tasks, $offset, TASKS_PER_PAGE, false);

        $hdr_stat = "<a href=\"/?page=1&orderBy=asc&sortBy=" . SORT_BY_STATUS . "&page={$curr_page}\">Статус</a>";
        if (SORT_BY_STATUS === $sort_by) {
            $hdr_stat = "<a href=\"/?page=1&orderBy=" . SORT_ORDER_ASK . "&sortBy=" . SORT_BY_STATUS . '">Статус &#9660;</a>';
            if (SORT_ORDER_ASK === $order_by) {
                $hdr_stat = '<a href="/?page=1&orderBy=' . SORT_ORDER_DESC . '&sortBy=' . SORT_BY_STATUS . '">Статус &#9650;</a>';
            }
        }

        $hdr_name = "<a href=\"/?page=1&orderBy=asc&sortBy=" . SORT_BY_USER_NAME . "&page={$curr_page}\">Имя</a>";
        if (SORT_BY_USER_NAME === $sort_by) {
            $hdr_name = "<a href=\"/?page=1&orderBy=" . SORT_ORDER_ASK . "&sortBy=" . SORT_BY_USER_NAME . '">Имя &#9660;</a>';
            if (SORT_ORDER_ASK === $order_by) {
                $hdr_name = '<a href="/?page=1&orderBy=' . SORT_ORDER_DESC . '&sortBy=' . SORT_BY_USER_NAME . '">Имя &#9650;</a>';
            }
        }

        $hdr_mail = "<a href=\"/?page=1&orderBy=asc&sortBy=" . SORT_BY_USER_EMAIL . "&page={$curr_page}\">E-mail</a>";
        if (SORT_BY_USER_EMAIL === $sort_by) {
            $hdr_mail = "<a href=\"/?page=1&orderBy=" . SORT_ORDER_ASK . "&sortBy=" . SORT_BY_USER_EMAIL . '">E-mail &#9660;</a>';
            if (SORT_ORDER_ASK === $order_by) {
                $hdr_mail = '<a href="/?page=1&orderBy=' . SORT_ORDER_DESC . '&sortBy=' . SORT_BY_USER_EMAIL . '">E-mail &#9650;</a>';
            }
        }

        echo <<<HTML
            <table class="table table-striped">
                <thead>
                    <tr>
                      <th scope="col">{$hdr_stat}</th>
                      <th scope="col">{$hdr_name}</th>
                      <th scope="col">{$hdr_mail}</th>
                      <th scope="col">Описание</th>
                    </tr>
                </thead>
                <tbody>
HTML;

        foreach ($tasks_page as $task) {
            $stat = TASK_STATUS_DONE === $task['status'] ? 'checked' : '';
            $name = htmlspecialchars($task['user_name'] ?? '', ENT_HTML5, 'UTF-8');
            $mail = htmlspecialchars($task['user_email'] ?? '', ENT_HTML5, 'UTF-8');
            $desc = htmlspecialchars($task['description'] ?? '', ENT_HTML5, 'UTF-8');

            echo <<<HTML
                <tr>
                    <td><input type="checkbox" disabled {$stat} /></td>
                    <td>{$name}</td>
                    <td>{$mail}</td>
                    <td>{$desc}</td>
HTML;

            if (ADMIN_LOGIN === $user_login) {
                echo <<<HTML
                    <td>[<a href="?action=editTask&taskId={$task['id']}&page={$curr_page}&orderBy={$order_by}&sortBy={$sort_by}">редактировать</a>]</td>
HTML;
            }

            echo <<<HTML
                </tr>
HTML;
        }
        echo '</tbody></table>';

        echo <<<HTML
            <nav aria-label="Page navigation example">
              <ul class="pagination">
HTML;
        for ($page = 1; $page <= $pages; $page++) {
            if ($curr_page === $page) {
                echo <<<HTML
                    <li class="page-item active" aria-current="page">
                        <a class="page-link" href="?orderBy={$order_by}&sortBy={$sort_by}&page={$page}">{$page}<span class="sr-only">(current)</span></a>
                    </li>
HTML;
            } else {
                echo <<<HTML
                    <li class="page-item"><a class="page-link" href="?orderBy={$order_by}&sortBy={$sort_by}&page={$page}">{$page}</a></li> 
HTML;
            }
        }

        echo <<<HTML
              </ul>
            </nav>
HTML;
    } else {
        echo '<div>Нет задач</div>';
    }

    echo '<hr />';

    echo <<<HTML
        <form action="/?action=addNewTask" method="post" style="max-width: 400px">
          <div class="form-group">
            <label for="user_name">Имя</label>
            <input type="text" class="form-control" id="user_name" placeholder="Имя" name="user_name">
          </div>
          <div class="form-group">
            <label for="user_email">E-mail</label>
            <input type="email" class="form-control" id="user_email" placeholder="E-mail" name="user_email">
          </div>
          <div class="form-group">
            <label for="description">Описание</label>
            <textarea class="form-control" id="description" rows="3" placeholder="Описание" name="description"></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Добавить</button>
        </form>
HTML;
    echo HTML_FOOTER;
} elseif ('addNewTask' === $action) {
    if ('POST' === $http_method) {
        $new_task = [
            'id'          => uniqid(),
            'created_at'  => time(),
            'status'      => TASK_STATUS_NEW,
            'user_name'   => $_POST['user_name'] ?? null,
            'user_email'  => $_POST['user_email'] ?? null,
            'description' => $_POST['description'] ?? null,
        ];

        $tasks[$new_task['id']] = $new_task;
        $new_json = json_encode($tasks, JSON_UNESCAPED_UNICODE);

        if (!file_put_contents($persist_storage, $new_json, LOCK_EX)) {
            header('HTTP/1.1 500 Internal Server Error');
            echo 'Error: Could not store a new task!';
            exit();
        }
    }
    header('Location: /', true, 302);
    exit();
} elseif ('login' === $action) {
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

    echo $html_header;
    echo <<<HTML
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Войти</li>
          </ol>
        </nav>
        
        {$message}

        <form action="/?action=login" method="post" style="max-width: 400px">
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
    echo HTML_FOOTER;
} elseif ('logout' === $action) {
    setcookie('login');
    header('Location: /', true, 302);
} elseif ('editTask' === $action) {
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
    if (!$task_id) {
        header('HTTP/1.1 400 Bad Request');
        echo '400 Bad Request';
        exit();
    }
    if (!array_key_exists($task_id, $tasks)) {
        header('HTTP/1.1 404 Not Found');
        echo '404 File not found';
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

    echo $html_header;
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
    echo HTML_FOOTER;
} else {
    header('HTTP/1.1 404 Not Found');
    echo '404 File not found';
}
