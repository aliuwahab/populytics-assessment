<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Populytics\Application\RssFeed\DTOs\CreateFeedDTO;
use Populytics\Application\RssFeed\UseCases\CreateFeedUseCase;
use Populytics\Application\RssFeed\UseCases\ListFeedItemsUseCase;
use Populytics\Application\RssFeed\UseCases\ListUserFeedsUseCase;
use App\Http\Requests\CreateFeedRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

final class FeedController extends Controller
{
    public function __construct(
        private readonly CreateFeedUseCase $createFeedUseCase,
        private readonly ListUserFeedsUseCase $listUserFeedsUseCase,
        private readonly ListFeedItemsUseCase $listFeedItemsUseCase
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $user = Auth::user();
        $userId = $user->id;

        $feeds = $this->listUserFeedsUseCase->execute($userId);
        $feedItems = $this->listFeedItemsUseCase->execute($userId);

        return Inertia::render('Feeds/Index', [
            'feeds' => $feeds,
            'feedItems' => $feedItems,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateFeedRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $dto = new CreateFeedDTO(
            userId: $user->id,
            name: $request->validated('name'),
            url: $request->validated('url')
        );

        $this->createFeedUseCase->execute($dto);

        return redirect()->route('feeds.index')->with('success', 'Feed added successfully.');
    }
}
