<?php

declare(strict_types=1);

namespace Populytics\Application\RssFeed\DTOs;

final readonly class FeedDTO
{
    public function __construct(
        public int $id,
        public int $userId,
        public string $name,
        public string $url,
        public ?string $lastProcessedAt,
        public string $createdAt,
        public string $updatedAt
    ) {
    }
}
