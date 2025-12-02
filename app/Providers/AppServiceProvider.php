<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
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


      //        $tmp_dir = ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir();
        //        die($tmp_dir);


        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }


        Gate::define('checkrole', function (User $user, string $roles) {
            if ($roles === '*' || $roles === 'all') return true;
            $allowed = array_map('trim', explode(',', $roles));
            return in_array($user->role->name, $allowed);
        });


        Blade::directive('role', function ($expression) {
            // Remove parentheses and quotes, then split by comma
            $expression = trim($expression, "()");
            $roles = array_map(function ($role) {
                return trim($role, " '\"");
            }, explode(',', $expression));
            $roleString = implode(',', $roles);

            return "<?php if(auth()->check() && Gate::allows('checkrole', '$roleString')): ?>";
        });
        Blade::directive('endrole', function () {
            return "<?php endif; ?>";
        });



        Blade::directive('convertRupiah', function ($money) {
            return "<?php echo 'Rp' . number_format({$money}, 0, ',', '.'); ?>";
        });
    }
}
