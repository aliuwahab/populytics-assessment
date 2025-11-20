<?php

use Populytics\Domain\RssFeed\ValueObjects\FeedUrl;

it('accepts valid http and https URLs', function () {
    $url = new FeedUrl('https://example.com/rss');

    expect((string) $url)->toBe('https://example.com/rss');
});

it('rejects empty values', function () {
    new FeedUrl('');
})->throws(\InvalidArgumentException::class, 'Feed URL cannot be empty.');

it('rejects non http schemes', function () {
    new FeedUrl('ftp://example.com/rss');
})->throws(\InvalidArgumentException::class, 'Feed URL must use HTTP or HTTPS protocol.');

it('compares equality by value', function () {
    $first = new FeedUrl('https://example.com/rss');
    $second = new FeedUrl('https://example.com/rss');
    $different = new FeedUrl('https://another.test/feed');

    expect($first->equals($second))->toBeTrue()
        ->and($first->equals($different))->toBeFalse();
});

