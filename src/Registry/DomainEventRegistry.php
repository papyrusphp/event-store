<?php

declare(strict_types=1);

namespace Papyrus\EventStore\Registry;

use Papyrus\EventSourcing\DomainEvent;

final class DomainEventRegistry
{
    /**
     * @var array<string, class-string<DomainEvent>>
     */
    private array $registeredEvents = [];

    /**
     * @param list<class-string<DomainEvent>> $eventClassNames
     */
    public function __construct(array $eventClassNames)
    {
        foreach ($eventClassNames as $eventClassName) {
            $this->registeredEvents[$eventClassName::getEventName()] = $eventClassName;
        }
    }

    /**
     * @throws EventNotRegisteredException
     *
     * @return class-string<DomainEvent>
     */
    public function retrieve(string $eventName): string
    {
        if (array_key_exists($eventName, $this->registeredEvents) === false) {
            throw EventNotRegisteredException::withEventName($eventName);
        }

        return $this->registeredEvents[$eventName];
    }
}
