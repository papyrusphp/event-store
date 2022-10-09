<?php

declare(strict_types=1);

namespace Papyrus\EventStore\Test\EventStore;

use Exception;
use Papyrus\EventStore\EventStore\Metadata;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class MetadataTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldCreateFromPayload(): void
    {
        $metadata = Metadata::fromPayload([
            'some' => 'metadata',
            'key' => true,
            'integer' => 12,
        ]);

        self::assertSame([
            'some' => 'metadata',
            'key' => true,
            'integer' => 12,
        ], $metadata->getData());
    }

    /**
     * @test
     */
    public function itShouldAddExtraMetadataAfterInstantiationImmutable(): void
    {
        $metadata = Metadata::fromPayload([
            'some' => 'metadata',
            'key' => true,
            'integer' => 12,
        ]);

        $metadata2 = $metadata
            ->withMetadata('another', 'key')
            ->withMetadata('key', false)
        ;

        self::assertSame([
            'some' => 'metadata',
            'key' => true,
            'integer' => 12,
        ], $metadata->getData());

        self::assertSame([
            'some' => 'metadata',
            'key' => false,
            'integer' => 12,
            'another' => 'key',
        ], $metadata2->getData());
    }

    /**
     * @test
     */
    public function itShouldReturnCount(): void
    {
        $metadata = Metadata::fromPayload([
            'some' => 'metadata',
            'key' => true,
            'integer' => 12,
        ]);

        self::assertCount(3, $metadata);
    }

    /**
     * @test
     */
    public function itShouldGetDataByKey(): void
    {
        $metadata = Metadata::fromPayload([
            'some' => 'metadata',
            'key' => true,
            'integer' => 12,
        ]);

        self::assertSame('metadata', $metadata->getDataByKey('some'));
    }

    /**
     * @test
     */
    public function itShouldReturnNullForUnknownKeyWhenGetDataByKey(): void
    {
        $metadata = Metadata::fromPayload([
            'some' => 'metadata',
            'key' => true,
            'integer' => 12,
        ]);

        self::assertNull($metadata->getDataByKey('unknown'));
    }

    /**
     * @test
     */
    public function itShouldIterateOverMetadata(): void
    {
        $metadata = Metadata::fromPayload([
            'some' => 'metadata',
            'key' => true,
            'integer' => 12,
        ]);

        foreach ($metadata as $key => $item) {
            self::assertSame(match ($key) {
                'some' => 'metadata',
                'key' => true,
                'integer' => 12,
                default => throw new Exception(sprintf('Assertion failed, missing key `%s`', $key)),
            }, $item);
        }
    }
}
