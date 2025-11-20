<?php

declare(strict_types=1);

namespace Populytics\Domain\RssFeed\Exceptions;

use RuntimeException;

final class FeedAccessDeniedException extends RuntimeException
{
    public static function forUser(int $feedId, int $userId): self
    {
        return new self("Access denied to feed {$feedId} for user {$userId}.");
    }
}
