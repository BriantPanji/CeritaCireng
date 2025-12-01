<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
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
        View::composer('*', function ($view) {
            $user = Auth::user();

            if (!$user) {
                View::share('sidebarMenus', collect());
                return;
            }

            $menus = collect(config('sidebar'))
                ->filter(function ($menu) use ($user) {
                    return in_array($user->role->name, $menu['roles']);
                })
                ->values();

            View::share('sidebarMenus', $menus);
        });



        Blade::directive('convertRupiah', function ($money) {
            return "<?php echo 'Rp' . number_format({$money}, 0, ',', '.'); ?>";
        });
    }
}
