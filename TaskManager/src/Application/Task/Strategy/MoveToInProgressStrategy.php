<?php

declare(strict_types=1);

namespace App\Application\Task\Strategy;

use App\Domain\Task\Entity\Task;
use App\Domain\Task\Strategy\TaskStatusStrategyInterface;
use App\Domain\Task\ValueObject\TaskStatus;
use DomainException;

final class MoveToInProgressStrategy implements TaskStatusStrategyInterface
{
    public function supports(TaskStatus $currentStatus): bool
    {
        return $currentStatus->isTodo();
    }

    public function apply(Task $task): void
    {
        if (!$this->supports($task->getStatus())) {
            throw new DomainException(
                sprintf(
                    'Cannot move task to "in_progress" from status "%s".',
                    $task->getStatus()->getValue()
                )
            );
        }

        $task->changeStatus(TaskStatus::inProgress());
    }
}