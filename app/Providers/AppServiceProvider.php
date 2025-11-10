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
                'icon' => 'grip',
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
                'name' => 'Penerimaan Barang',
                'icon' => 'box-archive',
                'route' => '/penerimaan-barang',
            ],
            [
                'name' => 'Laporan',
                'icon' => 'files',
                'route' => '/laporan',
            ],
            [
                'name' => 'Manajemen User',
                'icon' => 'users-gear',
                'route' => '/manajemen-user',
            ],
            [
                'name' => 'Absensi',
                'icon' => 'calendar-users',
                'route' => '/absensi',
            ],
            [
                'name' => 'Log Aktivitas',
                'icon' => 'chart-line',
                'route' => '/log-aktivitas',
            ],
            [
                'name' => 'Keluar',
                'icon' => 'arrow-right-from-bracket',
                'route' => '/logout',
            ],
        ]);
    }
}
