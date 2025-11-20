<?php

declare(strict_types=1);

namespace Populytics\Domain\RssFeed\ValueObjects;

use InvalidArgumentException;

final readonly class EntryId
{
    public function __construct(
        public string $value
    ) {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('Entry ID cannot be empty.');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(EntryId $other): bool
    {
        return $this->value === $other->value;
    }
}
