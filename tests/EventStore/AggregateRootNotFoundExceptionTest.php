<?php

declare(strict_types=1);

namespace Papyrus\EventStore\Test\EventStore;

use Papyrus\EventStore\EventStore\AggregateRootNotFoundException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class AggregateRootNotFoundExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldCreateException(): void
    {
        $exception = AggregateRootNotFoundException::withAggregateRootId('d22b83d3-2802-4f66-92e2-c20f551a7fa5');

        self::assertSame(
            'Aggregate root not found with ID `d22b83d3-2802-4f66-92e2-c20f551a7fa5`',
            $exception->getMessage(),
        );
    }
}
