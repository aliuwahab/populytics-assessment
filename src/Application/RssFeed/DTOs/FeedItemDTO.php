<?php

declare(strict_types=1);

namespace Populytics\Application\RssFeed\DTOs;

final readonly class FeedItemDTO
{
    public function __construct(
        public int $id,
        public int $feedId,
        public string $title,
        public string $link,
        public string $entryId,
        public string $publishedAt,
        public ?string $feedName = null
    ) {
    }
}
