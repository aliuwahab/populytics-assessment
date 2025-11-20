<?php

declare(strict_types=1);

namespace Populytics\Domain\RssFeed\DomainServices;

use Populytics\Domain\RssFeed\ValueObjects\FeedUrl;

interface RssFeedValidatorInterface
{
    /**
     * Validates if the given URL points to a valid RSS feed.
     *
     * @param FeedUrl $url The feed URL to validate
     * @return bool True if valid RSS feed, false otherwise
     * @throws \RuntimeException If the feed cannot be accessed or parsed
     */
    public function isValidRssFeed(FeedUrl $url): bool;
}
