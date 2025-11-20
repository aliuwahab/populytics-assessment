<?php

namespace App\Http\Controllers\Settings;

use App\Actions\Fortify\DeleteUserAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Populytics\Domain\User\Actions\UpdateUserProfileInformationAction;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function show(Request $request): Response
    {
        return Inertia::render('Settings/Profile', [
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request, UpdateUserProfileInformationAction $updater): RedirectResponse
    {
        $updater->update($request->user(), $request->all());

        return to_route('profile.show');
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(Request $request, DeleteUserAction $deleteUser): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $deleteUser->delete($request->user(), $request->all());

        return redirect('/');
    }
}
