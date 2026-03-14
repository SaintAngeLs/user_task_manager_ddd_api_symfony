<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger;

use App\Domain\Shared\Event\DomainEventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class SymfonyMessengerDomainEventDispatcher implements DomainEventDispatcherInterface
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function dispatchAll(array $events): void
    {
        foreach ($events as $event) {
            $this->messageBus->dispatch($event);
        }
    }
}