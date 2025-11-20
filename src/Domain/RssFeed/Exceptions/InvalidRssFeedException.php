<?php

declare(strict_types=1);

namespace Populytics\Domain\RssFeed\Exceptions;

use RuntimeException;

final class InvalidRssFeedException extends RuntimeException
{
    public static function invalidFormat(string $url): self
    {
        return new self("Invalid RSS feed format for URL: {$url}");
    }

    public static function unableToFetch(string $url): self
    {
        return new self("Unable to fetch RSS feed from URL: {$url}");
    }
}
