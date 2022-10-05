<?php

declare(strict_types=1);

namespace Papyrus\EventStore\EventStore;

use Generator;
use Papyrus\EventSourcing\AggregateRootId;

interface EventStore
{
    /**
     * @throws AggregateRootNotFoundException
     * @throws EventStoreFailedException
     *
     * @return Generator<DomainEventEnvelope>
     */
    public function load(AggregateRootId $aggregateRootId, int $playhead = 0): Generator;

    /**
     * @throws EventStoreFailedException
     */
    public function append(AggregateRootId $aggregateRootId, DomainEventEnvelope ...$envelopes): void;
}
