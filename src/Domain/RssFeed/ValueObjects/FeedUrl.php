<?php

declare(strict_types=1);

namespace Populytics\Domain\RssFeed\ValueObjects;

use InvalidArgumentException;

final readonly class FeedUrl
{
    public function __construct(
        public string $value
    ) {
        if (empty($value)) {
            throw new InvalidArgumentException('Feed URL cannot be empty.');
        }

        if (! filter_var($value, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Feed URL must be a valid URL.');
        }

        // Additional validation to ensure it's an HTTP/HTTPS URL
        $scheme = parse_url($value, PHP_URL_SCHEME);
        if (! in_array($scheme, ['http', 'https'], true)) {
            throw new InvalidArgumentException('Feed URL must use HTTP or HTTPS protocol.');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(FeedUrl $other): bool
    {
        return $this->value === $other->value;
    }
}
