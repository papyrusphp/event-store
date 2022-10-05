<?php

declare(strict_types=1);

namespace Papyrus\EventStore\Test\Repository\EventSourced\Stub;

use DateTimeImmutable;
use Papyrus\Clock\Clock;

final class FixedClock implements Clock
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
