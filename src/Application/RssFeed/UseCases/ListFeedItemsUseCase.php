<?php

declare(strict_types=1);

namespace Populytics\Application\RssFeed\UseCases;

use Populytics\Application\RssFeed\DTOs\FeedItemDTO;
use Populytics\Application\RssFeed\Repositories\FeedItemRepositoryInterface;
use Populytics\Application\RssFeed\Repositories\FeedRepositoryInterface;
use Populytics\Domain\RssFeed\Exceptions\FeedAccessDeniedException;
use Populytics\Domain\RssFeed\Exceptions\FeedNotFoundException;

final class ListFeedItemsUseCase
{
    public function __construct(
        private readonly FeedRepositoryInterface $feedRepository,
        private readonly FeedItemRepositoryInterface $feedItemRepository
    ) {
    }

    /**
     * @return array<FeedItemDTO>
     */
    public function execute(int $userId): array
    {
        // Get all feeds for the user
        $feeds = $this->feedRepository->findByUserId($userId);
        $feedIds = $feeds->map(fn ($feed) => $feed->getId())
            ->filter()
            ->values()
            ->toArray();

        if (empty($feedIds)) {
            return [];
        }

        // Get all items for these feeds
        $feedItems = $this->feedItemRepository->findAllByFeedIds($feedIds);

        // Create a map of feed ID to feed name
        $feedNamesMap = $feeds->mapWithKeys(function ($feed) {
            return [$feed->getId() => (string) $feed->getName()];
        })->toArray();

        // Convert to DTOs and sort by published_at descending
        return $feedItems
            ->map(function ($item) use ($feedNamesMap) {
                $feedId = $item->getFeedId();
                return new FeedItemDTO(
                    id: $item->getId() ?? throw new \RuntimeException('Feed item ID is required.'),
                    feedId: $feedId,
                    title: $item->getTitle(),
                    link: $item->getLink(),
                    entryId: (string) $item->getEntryId(),
                    publishedAt: $item->getPublishedAt()->toIso8601String(),
                    feedName: $feedNamesMap[$feedId] ?? null
                );
            })
            ->sortByDesc(fn (FeedItemDTO $dto) => $dto->publishedAt)
            ->values()
            ->toArray();
    }

    /**
     * List feed items for a specific feed
     *
     * @return array<FeedItemDTO>
     * @throws FeedNotFoundException
     * @throws FeedAccessDeniedException
     */
    public function executeForFeed(int $feedId, int $userId): array
    {
        $feed = $this->feedRepository->findById($feedId);
        if ($feed === null) {
            throw FeedNotFoundException::byId($feedId);
        }

        if (! $feed->belongsTo($userId)) {
            throw FeedAccessDeniedException::forUser($feedId, $userId);
        }

        $feedItems = $this->feedItemRepository->findByFeedId($feedId);

        return $feedItems
            ->map(function ($item) use ($feed) {
                return new FeedItemDTO(
                    id: $item->getId() ?? throw new \RuntimeException('Feed item ID is required.'),
                    feedId: $item->getFeedId(),
                    title: $item->getTitle(),
                    link: $item->getLink(),
                    entryId: (string) $item->getEntryId(),
                    publishedAt: $item->getPublishedAt()->toIso8601String(),
                    feedName: (string) $feed->getName()
                );
            })
            ->sortByDesc(fn (FeedItemDTO $dto) => $dto->publishedAt)
            ->values()
            ->toArray();
    }
}
