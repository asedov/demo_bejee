<?php
declare(strict_types=1);

/**
 * @var Zend\Diactoros\Response      $response
 * @var Zend\Diactoros\ServerRequest $request
 */

include __DIR__ . '/_init.php';

$post = $request->getParsedBody();

$new_task = [
    'id'          => uniqid(),
    'created_at'  => time(),
    'status'      => TASK_STATUS_NEW,
    'user_name'   => $post['user_name'] ?? null,
    'user_email'  => $post['user_email'] ?? null,
    'description' => $post['description'] ?? null,
];

$tasks[$new_task['id']] = $new_task;
$new_json = json_encode($tasks, JSON_UNESCAPED_UNICODE);

if (!file_put_contents($persist_storage, $new_json, LOCK_EX)) {
    $response->getBody()->write('Error: Could not store a new task!');

    return $response->withStatus(500);
}

return $response
    ->withStatus(302)
    ->withHeader('Location', '/');
