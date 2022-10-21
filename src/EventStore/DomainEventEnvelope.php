<?php

declare(strict_types=1);

namespace Papyrus\EventStore\EventStore;

use DateTimeImmutable;

/**
 * @template DomainEvent of object
 */
final class DomainEventEnvelope
{
    /**
     * @param DomainEvent $event
     */
    public function __construct(
        public readonly string $eventId,
        public readonly object $event,
        public readonly int $playhead,
        public readonly DateTimeImmutable $appliedAt,
        public readonly Metadata $metadata,
    ) {
    }

    /**
     * @return self<DomainEvent>
     */
    public function withMetadata(string $key, mixed $value): self
    {
        return new self(
            $this->eventId,
            $this->event,
            $this->playhead,
            $this->appliedAt,
            $this->metadata->withMetadata($key, $value),
        );
    }
}
