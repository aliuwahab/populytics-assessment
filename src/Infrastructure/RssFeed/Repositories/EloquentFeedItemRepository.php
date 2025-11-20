<?php

declare(strict_types=1);

namespace Populytics\Infrastructure\RssFeed\Repositories;

use Populytics\Application\RssFeed\Repositories\FeedItemRepositoryInterface;
use Populytics\Domain\RssFeed\Entities\FeedItem;
use Populytics\Domain\RssFeed\ValueObjects\EntryId;
use App\Models\FeedItem as FeedItemModel;
use Carbon\Carbon;
use Illuminate\Support\Collection;

final class EloquentFeedItemRepository implements FeedItemRepositoryInterface
{
    public function save(FeedItem $feedItem): void
    {
        $model = $feedItem->getId() !== null
            ? FeedItemModel::findOrFail($feedItem->getId())
            : new FeedItemModel();

        $model->feed_id = $feedItem->getFeedId();
        $model->title = $feedItem->getTitle();
        $model->link = $feedItem->getLink();
        $model->entry_id = (string) $feedItem->getEntryId();
        $model->published_at = $feedItem->getPublishedAt();
        $model->save();

        if ($feedItem->getId() === null) {
            $feedItem->setId($model->id);
            $feedItem->setCreatedAt($model->created_at ? Carbon::instance($model->created_at) : null);
            $feedItem->setUpdatedAt($model->updated_at ? Carbon::instance($model->updated_at) : null);
        } else {
            $feedItem->setUpdatedAt($model->updated_at ? Carbon::instance($model->updated_at) : null);
        }
    }

    public function findById(int $feedItemId): ?FeedItem
    {
        $model = FeedItemModel::find($feedItemId);
        if ($model === null) {
            return null;
        }

        return $this->toDomainEntity($model);
    }

    public function findByFeedId(int $feedId): Collection
    {
        return FeedItemModel::where('feed_id', $feedId)
            ->get()
            ->map(fn ($model) => $this->toDomainEntity($model));
    }

    public function findByFeedIdAndEntryId(int $feedId, EntryId $entryId): ?FeedItem
    {
        $model = FeedItemModel::where('feed_id', $feedId)
            ->where('entry_id', (string) $entryId)
            ->first();

        if ($model === null) {
            return null;
        }

        return $this->toDomainEntity($model);
    }

    public function findAllByFeedIds(array $feedIds): Collection
    {
        if (empty($feedIds)) {
            return collect();
        }

        return FeedItemModel::whereIn('feed_id', $feedIds)
            ->get()
            ->map(fn ($model) => $this->toDomainEntity($model));
    }

    public function delete(FeedItem $feedItem): void
    {
        if ($feedItem->getId() === null) {
            return;
        }

        FeedItemModel::destroy($feedItem->getId());
    }

    private function toDomainEntity(FeedItemModel $model): FeedItem
    {
        $feedItem = FeedItem::create(
            feedId: $model->feed_id,
            title: $model->title,
            link: $model->link,
            entryId: new EntryId($model->entry_id),
            publishedAt: Carbon::parse($model->published_at)
        );

        $feedItem->setId($model->id);
        $feedItem->setCreatedAt($model->created_at ? Carbon::instance($model->created_at) : null);
        $feedItem->setUpdatedAt($model->updated_at ? Carbon::instance($model->updated_at) : null);

        return $feedItem;
    }
}
