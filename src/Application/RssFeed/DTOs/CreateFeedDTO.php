<?php

declare(strict_types=1);

namespace Populytics\Application\RssFeed\DTOs;

final readonly class CreateFeedDTO
{
    public function __construct(
        public int $userId,
        public string $name,
        public string $url
    ) {
    }
}
