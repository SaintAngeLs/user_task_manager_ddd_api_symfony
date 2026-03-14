<?php

declare(strict_types=1);

namespace App\Application\Task\Factory;

use App\Domain\Task\Entity\Task;
use App\Domain\Task\ValueObject\TaskId;
use App\Domain\Task\ValueObject\TaskStatus;
use App\Domain\User\ValueObject\UserId;
use DateTimeImmutable;
use InvalidArgumentException;

final class TaskFactory
{
    public function create(
        string $title,
        string $description,
        string $assignedUserId,
        string $status = TaskStatus::TODO,
    ): Task {
        if (trim($title) === '') {
            throw new InvalidArgumentException('Task title cannot be empty.');
        }

        if (trim($assignedUserId) === '') {
            throw new InvalidArgumentException('AssignedUserId cannot be empty.');
        }

        return new Task(
            id:             TaskId::generate(),
            title:          trim($title),
            description:    trim($description),
            status:         TaskStatus::fromString($status),
            assignedUserId: UserId::fromString($assignedUserId),
            createdAt:      new DateTimeImmutable(),
        );
    }

    public function reconstitute(
        string $id,
        string $title,
        string $description,
        string $status,
        string $assignedUserId,
        DateTimeImmutable $createdAt,
    ): Task {
        return new Task(
            id:             TaskId::fromString($id),
            title:          $title,
            description:    $description,
            status:         TaskStatus::fromString($status),
            assignedUserId: UserId::fromString($assignedUserId),
            createdAt:      $createdAt,
        );
    }
}