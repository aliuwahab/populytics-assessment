<?php

declare(strict_types=1);

namespace Populytics\Domain\RssFeed\Events;

use Populytics\Domain\RssFeed\Entities\Feed;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class FeedCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Feed $feed
    ) {
    }
}
