<?php

declare(strict_types=1);

namespace App\Domain\Task\Strategy;

use App\Domain\Task\Entity\Task;
use App\Domain\Task\ValueObject\TaskStatus;

interface TaskStatusStrategyInterface
{
    public function supports(TaskStatus $currentStatus): bool;

    public function apply(Task $task): void;
}