<?php

declare(strict_types=1);

namespace App\Listeners;

use Populytics\Domain\RssFeed\Events\FeedCreated;
use App\Jobs\ProcessFeedJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

final class QueueFeedProcessing implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(FeedCreated $event): void
    {
        $feedId = $event->feed->getId();
        if ($feedId === null) {
            Log::warning('Cannot queue feed processing: feed ID is null', [
                'feed_url' => (string) $event->feed->getUrl(),
            ]);
            return;
        }

        // Queue the feed processing job
        ProcessFeedJob::dispatch($feedId);

        Log::info('Queued feed processing after creation', [
            'feed_id' => $feedId,
        ]);
    }
}
