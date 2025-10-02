<?php

// This service provider configures application services for the fortify service scope.
namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::twoFactorChallengeView(fn () => view('auth_breeze.two-factor-challenge'));
        Fortify::confirmPasswordView(fn () => view('auth_breeze.confirm-password'));

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        $this->app->booted(function () {
            $router = $this->app['router'];

            foreach ($router->getRoutes() as $route) {
                if ($route->uri() === 'user/confirm-password' && $route->getName() === 'password.confirm') {
                    $route->setAction(array_merge($route->getAction(), ['as' => 'password.confirm.fortify']));
                }
            }
        });
    }
}
