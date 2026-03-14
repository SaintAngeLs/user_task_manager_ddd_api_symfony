<?php

declare(strict_types=1);

namespace App\Application\Task\DTO;

final class TaskHistoryItem
{
    public function __construct(
        public readonly string $eventType,
        public readonly string $payload,
        public readonly string $occurredAt,
    ) {
    }
}