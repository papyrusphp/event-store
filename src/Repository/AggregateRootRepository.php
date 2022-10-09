<?php

declare(strict_types=1);

namespace Papyrus\EventStore\Repository;

use Papyrus\EventSourcing\AggregateRoot;
use Papyrus\EventSourcing\AggregateRootId;
use Papyrus\EventStore\EventStore\AggregateRootNotFoundException;

interface AggregateRootRepository
{
    /**
     * @param class-string<AggregateRoot> $aggregateRootClassName
     *
     * @throws AggregateRootNotFoundException
     */
    public function get(string $aggregateRootClassName, AggregateRootId $aggregateRootId): AggregateRoot;

    public function save(AggregateRoot $aggregateRoot): void;
}
