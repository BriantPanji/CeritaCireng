<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
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

//        $tmp_dir = ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir();
//        die($tmp_dir);


        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        View::share('sidebarMenus', [
            [
                'name' => 'Dashboard',
                'icon' => 'list-dashes',
                'route' => '/dashboard',
            ],
            [
                'name' => 'Pengantaran',
                'icon' => 'truck',
                'route' => '/pengantaran',
            ],
            [
                'name' => 'Inventory',
                'icon' => 'warehouse',
                'route' => '/inventory',
            ],
            [
                'name' => 'Laporan',
                'icon' => 'files',
                'route' => '/laporan',
            ],
            [
                'name' => 'Manajemen User',
                'icon' => 'user-gear',
                'route' => '/user-management',
            ],
            [
                'name' => 'Absensi',
                'icon' => 'identification-badge',
                'route' => '/absensi',
            ],
            [
                'name' => 'Log Aktivitas',
                'icon' => 'note-pencil',
                'route' => '/log-aktivitas',
            ],
            [
                'name' => 'Keluar',
                'icon' => 'sign-out',
                'route' => '/logout',
            ],
        ]);



        Gate::define('checkrole', function (User $user, string $roles) {
            if ($roles === '*' || $roles === 'all') return true;
            $allowed = array_map('trim', explode(',', $roles));
            return in_array($user->role->name, $allowed);
        });


        Blade::directive('role', function (...$roles) {
            $roleString = collect($roles)->map(fn($r) => trim($r, " '\""))->implode(',');
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
