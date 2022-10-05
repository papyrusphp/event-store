<?php

declare(strict_types=1);

namespace Papyrus\EventStore\Serialization;

use Papyrus\EventSourcing\DomainEvent;
use Papyrus\Serializer\Serializer;

/**
 * @implements Serializer<DomainEvent>
 */
final class SerializableDomainEventSerializerDecorator implements Serializer
{
    /**
     * @param Serializer<DomainEvent> $serializer
     */
    public function __construct(
        private readonly Serializer $serializer,
    ) {
    }

    public function serialize(object $object): mixed
    {
        if ($object instanceof SerializableDomainEvent) {
            return $object->serialize();
        }

        return $this->serializer->serialize($object);
    }

    public function deserialize(mixed $payload, string $objectClassName): DomainEvent
    {
        if (is_subclass_of($objectClassName, SerializableDomainEvent::class)) {
            return $objectClassName::deserialize($payload);
        }

        return $this->serializer->deserialize($payload, $objectClassName);
    }
}
