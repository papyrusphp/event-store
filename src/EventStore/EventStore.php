<?php

declare(strict_types=1);

namespace Papyrus\EventStore\EventStore;

use Generator;

/**
 * @template DomainEvent of object
 */
interface EventStore
{
    /**
     * @throws AggregateRootNotFoundException
     * @throws EventStoreFailedException
     *
     * @return Generator<DomainEventEnvelope<DomainEvent>>
     */
    public function load(string $aggregateRootId, int $playhead = 0): Generator;

    /**
     * @param array<DomainEventEnvelope<DomainEvent>> $envelopes
     *
     * @throws EventStoreFailedException
     */
    public function append(string $aggregateRootId, array $envelopes): void;
}
