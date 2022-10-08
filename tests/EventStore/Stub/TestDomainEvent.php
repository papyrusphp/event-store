<?php

declare(strict_types=1);

namespace Papyrus\EventStore\Test\EventStore\Stub;

use Papyrus\EventSourcing\DomainEvent;

final class TestDomainEvent implements DomainEvent
{
    public static function getEventName(): string
    {
        return 'test.domain_event';
    }

    public function getAggregateRootId(): string
    {
        return 'c85d5ce4-bcd6-4192-8d1e-405d7990a7cd';
    }
}
