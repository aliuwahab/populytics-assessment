<?php

declare(strict_types=1);

namespace Populytics\Domain\RssFeed\ValueObjects;

use InvalidArgumentException;

final readonly class FeedName
{
    public function __construct(
        public string $value
    ) {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('Feed name cannot be empty.');
        }

        if (mb_strlen($value) > 255) {
            throw new InvalidArgumentException('Feed name cannot exceed 255 characters.');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
