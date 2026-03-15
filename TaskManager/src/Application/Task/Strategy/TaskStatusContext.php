<?php

declare(strict_types=1);

namespace App\Application\Task\Strategy;

use App\Domain\Task\Entity\Task;
use App\Domain\Task\Strategy\TaskStatusStrategyInterface;
use App\Domain\Task\ValueObject\TaskStatus;

final class TaskStatusContext
{
    /** @param iterable<TaskStatusStrategyInterface> $strategies */
    public function __construct(private readonly iterable $strategies)
    {
    }

    public function transition(Task $task, TaskStatus $targetStatus): void
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($task->getStatus(), $targetStatus)) {
                $strategy->apply($task, $targetStatus);

                return;
            }
        }

        throw new \DomainException(sprintf('No strategy found to transition from "%s" to "%s".', $task->getStatus()->getValue(), $targetStatus->getValue()));
    }
}
