<?php

declare(strict_types=1);

namespace Populytics\Infrastructure\RssFeed\Services;

use Populytics\Application\RssFeed\DTOs\RssFeedEntryDTO;
use Populytics\Application\RssFeed\Services\RssFeedParserInterface;
use Populytics\Domain\RssFeed\Exceptions\InvalidRssFeedException;
use Populytics\Domain\RssFeed\ValueObjects\FeedUrl;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class SimpleXmlRssFeedParser implements RssFeedParserInterface
{
    private const HTTP_TIMEOUT = 30;

    public function parseFeed(FeedUrl $url): array
    {
        try {
            $response = Http::timeout(self::HTTP_TIMEOUT)->get((string) $url);

            if (! $response->successful()) {
                Log::error('Failed to fetch RSS feed', [
                    'url' => (string) $url,
                    'status' => $response->status(),
                ]);
                throw InvalidRssFeedException::unableToFetch((string) $url);
            }

            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($response->body());

            if ($xml === false) {
                $errors = libxml_get_errors();
                libxml_clear_errors();

                Log::error('Failed to parse RSS feed XML', [
                    'url' => (string) $url,
                    'errors' => array_map(fn ($error) => $error->message, $errors),
                ]);

                throw InvalidRssFeedException::invalidFormat((string) $url);
            }

            // Handle both RSS 2.0 and Atom formats
            $items = $xml->channel->item ?? $xml->item ?? [];

            $entries = [];
            foreach ($items as $item) {
                try {
                    $entryId = (string) ($item->guid ?? $item->id ?? $item->link ?? '');
                    if (empty($entryId)) {
                        continue; // Skip items without a valid identifier
                    }

                    $title = (string) ($item->title ?? 'Untitled');
                    $link = (string) ($item->link ?? $item->href ?? '');
                    $pubDate = (string) ($item->pubDate ?? $item->published ?? $item->updated ?? '');

                    // Parse published date
                    $publishedAt = Carbon::now();
                    if (! empty($pubDate)) {
                        try {
                            $publishedAt = Carbon::parse($pubDate);
                        } catch (\Exception $e) {
                            Log::warning('Failed to parse published date', [
                                'url' => (string) $url,
                                'pubDate' => $pubDate,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }

                    $entries[] = new RssFeedEntryDTO(
                        title: $title,
                        link: $link,
                        entryId: $entryId,
                        publishedAt: $publishedAt
                    );
                } catch (\Exception $e) {
                    Log::warning('Failed to process RSS feed item', [
                        'url' => (string) $url,
                        'error' => $e->getMessage(),
                    ]);
                    // Continue processing other items
                    continue;
                }
            }

            return $entries;
        } catch (InvalidRssFeedException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Unexpected error parsing RSS feed', [
                'url' => (string) $url,
                'error' => $e->getMessage(),
            ]);
            throw InvalidRssFeedException::unableToFetch((string) $url);
        }
    }
}
