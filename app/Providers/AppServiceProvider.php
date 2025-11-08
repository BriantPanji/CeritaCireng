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
                'fontSize' => '28px',
            ],
            [
                'name' => 'Pengantaran',
                'icon' => 'truck',
                'route' => '/pengantaran',
                'fontSize' => '28px',
            ],
            [
                'name' => 'Inventory',
                'icon' => 'warehouse',
                'route' => '/inventory',
                'fontSize' => '28px',
            ],
            [
                'name' => 'Penerimaan Barang',
                'icon' => 'box-archive',
                'route' => '/penerimaan-barang',
                'fontSize' => '28px',
            ],
            [
                'name' => 'Laporan',
                'icon' => 'files',
                'route' => '/laporan',
                'fontSize' => '28px',
            ],
            [
                'name' => 'Manajemen User',
                'icon' => 'users-gear',
                'route' => '/manajemen-user',
                'fontSize' => '28px',
            ],
            [
                'name' => 'Absensi',
                'icon' => 'calendar-users',
                'route' => '/absensi',
                'fontSize' => '28px',
            ],
            [
                'name' => 'Log Aktivitas',
                'icon' => 'chart-line',
                'route' => '/log-aktivitas',
                'fontSize' => '28px',
            ],
            [
                'name' => 'Keluar',
                'icon' => 'arrow-right-from-bracket',
                'route' => '/logout',
                'fontSize' => '28px',
            ],
        ]);
    }
}
