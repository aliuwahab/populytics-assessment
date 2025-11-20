<?php

declare(strict_types=1);

namespace Populytics\Application\RssFeed\UseCases;

use Populytics\Application\RssFeed\DTOs\FeedDTO;
use Populytics\Application\RssFeed\Repositories\FeedRepositoryInterface;

final class ListUserFeedsUseCase
{
    public function __construct(
        private readonly FeedRepositoryInterface $feedRepository
    ) {
    }

    /**
     * @return array<FeedDTO>
     */
    public function execute(int $userId): array
    {
        $feeds = $this->feedRepository->findByUserId($userId);

        return $feeds->map(function ($feed) {
            return new FeedDTO(
                id: $feed->getId() ?? throw new \RuntimeException('Feed ID is required.'),
                userId: $feed->getUserId(),
                name: (string) $feed->getName(),
                url: (string) $feed->getUrl(),
                lastProcessedAt: $feed->getLastProcessedAt()?->toIso8601String(),
                createdAt: $feed->getCreatedAt()?->toIso8601String() ?? now()->toIso8601String(),
                updatedAt: $feed->getUpdatedAt()?->toIso8601String() ?? now()->toIso8601String()
            );
        })->toArray();
    }
}
