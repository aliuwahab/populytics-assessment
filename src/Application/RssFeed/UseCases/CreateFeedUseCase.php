<?php

declare(strict_types=1);

namespace Populytics\Application\RssFeed\UseCases;

use Populytics\Application\RssFeed\DTOs\CreateFeedDTO;
use Populytics\Application\RssFeed\DTOs\FeedDTO;
use Populytics\Application\RssFeed\Repositories\FeedRepositoryInterface;
use Populytics\Domain\RssFeed\DomainServices\RssFeedValidatorInterface;
use Populytics\Domain\RssFeed\Entities\Feed;
use Populytics\Domain\RssFeed\Exceptions\InvalidRssFeedException;
use Populytics\Domain\RssFeed\Events\FeedCreated;
use Populytics\Domain\RssFeed\ValueObjects\FeedName;
use Populytics\Domain\RssFeed\ValueObjects\FeedUrl;
use Illuminate\Events\Dispatcher;
use Illuminate\Validation\ValidationException;

final class CreateFeedUseCase
{
    public function __construct(
        private readonly FeedRepositoryInterface $feedRepository,
        private readonly RssFeedValidatorInterface $feedValidator,
        private readonly Dispatcher $eventDispatcher
    ) {
    }

    public function execute(CreateFeedDTO $dto): FeedDTO
    {
        $feedUrl = new FeedUrl($dto->url);
        $feedName = new FeedName($dto->name);

        // Check if feed URL already exists
        $existingFeed = $this->feedRepository->findByUrl($feedUrl);
        if ($existingFeed !== null) {
            throw ValidationException::withMessages([
                'url' => ['This feed URL has already been registered.'],
            ]);
        }

        // Validate RSS feed
        try {
            if (! $this->feedValidator->isValidRssFeed($feedUrl)) {
                throw ValidationException::withMessages([
                    'url' => ['The provided URL does not point to a valid RSS feed.'],
                ]);
            }
        } catch (\RuntimeException $e) {
            throw ValidationException::withMessages([
                'url' => ['Unable to validate RSS feed: ' . $e->getMessage()],
            ]);
        }

        // Create domain entity
        $feed = Feed::create($dto->userId, $feedName, $feedUrl);

        // Persist
        $this->feedRepository->save($feed);

        // Dispatch domain event
        $this->eventDispatcher->dispatch(new FeedCreated($feed));

        // Return DTO
        return new FeedDTO(
            id: $feed->getId() ?? throw new \RuntimeException('Feed ID should be set after save.'),
            userId: $feed->getUserId(),
            name: (string) $feed->getName(),
            url: (string) $feed->getUrl(),
            lastProcessedAt: $feed->getLastProcessedAt()?->toIso8601String(),
            createdAt: $feed->getCreatedAt()?->toIso8601String() ?? now()->toIso8601String(),
            updatedAt: $feed->getUpdatedAt()?->toIso8601String() ?? now()->toIso8601String()
        );
    }
}
