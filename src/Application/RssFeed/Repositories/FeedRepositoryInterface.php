<?php

declare(strict_types=1);

namespace Populytics\Application\RssFeed\Repositories;

use Populytics\Domain\RssFeed\Entities\Feed;
use Populytics\Domain\RssFeed\ValueObjects\FeedUrl;
use Illuminate\Support\Collection;

interface FeedRepositoryInterface
{
    public function save(Feed $feed): void;

    public function findById(int $feedId): ?Feed;

    public function findByUrl(FeedUrl $url): ?Feed;

    public function findByUserId(int $userId): Collection;

    public function findAll(): Collection;

    public function delete(Feed $feed): void;
}
