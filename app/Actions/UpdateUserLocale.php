<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UpdateUserLocale
{
    /**
     * Validate and save the user's preferred locale.
     *
     * @throws ValidationException
     */
    public function __invoke(User $user, Request $request): void
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', 'in:ro,ru,en'],
        ]);

        $user->update(['locale' => $validated['locale']]);

        session()->put('locale', $validated['locale']);
    }
}
