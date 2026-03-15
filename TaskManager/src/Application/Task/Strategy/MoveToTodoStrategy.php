<?php

declare(strict_types=1);

namespace App\Application\Task\Strategy;

use App\Domain\Task\Entity\Task;
use App\Domain\Task\Strategy\TaskStatusStrategyInterface;
use App\Domain\Task\ValueObject\TaskStatus;

final class MoveToTodoStrategy implements TaskStatusStrategyInterface
{
    public function supports(TaskStatus $currentStatus, TaskStatus $targetStatus): bool
    {
        return !$currentStatus->equals($targetStatus) && $targetStatus->isTodo();
    }

    public function apply(Task $task, TaskStatus $targetStatus): void
    {
        if (!$this->supports($task->getStatus(), $targetStatus)) {
            throw new \DomainException(sprintf('Cannot move task from "%s" to "%s".', $task->getStatus()->getValue(), $targetStatus->getValue()));
        }

        $task->changeStatus(TaskStatus::todo());
    }
}
