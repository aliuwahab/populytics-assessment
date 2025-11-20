<?php

declare(strict_types=1);

namespace Populytics\Application\RssFeed\UseCases;

use Populytics\Application\RssFeed\Repositories\FeedItemRepositoryInterface;
use Populytics\Application\RssFeed\Repositories\FeedRepositoryInterface;
use Populytics\Application\RssFeed\Services\RssFeedParserInterface;
use Populytics\Domain\RssFeed\Entities\FeedItem;
use Populytics\Domain\RssFeed\Events\FeedProcessed;
use Populytics\Domain\RssFeed\Exceptions\FeedNotFoundException;
use Populytics\Domain\RssFeed\ValueObjects\EntryId;
use Illuminate\Events\Dispatcher;

final class ProcessFeedUseCase
{
    public function __construct(
        private readonly FeedRepositoryInterface $feedRepository,
        private readonly FeedItemRepositoryInterface $feedItemRepository,
        private readonly RssFeedParserInterface $feedParser,
        private readonly Dispatcher $eventDispatcher
    ) {
    }

    public function execute(int $feedId): void
    {
        $feed = $this->feedRepository->findById($feedId);
        if ($feed === null) {
            throw FeedNotFoundException::byId($feedId);
        }

        // Parse RSS feed
        $entries = $this->feedParser->parseFeed($feed->getUrl());

        $itemsProcessed = 0;

        // Process each entry
        foreach ($entries as $entry) {
            $entryId = new EntryId($entry->entryId);

            // Check if item already exists
            $existingItem = $this->feedItemRepository->findByFeedIdAndEntryId(
                $feedId,
                $entryId
            );

            if ($existingItem !== null) {
                // Update existing item
                $existingItem->updateTitle($entry->title);
                $existingItem->updateLink($entry->link);
                $existingItem->updatePublishedAt($entry->publishedAt);
                $this->feedItemRepository->save($existingItem);
            } else {
                // Create new item
                $feedItem = FeedItem::create(
                    feedId: $feedId,
                    title: $entry->title,
                    link: $entry->link,
                    entryId: $entryId,
                    publishedAt: $entry->publishedAt
                );
                $this->feedItemRepository->save($feedItem);
            }

            $itemsProcessed++;
        }

        // Mark feed as processed
        $feed->markAsProcessed();
        $this->feedRepository->save($feed);

        // Dispatch domain event
        $this->eventDispatcher->dispatch(new FeedProcessed($feed, $itemsProcessed));
    }
}
