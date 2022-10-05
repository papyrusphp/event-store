<?php

declare(strict_types=1);

namespace Papyrus\EventStore\Test\Serialization;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Papyrus\EventSourcing\DomainEvent;
use Papyrus\EventStore\Serialization\SerializableDomainEventSerializerDecorator;
use Papyrus\EventStore\Test\EventStore\Stub\TestDomainEvent;
use Papyrus\EventStore\Test\Serialization\Stub\TestSerializableDomainEvent;
use Papyrus\Serializer\Serializer;

/**
 * @internal
 */
class SerializableDomainEventSerializerDecoratorTest extends MockeryTestCase
{
    private SerializableDomainEventSerializerDecorator $decorator;

    /**
     * @var MockInterface&Serializer<DomainEvent>
     */
    private MockInterface $serializer;

    protected function setUp(): void
    {
        $this->decorator = new SerializableDomainEventSerializerDecorator(
            $this->serializer = Mockery::mock(Serializer::class),
        );

        parent::setUp();
    }

    /**
     * @test
     */
    public function itShouldSerializeIfEventImplementsSerializableDomainEvent(): void
    {
        $this->serializer->expects('serialize')->never();

        $payload = $this->decorator->serialize(new TestSerializableDomainEvent('test'));

        self::assertSame(['param' => 'test'], $payload);
    }

    /**
     * @test
     */
    public function itShouldSerializeViaDecoratedSerializerWhenEventDoesNotImplementSerializableDomainEvent(): void
    {
        $event = new TestDomainEvent();

        $this->serializer->expects('serialize')
            ->with($event)
            ->andReturn(['serialized_by_decorated_serializer' => true])
        ;

        $payload = $this->decorator->serialize($event);

        self::assertSame(['serialized_by_decorated_serializer' => true], $payload);
    }

    /**
     * @test
     */
    public function itShouldDeserializeIfEventImplementsSerializableDomainEvent(): void
    {
        $this->serializer->expects('deserialize')->never();

        $event = $this->decorator->deserialize(['param' => 'test'], TestSerializableDomainEvent::class);

        self::assertInstanceOf(TestSerializableDomainEvent::class, $event);
        self::assertSame('test', $event->param);
    }

    /**
     * @test
     */
    public function itShouldDeserializeViaDecoratedSerializerWhenEventDoesNotImplementSerializableDomainEvent(): void
    {
        $this->serializer->expects('deserialize')
            ->with(['payload' => true], TestDomainEvent::class)
            ->andReturn($event = new TestDomainEvent())
        ;

        $deserializedEvent = $this->decorator->deserialize(['payload' => true], TestDomainEvent::class);

        self::assertSame($event, $deserializedEvent);
    }
}
