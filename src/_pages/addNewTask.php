<?php
declare(strict_types=1);

include __DIR__ . '/_init.php';

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

header('Location: /', true, 302);
