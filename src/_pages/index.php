<?php
declare(strict_types=1);

include __DIR__ . '/_header.php';

echo <<<HTML
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
          </ol>
        </nav>
HTML;


if (count($tasks) > 0) {
    usort($tasks, function (array $a, array $b) use ($sort_by, $order_by): int {
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
                    <td>[<a href="/tasks/{$task['id']}/?page={$curr_page}&orderBy={$order_by}&sortBy={$sort_by}">редактировать</a>]</td>
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
        <form action="/tasks" method="post" style="max-width: 400px">
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

include __DIR__ . '/_footer.php';
