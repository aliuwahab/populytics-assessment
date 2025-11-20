<?php

declare(strict_types=1);

namespace Populytics\Infrastructure\RssFeed\Repositories;

use Populytics\Application\RssFeed\Repositories\FeedRepositoryInterface;
use Populytics\Domain\RssFeed\Entities\Feed;
use Populytics\Domain\RssFeed\ValueObjects\FeedName;
use Populytics\Domain\RssFeed\ValueObjects\FeedUrl;
use App\Models\Feed as FeedModel;
use Carbon\Carbon;
use Illuminate\Support\Collection;

final class EloquentFeedRepository implements FeedRepositoryInterface
{
    public function save(Feed $feed): void
    {
        $model = $feed->getId() !== null
            ? FeedModel::findOrFail($feed->getId())
            : new FeedModel();

        $model->user_id = $feed->getUserId();
        $model->name = (string) $feed->getName();
        $model->url = (string) $feed->getUrl();
        $model->last_processed_at = $feed->getLastProcessedAt();
        $model->save();

        if ($feed->getId() === null) {
            $feed->setId($model->id);
            $feed->setCreatedAt($model->created_at ? Carbon::make($model->created_at) : null);
            $feed->setUpdatedAt($model->updated_at ? Carbon::make($model->updated_at) : null);
        } else {
            $feed->setUpdatedAt($model->updated_at ? Carbon::make($model->updated_at) : null);
        }
    }

    public function findById(int $feedId): ?Feed
    {
        $model = FeedModel::find($feedId);
        if ($model === null) {
            return null;
        }

        return $this->toDomainEntity($model);
    }

    public function findByUrl(FeedUrl $url): ?Feed
    {
        $model = FeedModel::where('url', (string) $url)->first();
        if ($model === null) {
            return null;
        }

        return $this->toDomainEntity($model);
    }

    public function findByUserId(int $userId): Collection
    {
        return FeedModel::where('user_id', $userId)
            ->get()
            ->map(fn ($model) => $this->toDomainEntity($model));
    }

    public function findAll(): Collection
    {
        return FeedModel::all()
            ->map(fn ($model) => $this->toDomainEntity($model));
    }

    public function delete(Feed $feed): void
    {
        if ($feed->getId() === null) {
            return;
        }

        FeedModel::destroy($feed->getId());
    }

    private function toDomainEntity(FeedModel $model): Feed
    {
        $feed = Feed::create(
            userId: $model->user_id,
            name: new FeedName($model->name),
            url: new FeedUrl($model->url)
        );

        $feed->setId($model->id);
        $feed->setCreatedAt($model->created_at ? Carbon::make($model->created_at) : null);
        $feed->setUpdatedAt($model->updated_at ? Carbon::make($model->updated_at) : null);

        if ($model->last_processed_at) {
            $feed->setLastProcessedAt(Carbon::make($model->last_processed_at));
        }

        return $feed;
    }
}
