<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DeleteUserAction
{
    /**
     * Delete the given user.
     *
     * @param  array<string, mixed>  $input
     */
    public function delete(mixed $user, array $input): void
    {
        Validator::make($input, [
            'password' => ['required', 'current_password'],
        ])->validate();

        $user->delete();

        if ($user->fresh()) {
            $user->newModelQuery()->whereKey($user->getKey())->delete();
        }
    }
}

