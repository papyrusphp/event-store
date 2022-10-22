# 📜 Papyrus Event Store
[![Build Status](https://scrutinizer-ci.com/g/papyrusphp/event-store/badges/build.png?b=main)](https://github.com/papyrusphp/event-store/actions)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/papyrusphp/event-store.svg?style=flat)](https://scrutinizer-ci.com/g/papyrusphp/event-store/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/papyrusphp/event-store.svg?style=flat)](https://scrutinizer-ci.com/g/papyrusphp/event-store)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/papyrus/event-store.svg?style=flat&include_prereleases)](https://packagist.org/packages/papyrus/event-store)
[![PHP Version](https://img.shields.io/badge/php-%5E8.1-8892BF.svg?style=flat)](http://www.php.net)

Event store interface for [papyrus/event-sourcing](https://github.com/papyrusphp/event-sourcing).

## Installation
This library contains a set of interfaces (the contract) for an event store.
Therefore, you will need to install an existing event store implementation or build your own one.

Available implementations:
- [papyrus/doctrine-dbal-event-store](https://github.com/papyrusphp/doctrine-dbal-event-store) - using [doctrine/dbal](https://github.com/doctrine/dbal)

_Follow the installation instructions of the chosen implementation first._

### Other optional packages
When using the optional `EventSourcedAggregateRootRepository`, some other libraries are required as well:
- A [papyrus/identity-generator](https://github.com/papyrusphp/identity-generator) implementation, e.g. [papyrus/ramsey-uuid-identity-generator](https://github.com/papyrusphp/ramsey-uuid-identity-generator)
- The [papyrus/clock](https://github.com/papyrusphp/clock) implementation (future PSR-20)

## Configuration
A plain PHP PSR-11 Container definition:
```php
use Papyrus\EventStore\Clock\Clock;
use Papyrus\EventStore\EventStore\EventStore;
use Papyrus\IdentityGenerator\IdentityGenerator;
use Papyrus\EventStore\Repository\AggregateRootRepository;
use Papyrus\EventStore\Repository\EventSourced\EventSourcedAggregateRootRepository;
use Psr\Container\ContainerInterface;

return [
    // Other definitions
    // ...

    EventStore::class => static function (): EventStore {
        // See papyrus/event-store implementation for more details
    },

    AggregateRootRepository::class => static function (ContainerInterface $container): AggregateRootRepository {
        return new EventSourcedAggregateRootRepository(
            $container->get(EventStore::class),
            // See papyrus/identity-generator for more details
            $container->get(IdentityGenerator::class),
            // See papyrus/clock for more details
            $container->get(Clock::class),
        );
    },
]
```
A Symfony YAML-file definition:
```yaml
services:
  _defaults:
    autowire: true
    autoconfigure: true

  # Other definitions
  # ...

  Papyrus\EventStore\EventStore\EventStore:
    # See papyrus/event-store implementation for more details

  Papyrus\EventStore\Repository\AggregateRootRepository:
    class: Papyrus\EventStore\Repository\EventSourced\EventSourcedAggregateRootRepository
```

## How to use
Typically, every aggregate root has its own repository. In an event sourced setup,
this repository will get and save the aggregate root into the event store. It will also dispatch
all events to an event bus, so that projectors can update all read models.

Using an implementation of `EventStore` you can create a repository on your own,
to fit your needs.

To make your life easier, this library contains an `AggregateRootRepository`,
which handles all event store logic for you. You can inject this repository to your domain specific
aggregate root repository.

Your **simplified** example repository could be (using the `AggregateRootRepository`):
```php
use Papyrus\EventStore\Repository\AggregateRootRepository;

final class YourImplAggregateRootRepository implements YourAggregateRootRepository
{
    /**
     * @param AggregateRootRepository<YourAggregateRoot, YourDomainEventInterface> $repository
     */
    public function __construct(
        private readonly AggregateRootRepository $repository,
        private readonly AnEventBus $eventBus // Or an outbox-pattern repository
    ) {
    }

    // A simplified implementation of the get method
    public function get(YourAggregateRootId $yourAggregateRootId): YourAggregateRoot
    {    
        try {
            return $this->repository->get(
                (string) $yourAggregateRootId,
                static function (array $domainEvents): YourAggregateRoot {
                    // Your custom solution to reconstitute the aggregate root
                    return YourAggregateRoot::reconstituteFromEvents($domainEvents);
                },
            );
        } catch (AggregateRootNotFoundException) {
            // Your aggregate root domain specific exception
            throw YourAggregateRootNotFoundException::withAggregateRootId($yourAggregateRootId);        
        }
    }
    
    // A simplified implementation of the save method
    // Note: when using the outbox-pattern, the whole save() method can be made atomic (e.g. SQL transactions)
    public function save(YourAggregateRoot $yourAggregateRoot): void
    {
        // Persist in event store via the available event repository 
        $this->repository->save(
            (string) $yourAggregateRoot->getAggregateRootId(),
            $yourAggregateRoot->getPlayhead(),
            $yourAggregateRoot->getAppliedEvents(),
        );

        // Dispatch all applied events to the event bus so that projectors can update read models
        foreach ($yourAggregateRoot->getAppliedEvents() as $event) {
            $this->eventBus->dispatch($event);
        }
        
        // Remove all applied events so that a new save() will not append again
        $yourAggregateRoot->clearAppliedEvents();
    }
}
```
