<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\EventHandler;

use App\Domain\Task\Event\TaskCreatedEvent;
use App\Infrastructure\Persistence\Doctrine\EventStore\EventStoreRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class TaskCreatedEventHandler
{
    public function __construct(
        private readonly EventStoreRepository $eventStoreRepository,
    ) {
    }

    public function __invoke(TaskCreatedEvent $event): void
    {
        $this->eventStoreRepository->append(
            aggregateId: $event->taskId,
            eventType: TaskCreatedEvent::class,
            payload: [
                'taskId' => $event->taskId,
                'title' => $event->title,
                'status' => $event->status,
                'assignedUserId' => $event->assignedUserId,
            ],
            occurredAt: $event->occurredAt,
        );
    }
}