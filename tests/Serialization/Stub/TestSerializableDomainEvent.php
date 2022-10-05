<?php

declare(strict_types=1);

namespace Papyrus\EventStore\Test\Serialization\Stub;

use Papyrus\EventStore\Serialization\SerializableDomainEvent;

final class TestSerializableDomainEvent implements SerializableDomainEvent
{
    public function __construct(
        public readonly string $param,
    ) {
    }

    public static function getEventName(): string
    {
        return 'test.serializable_domain_event';
    }

    public function getAggregateRootId(): string
    {
        return 'c85d5ce4-bcd6-4192-8d1e-405d7990a7cd';
    }

    public function serialize(): mixed
    {
        return ['param' => $this->param];
    }

    /**
     * @param array{param: string} $payload
     */
    public static function deserialize(mixed $payload): SerializableDomainEvent
    {
        return new self($payload['param']);
    }
}
