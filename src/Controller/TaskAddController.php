<?php
declare(strict_types=1);

namespace App\Controller;

use Exception;
use App\Entity\Task;
use App\Model\TaskModel;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class TaskAddController
 *
 * @package App\Controller
 */
final class TaskAddController implements ControllerInterface
{
    /**
     * @inheritDoc
     */
    public function dispatch(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $post = $request->getParsedBody();

        $task = (new Task())
            ->setCreatedAt(time())
            ->setStatus(Task::TASK_STATUS_NEW)
            ->setUserName($post['user_name'] ?? '')
            ->setUserEmail($post['user_email'] ?? '')
            ->setDescription($post['description'] ?? '');

        try {
            $mode = new TaskModel();
            $mode->persist($task);
        } catch (Exception $e) {
            $response->getBody()->write($e->getMessage());

            return $response
                ->withHeader('Content-Type', 'text/plain')
                ->withStatus(500);
        }

        return $response
            ->withStatus(302)
            ->withHeader('Location', '/');
    }
}
