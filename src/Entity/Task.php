<?php
declare(strict_types=1);

namespace App\Entity;

/**
 * Class Task
 *
 * @package App\Entity
 */
class Task
{
    public const TASK_STATUS_NEW = 0;
    public const TASK_STATUS_DONE = 1;

    /** @var mixed */
    private $taskId;

    /** @var integer */
    private $createdAt = 0;

    /** @var integer */
    private $status = 0;

    /** @var string */
    private $userName = '';

    /** @var string */
    private $userEmail = '';

    /** @var string */
    private $description = '';

    /**
     * Task constructor.
     *
     * @param string|null $taskId
     */
    public function __construct(string $taskId = null)
    {
        $this->taskId = $taskId;
    }

    /**
     * @return bool
     */
    public function hasId(): bool
    {
        return $this->taskId !== null;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->taskId;
    }

    /**
     * @param mixed $taskId
     * @return Task
     */
    public function withId($taskId): Task
    {
        $new = clone $this;
        $new->taskId = $taskId;

        return $new;
    }

    /**
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    /**
     * @param int $createdAt
     * @return Task
     */
    public function setCreatedAt(int $createdAt): Task
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return Task
     */
    public function setStatus(int $status): Task
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @param string $userName
     * @return Task
     */
    public function setUserName(string $userName): Task
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserEmail(): string
    {
        return $this->userEmail;
    }

    /**
     * @param string $userEmail
     * @return Task
     */
    public function setUserEmail(string $userEmail): Task
    {
        $this->userEmail = $userEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Task
     */
    public function setDescription(string $description): Task
    {
        $this->description = $description;

        return $this;
    }
}
