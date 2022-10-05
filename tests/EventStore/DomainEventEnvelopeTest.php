<?php

declare(strict_types=1);

namespace Papyrus\EventStore\Test\EventStore;

use DateTimeImmutable;
use Papyrus\EventStore\EventStore\DomainEventEnvelope;
use Papyrus\EventStore\EventStore\Metadata;
use Papyrus\EventStore\Test\EventStore\Stub\TestDomainEvent;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class DomainEventEnvelopeTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldAllowUpdatingMetadata(): void
    {
        $envelope = new DomainEventEnvelope(
            '3e43f3aa-6e0a-416f-b10d-5b2aab9bbc6c',
            new TestDomainEvent(),
            1,
            new DateTimeImmutable(),
            (new Metadata())->withMetadata('param', 'yes'),
        );

        $changedEnvelope = $envelope->withMetadata('anotherParam', ['test' => true]);

        self::assertSame(['param' => 'yes'], $envelope->metadata->jsonSerialize());
        self::assertSame(['param' => 'yes', 'anotherParam' => ['test' => true]], $changedEnvelope->metadata->jsonSerialize());
    }
}
