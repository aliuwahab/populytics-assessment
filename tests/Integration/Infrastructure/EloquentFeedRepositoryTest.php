<?php

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Populytics\Domain\RssFeed\Entities\Feed as FeedEntity;
use Populytics\Domain\RssFeed\ValueObjects\FeedName;
use Populytics\Domain\RssFeed\ValueObjects\FeedUrl;
use Populytics\Infrastructure\RssFeed\Repositories\EloquentFeedRepository;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('persists and retrieves feeds via the eloquent repository', function () {
    $repository = app(EloquentFeedRepository::class);
    $user = User::factory()->create();

    $feed = FeedEntity::create(
        userId: $user->id,
        name: new FeedName('Saved Feed'),
        url: new FeedUrl('https://example.com/rss')
    );

    $repository->save($feed);

    $retrieved = $repository->findById($feed->getId());

    expect($retrieved)->not->toBeNull()
        ->and($retrieved?->getName()->value)->toBe('Saved Feed')
        ->and($retrieved?->getUrl()->value)->toBe('https://example.com/rss');
});

it('returns feeds for a user and updates timestamps', function () {
    $repository = app(EloquentFeedRepository::class);
    $user = User::factory()->create();

    $feed = FeedEntity::create(
        userId: $user->id,
        name: new FeedName('Timestamped Feed'),
        url: new FeedUrl('https://example.com/rss')
    );
    $feed->markAsProcessed(Carbon::parse('2025-01-01'));

    $repository->save($feed);

    $feeds = $repository->findByUserId($user->id);

    expect($feeds)->toHaveCount(1)
        ->and($feeds->first()?->getLastProcessedAt())->not->toBeNull();
});

