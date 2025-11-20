<?php

declare(strict_types=1);

namespace Populytics\Infrastructure\RssFeed\Services;

use Populytics\Domain\RssFeed\DomainServices\RssFeedValidatorInterface;
use Populytics\Domain\RssFeed\ValueObjects\FeedUrl;
use Illuminate\Support\Facades\Http;

final class HttpRssFeedValidator implements RssFeedValidatorInterface
{
    private const HTTP_TIMEOUT = 10;

    public function isValidRssFeed(FeedUrl $url): bool
    {
        try {
            $response = Http::timeout(self::HTTP_TIMEOUT)
                ->withHeaders([
                    'Accept' => 'application/rss+xml, application/xml, text/xml, */*',
                ])
                ->get((string) $url);

            if (! $response->successful()) {
                return false;
            }

            $contentType = $response->header('Content-Type', '');
            $body = $response->body();

            // Check Content-Type header
            if (stripos($contentType, 'xml') === false &&
                stripos($contentType, 'rss') === false &&
                stripos($contentType, 'atom') === false) {
                // Content-Type doesn't indicate XML, but let's check the body
                if (stripos($body, '<rss') === false &&
                    stripos($body, '<feed') === false &&
                    stripos($body, '<?xml') === false) {
                    return false;
                }
            }

            // Try to parse as XML
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($body);
            libxml_clear_errors();

            if ($xml === false) {
                return false;
            }

            // Check if it's a valid RSS or Atom feed
            return isset($xml->channel) || isset($xml->item) || isset($xml->entry);
        } catch (\Exception $e) {
            return false;
        }
    }
}
