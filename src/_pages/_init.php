<?php
declare(strict_types=1);

/**
 * @var Zend\Diactoros\Response $response
 * @var Zend\Diactoros\ServerRequest $request
 */

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

$get = $request->getQueryParams();

$sort_by = $get['sortBy'] ?? SORT_BY_CREATED_AT;
$order_by = $get['orderBy'] ?? SORT_ORDER_DESC;
$curr_page = (int)($get['page'] ?? 1);

if (!file_exists($persist_storage)) {
    if (!file_put_contents($persist_storage, '[]', LOCK_EX)) {
        header('HTTP/1.1 500 Internal Server Error');
        echo 'Error: Could not create persistance storage!';
        exit();
    }
}

$storage = file_get_contents($persist_storage);
$tasks = json_decode($storage, true);

$user_login = $request->getCookieParams()['login'] ?? false;
