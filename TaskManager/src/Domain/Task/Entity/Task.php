<?php

declare(strict_types=1);

namespace App\Domain\Task\Entity;

use App\Domain\Task\Event\TaskCreatedEvent;
use App\Domain\Task\Event\TaskStatusUpdatedEvent;
use App\Domain\Task\ValueObject\TaskId;
use App\Domain\Task\ValueObject\TaskStatus;
use App\Domain\User\ValueObject\UserId;
use DateTimeImmutable;

class Task
{
    private TaskId            $id;
    private string            $title;
    private string            $description;
    private TaskStatus        $status;
    private UserId            $assignedUserId;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    /** @var object[] */
    private array $domainEvents = [];

    public function __construct(
        TaskId            $id,
        string            $title,
        string            $description,
        TaskStatus        $status,
        UserId            $assignedUserId,
        DateTimeImmutable $createdAt,
    ) {
        $this->id             = $id;
        $this->title          = $title;
        $this->description    = $description;
        $this->status         = $status;
        $this->assignedUserId = $assignedUserId;
        $this->createdAt      = $createdAt;
        $this->updatedAt      = $createdAt;

        $this->recordEvent(new TaskCreatedEvent(
            $id->getValue(),
            $title,
            $status->getValue(),
            $assignedUserId->getValue(),
            $createdAt,
        ));
    }

    public function changeStatus(TaskStatus $newStatus): void
    {
        $previous      = $this->status->getValue();
        $this->status  = $newStatus;
        $this->updatedAt = new DateTimeImmutable();

        $this->recordEvent(new TaskStatusUpdatedEvent(
            $this->id->getValue(),
            $previous,
            $newStatus->getValue(),
            $this->updatedAt,
        ));
    }

    public function getId(): TaskId
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStatus(): TaskStatus
    {
        return $this->status;
    }

    public function getAssignedUserId(): UserId
    {
        return $this->assignedUserId;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /** @return object[] */
    public function pullDomainEvents(): array
    {
        $events             = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }

    private function recordEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }
}