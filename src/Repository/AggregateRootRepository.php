<?php

declare(strict_types=1);

namespace Papyrus\EventStore\Repository;

use Papyrus\EventStore\EventStore\AggregateRootNotFoundException;

/**
 * @template AggregateRoot of object
 * @template DomainEvent of object
 */
interface AggregateRootRepository
{
    /**
     * @phpstan-param callable(list<DomainEvent>): AggregateRoot $reconstituteFromEvents
     *
     * @throws AggregateRootNotFoundException
     *
     * @return AggregateRoot
     */
    public function get(string $aggregateRootId, callable $reconstituteFromEvents): object;

    /**
     * @param list<DomainEvent> $appliedDomainEvents
     */
    public function save(string $aggregateRootId, int $currentPlayhead, array $appliedDomainEvents): void;
}
