<?php

declare(strict_types=1);

namespace Papyrus\EventStore\EventStore;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * @implements IteratorAggregate<string, mixed>
 */
final class Metadata implements IteratorAggregate, Countable, JsonSerializable
{
    /**
     * @var array<string, mixed>
     */
    private array $data = [];

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromPayload(array $payload): self
    {
        $metadata = new self();
        foreach ($payload as $key => $value) {
            $metadata = $metadata->withMetadata($key, $value);
        }

        return $metadata;
    }

    public function withMetadata(string $key, mixed $value): self
    {
        $metadata = clone $this;
        $metadata->data[$key] = $value;

        return $metadata;
    }

    public function getDataByKey(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->data);
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->data;
    }
}
