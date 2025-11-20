<?php

declare(strict_types=1);

namespace Populytics\Application\RssFeed\Services;

use Populytics\Application\RssFeed\DTOs\RssFeedEntryDTO;
use Populytics\Domain\RssFeed\ValueObjects\FeedUrl;

interface RssFeedParserInterface
{
    /**
     * Fetches and parses an RSS feed from the given URL.
     *
     * @param FeedUrl $url The URL of the RSS feed
     * @return array<RssFeedEntryDTO> Array of feed entries
     * @throws \App\Domain\RssFeed\Exceptions\InvalidRssFeedException
     */
    public function parseFeed(FeedUrl $url): array;
}
