<?php

declare(strict_types=1);

namespace Populytics\Application\RssFeed\DTOs;

use Carbon\Carbon;

final readonly class RssFeedEntryDTO
{
    public function __construct(
        public string $title,
        public string $link,
        public string $entryId,
        public Carbon $publishedAt
    ) {
    }
}
