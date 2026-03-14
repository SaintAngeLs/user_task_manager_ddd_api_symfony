<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\EventStore;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'event_store')]
class StoredEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'aggregate_id', type: 'string', length: 255)]
    private string $aggregateId;

    #[ORM\Column(name: 'event_type', type: 'string', length: 255)]
    private string $eventType;

    #[ORM\Column(name: 'payload', type: 'json')]
    private array $payload;

    #[ORM\Column(name: 'occurred_at', type: 'datetime_immutable')]
    private DateTimeImmutable $occurredAt;

    public function __construct(
        string $aggregateId,
        string $eventType,
        array $payload,
        DateTimeImmutable $occurredAt,
    ) {
        $this->aggregateId = $aggregateId;
        $this->eventType = $eventType;
        $this->payload = $payload;
        $this->occurredAt = $occurredAt;
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getAggregateId(): string
    {
        return $this->aggregateId;
    }
    public function getEventType(): string
    {
        return $this->eventType;
    }
    public function getPayload(): array
    {
        return $this->payload;
    }
    public function getOccurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}