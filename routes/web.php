<?php

use App\Http\Controllers\FeedController;
use App\Models\Feed;
use App\Models\FeedItem;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    $user = auth()->user();

    $feedsQuery = Feed::query()->where('user_id', $user->id);

    $feedsCount = (clone $feedsQuery)->count();
    $latestFeed = (clone $feedsQuery)->latest('created_at')->first();
    $lastProcessedFeed = (clone $feedsQuery)
        ->whereNotNull('last_processed_at')
        ->latest('last_processed_at')
        ->first();

    $feedItemsCount = FeedItem::query()
        ->whereHas('feed', fn ($query) => $query->where('user_id', $user->id))
        ->count();

    return Inertia::render('Dashboard', [
        'stats' => [
            'feedsCount' => $feedsCount,
            'feedItemsCount' => $feedItemsCount,
            'lastProcessedAt' => $lastProcessedFeed?->last_processed_at?->toIso8601String(),
            'latestFeedName' => $latestFeed?->name,
        ],
        'feedsUrl' => route('feeds.index'),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/feeds', [FeedController::class, 'index'])->name('feeds.index');
    Route::post('/feeds', [FeedController::class, 'store'])->name('feeds.store');
});

require __DIR__.'/settings.php';
