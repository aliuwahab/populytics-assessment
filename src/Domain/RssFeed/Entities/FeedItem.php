<?php

declare(strict_types=1);

namespace Populytics\Domain\RssFeed\Entities;

use Populytics\Domain\RssFeed\ValueObjects\EntryId;
use Carbon\Carbon;

final class FeedItem
{
    private ?int $id = null;
    private int $feedId;
    private string $title;
    private string $link;
    private EntryId $entryId;
    private Carbon $publishedAt;
    private ?Carbon $createdAt = null;
    private ?Carbon $updatedAt = null;

    public function __construct(
        int $feedId,
        string $title,
        string $link,
        EntryId $entryId,
        Carbon $publishedAt
    ) {
        if (empty(trim($title))) {
            throw new \InvalidArgumentException('Feed item title cannot be empty.');
        }

        if (empty(trim($link))) {
            throw new \InvalidArgumentException('Feed item link cannot be empty.');
        }

        $this->feedId = $feedId;
        $this->title = $title;
        $this->link = $link;
        $this->entryId = $entryId;
        $this->publishedAt = $publishedAt;
    }

    public static function create(
        int $feedId,
        string $title,
        string $link,
        EntryId $entryId,
        Carbon $publishedAt
    ): self {
        return new self($feedId, $title, $link, $entryId, $publishedAt);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        if ($this->id !== null) {
            throw new \RuntimeException('Feed item ID has already been set.');
        }
        $this->id = $id;
    }

    public function getFeedId(): int
    {
        return $this->feedId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getEntryId(): EntryId
    {
        return $this->entryId;
    }

    public function getPublishedAt(): Carbon
    {
        return $this->publishedAt;
    }

    public function updateTitle(string $title): void
    {
        if (empty(trim($title))) {
            throw new \InvalidArgumentException('Feed item title cannot be empty.');
        }
        $this->title = $title;
    }

    public function updateLink(string $link): void
    {
        if (empty(trim($link))) {
            throw new \InvalidArgumentException('Feed item link cannot be empty.');
        }
        $this->link = $link;
    }

    public function updatePublishedAt(Carbon $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
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

    public function hasSameEntryId(EntryId $entryId): bool
    {
        return $this->entryId->equals($entryId);
    }
}
