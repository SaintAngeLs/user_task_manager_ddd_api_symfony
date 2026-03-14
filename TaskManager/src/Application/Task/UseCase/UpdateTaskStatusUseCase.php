<?php

declare(strict_types=1);

namespace App\Application\Task\UseCase;

use App\Application\Task\Strategy\TaskStatusContext;
use App\Domain\Shared\Event\DomainEventDispatcherInterface;
use App\Domain\Task\Entity\Task;
use App\Domain\Task\Repository\TaskRepositoryInterface;
use App\Domain\Task\ValueObject\TaskId;
use App\Domain\Task\ValueObject\TaskStatus;
use App\Infrastructure\Security\AuthorizationService;
use RuntimeException;

final class UpdateTaskStatusUseCase
{
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository,
        private readonly TaskStatusContext $taskStatusContext,
        private readonly AuthorizationService $authorizationService,
        private readonly DomainEventDispatcherInterface $domainEventDispatcher,
    ) {
    }

    public function execute(string $taskId, string $targetStatus): Task
    {
        $task = $this->taskRepository->findById(TaskId::fromString($taskId));

        if ($task === null) {
            throw new RuntimeException(sprintf('Task "%s" not found.', $taskId));
        }

        if (!$this->authorizationService->canManageTaskAssignedTo($task->getAssignedUserId()->getValue())) {
            throw new RuntimeException('Forbidden.');
        }

        $this->taskStatusContext->transition($task, TaskStatus::fromString($targetStatus));

        $this->taskRepository->save($task);

        $events = $task->pullDomainEvents();

        try {
            $this->domainEventDispatcher->dispatchAll($events);
        } catch (\Throwable $exception) {
            // Events dispatch failure should not invalidate the already-persisted task update.
            error_log(sprintf(
                'Failed to dispatch domain events for task "%s": %s',
                $taskId,
                $exception->getMessage()
            ));
        }

        return $task;
    }
}