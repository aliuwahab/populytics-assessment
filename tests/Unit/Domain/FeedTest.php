<?php

use Carbon\Carbon;
use Populytics\Domain\RssFeed\Entities\Feed;
use Populytics\Domain\RssFeed\ValueObjects\FeedName;
use Populytics\Domain\RssFeed\ValueObjects\FeedUrl;

it('marks a feed as processed with the provided timestamp', function () {
    $feed = Feed::create(
        userId: 1,
        name: new FeedName('My Feed'),
        url: new FeedUrl('https://example.com/rss')
    );

    $timestamp = Carbon::parse('2025-01-01 12:00:00');
    $feed->markAsProcessed($timestamp);

    expect($feed->getLastProcessedAt())->toEqual($timestamp);
});

it('prevents resetting the feed identifier once set', function () {
    $feed = Feed::create(
        userId: 1,
        name: new FeedName('My Feed'),
        url: new FeedUrl('https://example.com/rss')
    );

    $feed->setId(10);

    $feed->setId(11);
})->throws(RuntimeException::class);

it('determines if it belongs to a user', function () {
    $feed = Feed::create(
        userId: 42,
        name: new FeedName('My Feed'),
        url: new FeedUrl('https://example.com/rss')
    );

    expect($feed->belongsTo(42))->toBeTrue()
        ->and($feed->belongsTo(99))->toBeFalse();
});

