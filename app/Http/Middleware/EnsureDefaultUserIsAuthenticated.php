<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EnsureDefaultUserIsAuthenticated
{
    /**
     * Guarantees that the single-user workspace always has an authenticated owner.
     */
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            $user = User::query()->first();

            if (! $user) {
                $user = User::forceCreate([
                    'name' => 'Workspace Owner',
                    'email' => 'owner@example.com',
                    'password' => Str::random(40),
                ]);

                $user->forceFill(['email_verified_at' => now()])->save();
            } elseif (is_null($user->email_verified_at)) {
                $user->forceFill(['email_verified_at' => now()])->save();
            }

            Auth::login($user);
        }

        return $next($request);
    }
}
