<?php

declare(strict_types=1);

namespace Papyrus\EventStore\Test\Repository\EventSourced\Stub;

use DateTimeImmutable;
use Psr\Clock\ClockInterface;

final class FixedClock implements ClockInterface
{
    private DateTimeImmutable $dateTime;

    public function setTestNow(DateTimeImmutable $dateTime): void
    {
        $this->dateTime = $dateTime;
    }

    public function now(): DateTimeImmutable
    {
        return $this->dateTime;
    }
}
