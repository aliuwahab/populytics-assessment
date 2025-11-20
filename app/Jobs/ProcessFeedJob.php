<?php

declare(strict_types=1);

namespace App\Jobs;

use Populytics\Application\RssFeed\UseCases\ProcessFeedUseCase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

final class ProcessFeedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60; // seconds

    public function __construct(
        public readonly int $feedId
    ) {
    }

    public function handle(ProcessFeedUseCase $processFeedUseCase): void
    {
        try {
            Log::info('Processing feed job started', ['feed_id' => $this->feedId]);
            $processFeedUseCase->execute($this->feedId);
            Log::info('Processing feed job completed', ['feed_id' => $this->feedId]);
        } catch (\Exception $e) {
            Log::error('Processing feed job failed', [
                'feed_id' => $this->feedId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e; // Re-throw to trigger retry mechanism
        }
    }
}
