<?php

declare(strict_types=1);

namespace Papyrus\EventStore\Test\Repository\EventSourced\Stub;

final class TestAggregateRootEvent
{
    public function __construct(
        public readonly string $aggregateRootId,
    ) {
    }

    public function getAggregateRootId(): string
    {
        return $this->aggregateRootId;
    }
}
