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

        $events = $task->pullDomainEvents();

        $this->taskRepository->save($task);
        $this->domainEventDispatcher->dispatchAll($events);

        return $task;
    }
}