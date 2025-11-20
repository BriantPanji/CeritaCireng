<?php

namespace App\Providers;

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
                'route' => '/manajemen-user',
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
    }
}
