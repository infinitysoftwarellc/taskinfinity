<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController as FortifyAuthenticatedSessionController;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth_breeze.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        return app(FortifyAuthenticatedSessionController::class)->store($request);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        return app(FortifyAuthenticatedSessionController::class)->destroy($request);
    }
}
