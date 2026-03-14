<?php

declare(strict_types=1);

namespace App\Domain\Task\Event;

use DateTimeImmutable;

final class TaskStatusUpdatedEvent
{
    public function __construct(
        public readonly string            $taskId,
        public readonly string            $previousStatus,
        public readonly string            $newStatus,
        public readonly DateTimeImmutable $occurredAt,
    ) {}
}