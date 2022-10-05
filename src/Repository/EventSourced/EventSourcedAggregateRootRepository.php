<?php

declare(strict_types=1);

namespace Papyrus\EventStore\Repository\EventSourced;

use Papyrus\Clock\Clock;
use Papyrus\EventSourcing\AggregateRoot;
use Papyrus\EventSourcing\AggregateRootId;
use Papyrus\EventSourcing\DomainEvent;
use Papyrus\EventSourcing\EventSourcedAggregateRoot;
use Papyrus\EventStore\EventStore\AggregateRootNotFoundException;
use Papyrus\EventStore\EventStore\DomainEventEnvelope;
use Papyrus\EventStore\EventStore\EventStore;
use Papyrus\EventStore\EventStore\EventStoreFailedException;
use Papyrus\EventStore\EventStore\Metadata;
use Papyrus\EventStore\Repository\AggregateRootRepository;
use Papyrus\IdentityGenerator\IdentityGenerator;

final class EventSourcedAggregateRootRepository implements AggregateRootRepository
{
    public function __construct(
        private readonly EventStore $eventStore,
        private readonly IdentityGenerator $identityGenerator,
        private readonly Clock $clock,
    ) {
    }

    /**
     * @param class-string<EventSourcedAggregateRoot> $aggregateRootClassName
     *
     * @throws AggregateRootNotFoundException
     * @throws EventStoreFailedException
     *
     * @return EventSourcedAggregateRoot
     */
    public function get(string $aggregateRootClassName, AggregateRootId $aggregateRootId): AggregateRoot
    {
        return $aggregateRootClassName::reconstituteFromEvents(...array_map(
            static fn (DomainEventEnvelope $envelope): DomainEvent => $envelope->event,
            iterator_to_array($this->eventStore->load($aggregateRootId)),
        ));
    }

    /**
     * @param EventSourcedAggregateRoot $aggregateRoot
     *
     * @throws EventStoreFailedException
     */
    public function save(AggregateRoot $aggregateRoot): void
    {
        $playhead = $aggregateRoot->getPlayhead() - count($aggregateRoot->getAppliedEvents());

        $this->eventStore->append($aggregateRoot->getAggregateRootId(), ...array_map(
            function (DomainEvent $event) use (&$playhead): DomainEventEnvelope {
                return new DomainEventEnvelope(
                    $this->identityGenerator->generateId(),
                    $event,
                    ++$playhead,
                    $this->clock->now(),
                    new Metadata(),
                );
            },
            $aggregateRoot->getAppliedEvents(),
        ));
    }
}
