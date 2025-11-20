<?php

namespace App\Http\Controllers\Settings;

use App\Actions\Fortify\UpdateUserPasswordAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PasswordController extends Controller
{
    /**
     * Show the user's password settings page.
     */
    public function edit(): Response
    {
        return Inertia::render('settings/Password');
    }

    /**
     * Update the user's password.
     */
    public function update(Request $request, UpdateUserPasswordAction $updater): RedirectResponse
    {
        $updater->update($request->user(), $request->all());

        return back();
    }
}
