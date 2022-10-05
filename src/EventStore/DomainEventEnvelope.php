<?php

declare(strict_types=1);

namespace Papyrus\EventStore\EventStore;

use DateTimeImmutable;
use Papyrus\EventSourcing\DomainEvent;

final class DomainEventEnvelope
{
    public function __construct(
        public readonly string $eventId,
        public readonly DomainEvent $event,
        public readonly int $playhead,
        public readonly DateTimeImmutable $appliedAt,
        public readonly Metadata $metadata,
    ) {
    }

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
