<?php

declare(strict_types=1);

namespace App\Domain\Shared\Event;

interface DomainEventDispatcherInterface
{
    /** @param object[] $events */
    public function dispatchAll(array $events): void;
}
