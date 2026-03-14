<?php

declare(strict_types=1);

namespace App\Application\Task\Strategy;

use App\Domain\Task\Entity\Task;
use App\Domain\Task\Strategy\TaskStatusStrategyInterface;
use App\Domain\Task\ValueObject\TaskStatus;
use DomainException;

final class TaskStatusContext
{
    /** @param iterable<TaskStatusStrategyInterface> $strategies */
    public function __construct(private readonly iterable $strategies)
    {
    }

    public function transition(Task $task, TaskStatus $targetStatus): void
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($task->getStatus())) {
                $clone = clone $task;
                $strategy->apply($clone);

                if ($clone->getStatus()->equals($targetStatus)) {
                    $strategy->apply($task);
                    return;
                }
            }
        }

        throw new DomainException(
            sprintf(
                'No strategy found to transition from "%s" to "%s".',
                $task->getStatus()->getValue(),
                $targetStatus->getValue()
            )
        );
    }
}