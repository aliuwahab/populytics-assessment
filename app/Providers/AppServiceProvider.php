<?php

declare(strict_types=1);

namespace App\Providers;

use Populytics\Application\RssFeed\Repositories\FeedItemRepositoryInterface;
use Populytics\Application\RssFeed\Repositories\FeedRepositoryInterface;
use Populytics\Application\RssFeed\Services\RssFeedParserInterface;
use Populytics\Domain\RssFeed\DomainServices\RssFeedValidatorInterface;
use Populytics\Infrastructure\RssFeed\Repositories\EloquentFeedItemRepository;
use Populytics\Infrastructure\RssFeed\Repositories\EloquentFeedRepository;
use Populytics\Infrastructure\RssFeed\Services\HttpRssFeedValidator;
use Populytics\Infrastructure\RssFeed\Services\SimpleXmlRssFeedParser;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind repository interfaces to their implementations
        $this->app->bind(FeedRepositoryInterface::class, EloquentFeedRepository::class);
        $this->app->bind(FeedItemRepositoryInterface::class, EloquentFeedItemRepository::class);

        // Bind service interfaces to their implementations
        $this->app->bind(RssFeedParserInterface::class, SimpleXmlRssFeedParser::class);
        $this->app->bind(RssFeedValidatorInterface::class, HttpRssFeedValidator::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
