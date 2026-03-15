<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\EventHandler;

use App\Domain\Task\Event\TaskStatusUpdatedEvent;
use App\Infrastructure\Persistence\Doctrine\EventStore\EventStoreRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class TaskStatusUpdatedEventHandler
{
    public function __construct(
        private readonly EventStoreRepository $eventStoreRepository,
    ) {
    }

    public function __invoke(TaskStatusUpdatedEvent $event): void
    {
        $this->eventStoreRepository->append(
            aggregateId: $event->taskId,
            eventType: TaskStatusUpdatedEvent::class,
            payload: [
                'taskId' => $event->taskId,
                'previousStatus' => $event->previousStatus,
                'newStatus' => $event->newStatus,
            ],
            occurredAt: $event->occurredAt,
        );
    }
}
