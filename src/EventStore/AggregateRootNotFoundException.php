<?php

declare(strict_types=1);

namespace Papyrus\EventStore\EventStore;

use Exception;

final class AggregateRootNotFoundException extends Exception
{
    public static function withAggregateRootId(string $aggregateRootId): self
    {
        return new self(sprintf('Aggregate root not found with ID `%s`', $aggregateRootId));
    }
}
