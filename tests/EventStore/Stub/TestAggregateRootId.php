<?php

declare(strict_types=1);

namespace Papyrus\EventStore\Test\EventStore\Stub;

use Papyrus\EventSourcing\AggregateRootId;

final class TestAggregateRootId implements AggregateRootId
{
    public function __toString(): string
    {
        return 'd22b83d3-2802-4f66-92e2-c20f551a7fa5';
    }
}
