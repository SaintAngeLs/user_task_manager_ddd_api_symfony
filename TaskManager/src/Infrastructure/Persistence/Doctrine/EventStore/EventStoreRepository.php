<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\EventStore;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final class EventStoreRepository
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function append(
        string $aggregateId,
        string $eventType,
        array $payload,
        DateTimeImmutable $occurredAt,
    ): void {
        $event = new StoredEvent(
            aggregateId: $aggregateId,
            eventType: $eventType,
            payload: $payload,
            occurredAt: $occurredAt,
        );

        $this->em->persist($event);
        $this->em->flush();
    }

    /** @return StoredEvent[] */
    public function findByAggregateId(string $aggregateId): array
    {
        return $this->em
            ->getRepository(StoredEvent::class)
            ->findBy(
                ['aggregateId' => $aggregateId],
                ['occurredAt' => 'ASC'],
            );
    }
}