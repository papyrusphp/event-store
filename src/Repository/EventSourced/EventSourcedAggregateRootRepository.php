<?php

declare(strict_types=1);

namespace Papyrus\EventStore\Repository\EventSourced;

use Papyrus\Clock\Clock;
use Papyrus\EventStore\EventStore\AggregateRootNotFoundException;
use Papyrus\EventStore\EventStore\DomainEventEnvelope;
use Papyrus\EventStore\EventStore\EventStore;
use Papyrus\EventStore\EventStore\EventStoreFailedException;
use Papyrus\EventStore\EventStore\Metadata;
use Papyrus\EventStore\Repository\AggregateRootRepository;
use Papyrus\IdentityGenerator\IdentityGenerator;

/**
 * @template AggregateRoot of object
 * @template DomainEvent of object
 *
 * @implements AggregateRootRepository<AggregateRoot, DomainEvent>
 */
final class EventSourcedAggregateRootRepository implements AggregateRootRepository
{
    /**
     * @param EventStore<DomainEvent> $eventStore
     */
    public function __construct(
        private readonly EventStore $eventStore,
        private readonly IdentityGenerator $identityGenerator,
        private readonly Clock $clock,
    ) {
    }

    /**
     * @throws AggregateRootNotFoundException
     * @throws EventStoreFailedException
     *
     * @return AggregateRoot
     */
    public function get(string $aggregateRootId, callable $reconstituteFromEvents): object
    {
        /** @var list<DomainEvent> $domainEvents */
        $domainEvents = array_map(
            static fn (DomainEventEnvelope $envelope): object => $envelope->event,
            iterator_to_array($this->eventStore->load($aggregateRootId)),
        );

        return $reconstituteFromEvents($domainEvents);
    }

    /**
     * @param list<DomainEvent> $appliedDomainEvents
     *
     * @throws EventStoreFailedException
     */
    public function save(string $aggregateRootId, int $currentPlayhead, array $appliedDomainEvents): void
    {
        $playhead = $currentPlayhead - count($appliedDomainEvents);

        $this->eventStore->append($aggregateRootId, array_map(
            function (object $event) use (&$playhead): DomainEventEnvelope {
                return new DomainEventEnvelope(
                    $this->identityGenerator->generateId(),
                    $event,
                    ++$playhead,
                    $this->clock->now(),
                    new Metadata(),
                );
            },
            $appliedDomainEvents,
        ));
    }
}
