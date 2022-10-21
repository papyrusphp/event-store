<?php

declare(strict_types=1);

namespace Papyrus\EventStore\Test\Repository\EventSourced\Stub;

final class TestAggregateRoot
{
    public int $playhead = 0;

    /**
     * @var list<object>
     */
    public array $appliedEvents = [];

    private string $aggregateRootId;

    /**
     * @param list<object> $events
     */
    public static function reconstituteFromEvents(array $events): self
    {
        $aggregateRoot = new self();
        foreach ($events as $event) {
            $aggregateRoot->handleEvent($event);
        }

        $aggregateRoot->appliedEvents = [];

        return $aggregateRoot;
    }

    public static function create(string $aggregateRootId, int $startingPlayhead): self
    {
        $aggregateRoot = new self();
        $aggregateRoot->playhead = $startingPlayhead;
        $aggregateRoot->handleEvent(new TestAggregateRootEvent($aggregateRootId));

        return $aggregateRoot;
    }

    public function anotherBehavior(): void
    {
        $this->handleEvent(new TestAnotherAggregateRootEvent($this->aggregateRootId));
    }

    public function getAggregateRootId(): string
    {
        return $this->aggregateRootId;
    }

    private function handleEvent(object $event): void
    {
        $this->appliedEvents[] = $event;

        if ($event instanceof TestAggregateRootEvent) {
            $this->applyTestAggregateRootEvent($event);
        }

        ++$this->playhead;
    }

    private function applyTestAggregateRootEvent(TestAggregateRootEvent $event): void
    {
        $this->aggregateRootId = $event->aggregateRootId;
    }
}
