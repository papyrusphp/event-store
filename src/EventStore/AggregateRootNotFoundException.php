<?php

declare(strict_types=1);

namespace Papyrus\EventStore\EventStore;

use Exception;
use Papyrus\EventSourcing\AggregateRootId;

final class AggregateRootNotFoundException extends Exception
{
    public static function withAggregateRootId(AggregateRootId $aggregateRootId): self
    {
        return new self(sprintf('Aggregate root not found with ID `%s`', $aggregateRootId));
    }
}
