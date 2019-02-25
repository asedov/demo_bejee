<?php
declare(strict_types=1);

namespace App\Model;

use Exception;
use App\Entity\Task;

/**
 * Class TaskModel
 *
 * @package App\Model
 */
class TaskModel
{
    /** @var string */
    private $storage;

    /**
     * TaskModel constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->initStorage();
    }

    /**
     * @throws Exception
     */
    private function initStorage(): void
    {
        $this->storage = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tasks.json';

        if (!file_exists($this->storage)) {
            if (!file_put_contents($this->storage, '[]', LOCK_EX)) {
                throw new Exception('Could not create persistance storage!');
            }
        }
    }

    /**
     * @return array
     */
    private function getStorage(): array
    {
        $storage = file_get_contents($this->storage);

        return json_decode($storage, true);
    }

    /**
     * @return Task[]
     */
    public function getAll(): array
    {
        $tasks = $this->getStorage();
        $result = [];

        foreach ($tasks as $taskId => $task) {
            $result[$taskId] = (new Task($task['id']))
                ->setCreatedAt($task['created_at'])
                ->setStatus($task['status'])
                ->setUserName($task['user_name'])
                ->setUserEmail($task['user_email'])
                ->setDescription($task['description']);
        }

        return $result;
    }

    /**
     * @param mixed $topicId
     * @return Task
     * @throws Exception
     */
    public function getById($topicId): Task
    {
        $tasks = $this->getStorage();

        if (!array_key_exists($topicId, $tasks)) {
            throw new Exception('Task not found');
        }

        $task = $tasks[$topicId];

        return (new Task($topicId))
            ->setStatus($task['status'])
            ->setCreatedAt($task['created_at'])
            ->setUserName($task['user_name'])
            ->setUserEmail($task['user_email'])
            ->setDescription($task['description']);
    }

    /**
     * @param int    $page
     * @param int    $perPage
     * @param string $orderBy
     * @return array
     */
    public function getPageSortByStatus(int $page, int $perPage, string $orderBy = 'asc'): array
    {
        $tasks = $this->getAll();

        usort($tasks, function (Task $taskA, Task $taskB) use ($orderBy): int {
            if ($taskA->getStatus() === $taskB->getStatus()) {
                return 0;
            }
            if ('asc' === $orderBy) {
                return ($taskA->getStatus() < $taskB->getStatus()) ? -1 : 1;
            }

            return ($taskA->getStatus() > $taskB->getStatus()) ? -1 : 1;
        });

        $offset = ($page - 1) * $perPage;

        return array_slice($tasks, $offset, $perPage, false);
    }

    /**
     * @param int    $page
     * @param int    $perPage
     * @param string $orderBy
     * @return array
     */
    public function getPageSortByCreated(int $page, int $perPage, string $orderBy = 'asc'): array
    {
        $tasks = $this->getAll();

        usort($tasks, function (Task $taskA, Task $taskB) use ($orderBy): int {
            if ($taskA->getCreatedAt() === $taskB->getCreatedAt()) {
                return 0;
            }
            if ('asc' === $orderBy) {
                return ($taskA->getCreatedAt() < $taskB->getCreatedAt()) ? -1 : 1;
            }

            return ($taskA->getCreatedAt() > $taskB->getCreatedAt()) ? -1 : 1;
        });

        $offset = ($page - 1) * $perPage;

        return array_slice($tasks, $offset, $perPage, false);
    }

    /**
     * @param int    $page
     * @param int    $perPage
     * @param string $orderBy
     * @return array
     */
    public function getPageSortByName(int $page, int $perPage, string $orderBy = 'asc'): array
    {
        $tasks = $this->getAll();

        usort($tasks, function (Task $taskA, Task $taskB) use ($orderBy): int {
            if ($taskA->getUserName() === $taskB->getUserName()) {
                return 0;
            }
            if ('asc' === $orderBy) {
                return strcmp($taskA->getUserName(), $taskB->getUserName());
            }

            return strcmp($taskA->getUserName(), $taskB->getUserName()) * -1;
        });

        $offset = ($page - 1) * $perPage;

        return array_slice($tasks, $offset, $perPage, false);
    }

    /**
     * @param int    $page
     * @param int    $perPage
     * @param string $orderBy
     * @return array
     */
    public function getPageSortByMail(int $page, int $perPage, string $orderBy = 'asc'): array
    {
        $tasks = $this->getAll();

        usort($tasks, function (Task $taskA, Task $taskB) use ($orderBy): int {
            if ($taskA->getUserEmail() === $taskB->getUserEmail()) {
                return 0;
            }
            if ('asc' === $orderBy) {
                return strcmp($taskA->getUserEmail(), $taskB->getUserEmail());
            }

            return strcmp($taskA->getUserEmail(), $taskB->getUserEmail()) * -1;
        });

        $offset = ($page - 1) * $perPage;

        return array_slice($tasks, $offset, $perPage, false);
    }

    /**
     * @param Task $task
     * @throws Exception
     */
    public function persist(Task $task): void
    {
        if (!$task->hasId()) {
            $task = $task->withId(uniqid());
        }

        $tasks = $this->getStorage();
        $tasks[$task->getId()] = [
            'id'          => $task->getId(),
            'created_at'  => $task->getCreatedAt(),
            'status'      => $task->getStatus(),
            'user_name'   => $task->getUserName(),
            'user_email'  => $task->getUserEmail(),
            'description' => $task->getDescription(),
        ];

        $json = json_encode($tasks, JSON_UNESCAPED_UNICODE);

        if (!file_put_contents($this->storage, $json, LOCK_EX)) {
            throw new Exception('Could not store a new task!');
        }
    }
}
