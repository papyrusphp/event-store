<?php

declare(strict_types=1);

namespace Papyrus\EventStore\Test\EventStore;

use Exception;
use Papyrus\EventStore\EventStore\EventStoreFailedException;
use Papyrus\EventStore\Test\EventStore\Stub\TestAggregateRootId;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class EventStoreFailedExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldCreateException(): void
    {
        $exception = EventStoreFailedException::withAggregateRootId(
            new TestAggregateRootId(),
            $previous = new Exception('Failed'),
        );

        self::assertSame(
            'Failed request to event store for aggregate root with ID `d22b83d3-2802-4f66-92e2-c20f551a7fa5`',
            $exception->getMessage(),
        );

        self::assertSame($previous, $exception->getPrevious());
    }
}
