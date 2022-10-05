<?php

declare(strict_types=1);

namespace Papyrus\EventStore\Test\Repository\EventSourced\Stub;

use Papyrus\EventSourcing\DomainEvent;

final class TestAggregateRootEvent implements DomainEvent
{
    public function __construct(
        public readonly string $aggregateRootId,
    ) {
    }

    public static function getEventName(): string
    {
        return 'test.event-name';
    }

    public function getAggregateRootId(): string
    {
        return $this->aggregateRootId;
    }
}
