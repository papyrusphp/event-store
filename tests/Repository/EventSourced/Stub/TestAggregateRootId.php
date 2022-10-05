<?php

declare(strict_types=1);

namespace Papyrus\EventStore\Test\Repository\EventSourced\Stub;

use Papyrus\EventSourcing\AggregateRootId;

final class TestAggregateRootId implements AggregateRootId
{
    public function __construct(
        public readonly string $aggregateRootId,
    ) {
    }

    public function __toString(): string
    {
        return $this->aggregateRootId;
    }
}
