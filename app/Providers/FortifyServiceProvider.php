<?php

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
        // Use the Livewire login view so Fortify can resolve the LoginViewResponse contract.
        Fortify::loginView(fn() => view('livewire.auth.login'));

        // Rate limiter for login attempts. Uses the configured Fortify username
        // (eg. 'username') combined with the request IP to prevent brute-force attacks.
        RateLimiter::for('login', function (Request $request) {
            $key = (string) $request->input(Fortify::username()) ?: $request->ip();

            return Limit::perMinute(5)->by($key . '|' . $request->ip());
        });
    }
}
