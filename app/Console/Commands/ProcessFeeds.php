<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Populytics\Application\RssFeed\Repositories\FeedRepositoryInterface;
use Populytics\Application\RssFeed\UseCases\ProcessFeedUseCase;
use App\Jobs\ProcessFeedJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

final class ProcessFeeds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feeds:process {--sync : Process feeds synchronously instead of queuing jobs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and process all registered RSS feeds';

    public function __construct(
        private readonly FeedRepositoryInterface $feedRepository,
        private readonly ProcessFeedUseCase $processFeedUseCase
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $feeds = $this->feedRepository->findAll();

        if ($feeds->isEmpty()) {
            $this->info('No feeds to process.');
            return Command::SUCCESS;
        }

        $this->info('Starting to process ' . $feeds->count() . ' feed(s)...');

        $processSynchronously = $this->option('sync');

        foreach ($feeds as $feed) {
            $feedId = $feed->getId();
            if ($feedId === null) {
                $this->warn('Skipping feed without ID: ' . (string) $feed->getUrl());
                continue;
            }

            $this->line('Processing feed: ' . (string) $feed->getName() . ' (' . (string) $feed->getUrl() . ')');

            if ($processSynchronously) {
                // Process synchronously
                try {
                    $this->processFeedUseCase->execute($feedId);
                    $this->info('✓ Successfully processed feed: ' . (string) $feed->getName());
                } catch (\Exception $e) {
                    $this->error('✗ Failed to process feed: ' . (string) $feed->getName());
                    $this->error('  Error: ' . $e->getMessage());
                    Log::error('Error processing feed', [
                        'feed_id' => $feedId,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            } else {
                // Queue the job
                ProcessFeedJob::dispatch($feedId);
                $this->info('→ Queued job for feed: ' . (string) $feed->getName());
            }
        }

        if (! $processSynchronously) {
            $this->info('All feeds have been queued for processing.');
        } else {
            $this->info('All feeds processed.');
        }

        return Command::SUCCESS;
    }
}