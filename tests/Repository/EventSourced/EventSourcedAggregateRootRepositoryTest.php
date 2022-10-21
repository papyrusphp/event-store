<?php

declare(strict_types=1);

namespace Papyrus\EventStore\Test\Repository\EventSourced;

use DateTimeImmutable;
use DateTimeInterface;
use Generator;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Papyrus\EventStore\EventStore\DomainEventEnvelope;
use Papyrus\EventStore\EventStore\EventStore;
use Papyrus\EventStore\EventStore\Metadata;
use Papyrus\EventStore\Repository\EventSourced\EventSourcedAggregateRootRepository;
use Papyrus\EventStore\Test\Repository\EventSourced\Stub\FixedClock;
use Papyrus\EventStore\Test\Repository\EventSourced\Stub\TestAggregateRoot;
use Papyrus\EventStore\Test\Repository\EventSourced\Stub\TestAggregateRootEvent;
use Papyrus\EventStore\Test\Repository\EventSourced\Stub\TestAnotherAggregateRootEvent;
use Papyrus\IdentityGenerator\IdentityGenerator;

/**
 * @internal
 */
class EventSourcedAggregateRootRepositoryTest extends MockeryTestCase
{
    /**
     * @var MockInterface&EventStore<object>
     */
    private MockInterface $eventStore;

    /**
     * @var MockInterface&IdentityGenerator
     */
    private MockInterface $identityGenerator;

    /**
     * @var EventSourcedAggregateRootRepository<TestAggregateRoot, object>
     */
    private EventSourcedAggregateRootRepository $repository;

    private FixedClock $clock;

    protected function setUp(): void
    {
        /** @var EventSourcedAggregateRootRepository<TestAggregateRoot, object> $repository */
        $repository = new EventSourcedAggregateRootRepository(
            $this->eventStore = Mockery::mock(EventStore::class),
            $this->identityGenerator = Mockery::mock(IdentityGenerator::class),
            $this->clock = new FixedClock(),
        );

        $this->repository = $repository;

        parent::setUp();
    }

    /**
     * @test
     */
    public function itShouldGetAggregateRoot(): void
    {
        $aggregateRootId = '2d1688f2-301a-4e7b-be4c-a6b0daa81a33';

        $this->eventStore->expects('load')->andReturn((function (): Generator {
            yield from [
                new DomainEventEnvelope(
                    '581e4fea-cddf-4c88-a149-84228ac02cec',
                    new TestAggregateRootEvent('2d1688f2-301a-4e7b-be4c-a6b0daa81a33'),
                    1,
                    new DateTimeImmutable('2022-10-05 12:00:00'),
                    new Metadata(),
                ),
            ];
        })());

        $aggregateRoot = $this->repository->get(
            $aggregateRootId,
            static fn (array $domainEvents): TestAggregateRoot => TestAggregateRoot::reconstituteFromEvents($domainEvents),
        );

        self::assertInstanceOf(TestAggregateRoot::class, $aggregateRoot);
        self::assertSame('2d1688f2-301a-4e7b-be4c-a6b0daa81a33', (string) $aggregateRoot->getAggregateRootId());
        self::assertSame(1, $aggregateRoot->playhead);
    }

    /**
     * @test
     */
    public function itShouldSaveAppliedEvents(): void
    {
        $this->clock->setTestNow(new DateTimeImmutable('2022-10-05 12:00:00'));

        $aggregateRootId = '2d1688f2-301a-4e7b-be4c-a6b0daa81a33';
        $aggregateRoot = TestAggregateRoot::create($aggregateRootId, 10);
        $aggregateRoot->anotherBehavior();

        $this->identityGenerator->allows('generateId')->once()->andReturn('921b1539-6fc8-47d7-838b-f320af6348c9');
        $this->identityGenerator->allows('generateId')->once()->andReturn('e48267d6-2f43-4bc5-8200-418709b319b1');

        $this->eventStore->expects('append')->with(
            $aggregateRootId,
            Mockery::on(function (array $envelopes): bool {
                self::assertCount(2, $envelopes);
                $envelope1 = $envelopes[0];
                self::assertInstanceOf(DomainEventEnvelope::class, $envelope1);
                self::assertSame('921b1539-6fc8-47d7-838b-f320af6348c9', $envelope1->eventId);
                self::assertInstanceOf(TestAggregateRootEvent::class, $envelope1->event);
                self::assertSame(11, $envelope1->playhead);
                self::assertSame('2022-10-05T12:00:00+00:00', $envelope1->appliedAt->format(DateTimeInterface::ATOM));

                $envelope2 = $envelopes[1];
                self::assertInstanceOf(DomainEventEnvelope::class, $envelope2);
                self::assertSame('e48267d6-2f43-4bc5-8200-418709b319b1', $envelope2->eventId);
                self::assertInstanceOf(TestAnotherAggregateRootEvent::class, $envelope2->event);
                self::assertSame(12, $envelope2->playhead);
                self::assertSame('2022-10-05T12:00:00+00:00', $envelope2->appliedAt->format(DateTimeInterface::ATOM));

                return true;
            }),
        );

        $this->repository->save(
            $aggregateRootId,
            $aggregateRoot->playhead,
            $aggregateRoot->appliedEvents,
        );
    }
}
