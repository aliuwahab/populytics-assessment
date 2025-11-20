<?php

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Populytics\Application\RssFeed\DTOs\RssFeedEntryDTO;
use Populytics\Application\RssFeed\Services\RssFeedParserInterface;
use Populytics\Application\RssFeed\UseCases\ProcessFeedUseCase;
use Populytics\Domain\RssFeed\Entities\Feed as FeedEntity;
use Populytics\Domain\RssFeed\Events\FeedProcessed;
use Populytics\Domain\RssFeed\ValueObjects\FeedName;
use Populytics\Domain\RssFeed\ValueObjects\FeedUrl;
use Populytics\Infrastructure\RssFeed\Repositories\EloquentFeedItemRepository;
use Populytics\Infrastructure\RssFeed\Repositories\EloquentFeedRepository;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('processes feed entries and updates the feed metadata', function () {
    $capturedEvent = null;
    Event::listen(FeedProcessed::class, function (FeedProcessed $event) use (&$capturedEvent) {
        $capturedEvent = $event;
    });

    $user = User::factory()->create();

    $feedRepository = app(EloquentFeedRepository::class);
    $feedItemRepository = app(EloquentFeedItemRepository::class);

    $feed = FeedEntity::create(
        userId: $user->id,
        name: new FeedName('My Feed'),
        url: new FeedUrl('https://example.com/rss')
    );
    $feedRepository->save($feed);

    $entries = [
        new RssFeedEntryDTO('First', 'https://example.com/1', 'entry-1', Carbon::parse('2025-01-01')),
        new RssFeedEntryDTO('Second', 'https://example.com/2', 'entry-2', Carbon::parse('2025-02-02')),
    ];

    app()->bind(RssFeedParserInterface::class, fn () => new class ($entries) implements RssFeedParserInterface {
        public function __construct(private array $entries)
        {
        }

        public function parseFeed(FeedUrl $url): array
        {
            return $this->entries;
        }
    });

    $useCase = app(ProcessFeedUseCase::class);

    $useCase->execute($feed->getId());

    $processedFeed = $feedRepository->findById($feed->getId());

    expect($processedFeed?->getLastProcessedAt())->not->toBeNull();
    expect(DB::table('feed_items')->count())->toBe(2);

    expect($capturedEvent)->not->toBeNull()
        ->and($capturedEvent?->feed->getId())->toBe($feed->getId())
        ->and($capturedEvent?->itemsProcessed)->toBe(2);
});

