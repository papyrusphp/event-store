<?php

declare(strict_types=1);

namespace Papyrus\EventStore\EventStore;

use Exception;
use Papyrus\EventSourcing\AggregateRootId;
use Throwable;

final class EventStoreFailedException extends Exception
{
    public static function withAggregateRootId(AggregateRootId $aggregateRootId, ?Throwable $previous): self
    {
        return new self(
            sprintf('Failed request to event store for aggregate root with ID `%s`', $aggregateRootId),
            (int) ($previous?->getCode() ?? 0),
            $previous,
        );
    }
}
