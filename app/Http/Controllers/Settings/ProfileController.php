<?php

namespace App\Http\Controllers\Settings;

use App\Actions\Fortify\DeleteUserAction;
use App\Actions\Fortify\UpdateUserProfileInformationAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/Profile', [
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request, UpdateUserProfileInformationAction $updater): RedirectResponse
    {
        $updater->update($request->user(), $request->only('name', 'email'));

        return to_route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(Request $request, DeleteUserAction $deleteUser): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $deleteUser->delete($request->user(), $request->only('password'));

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
