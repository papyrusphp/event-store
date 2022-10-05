<?php

declare(strict_types=1);

namespace Papyrus\EventStore\Test\Repository\EventSourced\Stub;

use Papyrus\EventSourcing\AggregateRootId;
use Papyrus\EventSourcing\EventSourceableAggregateRootTrait;
use Papyrus\EventSourcing\EventSourcedAggregateRoot;

final class TestAggregateRoot implements EventSourcedAggregateRoot
{
    use EventSourceableAggregateRootTrait;

    private TestAggregateRootId $aggregateRootId;

    public static function create(TestAggregateRootId $aggregateRootId, int $startingPlayhead): self
    {
        $aggregateRoot = new self();
        $aggregateRoot->playhead = $startingPlayhead;
        $aggregateRoot->apply(new TestAggregateRootEvent((string) $aggregateRootId));

        return $aggregateRoot;
    }

    public function anotherBehavior(): void
    {
        $this->apply(new TestAnotherAggregateRootEvent((string) $this->aggregateRootId));
    }

    public function getAggregateRootId(): AggregateRootId
    {
        return $this->aggregateRootId;
    }

    protected function applyTestAggregateRootEvent(TestAggregateRootEvent $event): void
    {
        $this->aggregateRootId = new TestAggregateRootId($event->aggregateRootId);
    }
}
