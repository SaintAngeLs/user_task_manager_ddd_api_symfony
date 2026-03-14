<?php

declare(strict_types=1);

namespace App\Domain\Task\Event;

use DateTimeImmutable;

final class TaskCreatedEvent
{
    public function __construct(
        public readonly string $taskId,
        public readonly string $title,
        public readonly string $status,
        public readonly string $assignedUserId,
        public readonly DateTimeImmutable $occurredAt,
    ) {
    }
}