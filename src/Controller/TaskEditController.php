<?php
declare(strict_types=1);

namespace App\Controller;

use Exception;
use App\View\View;
use App\Model\TaskModel;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class TaskEditController
 *
 * @package App\Controller
 */
class TaskEditController implements ControllerInterface
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function dispatch(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $model = new TaskModel();
        $cookie = $request->getCookieParams();
        $get = $request->getQueryParams();
        $taskId = $request->getAttribute('taskId', '');

        $userLogin = $cookie['login'] ?? null;

        $view = new View();
        $view
            ->set('userLogin', $userLogin)
            ->set('task_id', $taskId)
            ->set('curr_page', $get['page'] ?? 1)
            ->set('order_by', $get['orderBy'] ?? IndexController::SORT_ORDER_ASK)
            ->set('sort_by', $get['sortBy'] ?? IndexController::SORT_BY_CREATED_AT);

        if ($userLogin === null) {
            $view->set('errorMessage', '401 Unauthorized');
        } elseif ($userLogin !== 'admin') {
            $view->set('errorMessage', '403 Forbidden');
        }

        if ($userLogin === 'admin') {
            try {
                $task = $model->getById($taskId);
                if ('POST' === $request->getMethod()) {
                    $post = $request->getParsedBody();

                    $task
                        ->setStatus((int)($post['status'] ?? 0))
                        ->setDescription($post['description'] ?? $task->getDescription());

                    $view->set('successMessage', 'Задача успешно обновлена');

                    try {
                        $model->persist($task);
                    } catch (Exception $e) {
                        $view->set('errorMessage', $e->getMessage());
                    }
                }
                $view
                    ->set('status', $task->getStatus())
                    ->set('name', $task->getUserName())
                    ->set('mail', $task->getUserName())
                    ->set('desc', $task->getDescription());
            } catch (Exception $e) {
                $view->set('errorMessage', $e->getMessage());
            }
        }

        $html = $view->render('task_edit');

        $response->getBody()->write($html);

        return $response;
    }
}
