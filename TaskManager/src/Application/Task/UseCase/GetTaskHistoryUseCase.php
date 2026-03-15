<?php

declare(strict_types=1);

namespace App\Application\Task\UseCase;

use App\Application\Task\DTO\TaskHistoryItem;
use App\Domain\Task\Repository\TaskRepositoryInterface;
use App\Domain\Task\ValueObject\TaskId;
use App\Infrastructure\Persistence\Doctrine\EventStore\EventStoreRepository;
use App\Infrastructure\Security\AuthorizationService;
use RuntimeException;

final class GetTaskHistoryUseCase
{
    public function __construct(
        private readonly EventStoreRepository $eventStoreRepository,
        private readonly TaskRepositoryInterface $taskRepository,
        private readonly AuthorizationService $authorizationService,
    ) {
    }

    /** @return TaskHistoryItem[] */
    public function execute(string $taskId): array
    {
        $task = $this->taskRepository->findById(TaskId::fromString($taskId));

        if ($task === null) {
            throw new RuntimeException(sprintf('Task "%s" not found.', $taskId));
        }

        if (!$this->authorizationService->canAccessUserTasks($task->getAssignedUserId()->getValue())) {
            throw new RuntimeException('Forbidden.');
        }

        $events = $this->eventStoreRepository->findByAggregateId($taskId);

        return array_map(
            static fn($event) => new TaskHistoryItem(
                eventType: $event->getEventType(),
                payload: json_encode($event->getPayload(), JSON_THROW_ON_ERROR),
                occurredAt: $event->getOccurredAt()->format(DATE_ATOM),
            ),
            $events
        );
    }
}