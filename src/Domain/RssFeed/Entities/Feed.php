<?php

declare(strict_types=1);

namespace Populytics\Domain\RssFeed\Entities;

use Populytics\Domain\RssFeed\ValueObjects\FeedName;
use Populytics\Domain\RssFeed\ValueObjects\FeedUrl;
use Carbon\Carbon;

final class Feed
{
    private ?int $id = null;
    private int $userId;
    private FeedName $name;
    private FeedUrl $url;
    private ?Carbon $lastProcessedAt = null;
    private ?Carbon $createdAt = null;
    private ?Carbon $updatedAt = null;

    public function __construct(
        int $userId,
        FeedName $name,
        FeedUrl $url
    ) {
        $this->userId = $userId;
        $this->name = $name;
        $this->url = $url;
    }

    public static function create(
        int $userId,
        FeedName $name,
        FeedUrl $url
    ): self {
        return new self($userId, $name, $url);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        if ($this->id !== null) {
            throw new \RuntimeException('Feed ID has already been set.');
        }
        $this->id = $id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getName(): FeedName
    {
        return $this->name;
    }

    public function getUrl(): FeedUrl
    {
        return $this->url;
    }

    public function updateName(FeedName $name): void
    {
        $this->name = $name;
    }

    public function updateUrl(FeedUrl $url): void
    {
        $this->url = $url;
    }

    public function getLastProcessedAt(): ?Carbon
    {
        return $this->lastProcessedAt;
    }

    public function markAsProcessed(?Carbon $timestamp = null): void
    {
        $this->lastProcessedAt = $timestamp ?? Carbon::now();
    }

    public function setLastProcessedAt(?Carbon $timestamp): void
    {
        $this->lastProcessedAt = $timestamp;
    }

    public function getCreatedAt(): ?Carbon
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?Carbon $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?Carbon $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function belongsTo(int $userId): bool
    {
        return $this->userId === $userId;
    }
}
