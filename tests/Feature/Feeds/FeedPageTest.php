<?php

use App\Models\Feed;
use App\Models\FeedItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia;
use Populytics\Domain\RssFeed\DomainServices\RssFeedValidatorInterface;
use Populytics\Domain\RssFeed\Events\FeedCreated;
use Populytics\Domain\RssFeed\ValueObjects\FeedUrl;

uses(RefreshDatabase::class);

it('redirects guests from the feeds page', function () {
    $this->get(route('feeds.index'))->assertRedirect(route('login'));
});

it('lists and filters feed items by feed', function () {
    $user = User::factory()->create();

    $feedA = Feed::create([
        'user_id' => $user->id,
        'name' => 'Feed A',
        'url' => 'https://example.com/a.xml',
    ]);

    $feedB = Feed::create([
        'user_id' => $user->id,
        'name' => 'Feed B',
        'url' => 'https://example.com/b.xml',
    ]);

    FeedItem::create([
        'feed_id' => $feedA->id,
        'title' => 'Item A1',
        'link' => 'https://example.com/a1',
        'entry_id' => 'a1',
        'published_at' => Carbon::now(),
    ]);

    FeedItem::create([
        'feed_id' => $feedB->id,
        'title' => 'Item B1',
        'link' => 'https://example.com/b1',
        'entry_id' => 'b1',
        'published_at' => Carbon::now(),
    ]);

    $this->actingAs($user)
        ->get(route('feeds.index', ['feed_id' => $feedA->id]))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Feeds/Index')
            ->where('filters.feedId', $feedA->id)
            ->has('feedItems', 1, fn (AssertableInertia $item) => $item
                ->where('feedId', $feedA->id)
                ->etc())
        );
});

it('stores a feed via the endpoint and queues processing', function () {
    Queue::fake();
    $feedCreated = false;
    Event::listen(FeedCreated::class, function () use (&$feedCreated) {
        $feedCreated = true;
    });

    $user = User::factory()->create();

    app()->bind(RssFeedValidatorInterface::class, fn () => new class implements RssFeedValidatorInterface {
        public function isValidRssFeed(FeedUrl $feedUrl): bool
        {
            return true;
        }
    });

    $response = $this->actingAs($user)->post(route('feeds.store'), [
        'name' => 'My Feed',
        'url' => 'https://example.com/rss.xml',
    ]);

    $response->assertRedirect(route('feeds.index'));

    $this->assertDatabaseHas('feeds', [
        'name' => 'My Feed',
        'url' => 'https://example.com/rss.xml',
        'user_id' => $user->id,
    ]);

    expect($feedCreated)->toBeTrue();
});

