<?php

declare(strict_types=1);

namespace Populytics\Application\RssFeed\Repositories;

use Populytics\Domain\RssFeed\Entities\FeedItem;
use Populytics\Domain\RssFeed\ValueObjects\EntryId;
use Illuminate\Support\Collection;

interface FeedItemRepositoryInterface
{
    public function save(FeedItem $feedItem): void;

    public function findById(int $feedItemId): ?FeedItem;

    public function findByFeedId(int $feedId): Collection;

    public function findByFeedIdAndEntryId(int $feedId, EntryId $entryId): ?FeedItem;

    public function findAllByFeedIds(array $feedIds): Collection;

    public function delete(FeedItem $feedItem): void;
}
