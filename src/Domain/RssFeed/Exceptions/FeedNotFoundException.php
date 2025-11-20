<?php

declare(strict_types=1);

namespace Populytics\Domain\RssFeed\Exceptions;

use RuntimeException;

final class FeedNotFoundException extends RuntimeException
{
    public static function byId(int $feedId): self
    {
        return new self("Feed with ID {$feedId} not found.");
    }
}
