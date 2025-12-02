<?php
return [
    [
        'name' => 'Dashboard',
        'icon' => 'list-dashes',
        'route' => '/dashboard',
        'roles' => ['dev', 'admin', 'inventaris', 'kurir', 'staff'],
    ],
    [
        'name' => 'Pengantaran',
        'icon' => 'truck',
        'route' => '/delivery',
        'roles' => ['dev', 'admin', 'inventaris', 'kurir', 'staff'],
    ],
    [
        'name' => 'Inventory',
        'icon' => 'warehouse',
        'route' => '/inventory',
        'roles' => ['dev', 'admin', 'inventaris'],
    ],
    [
        'name' => 'Stok',
        'icon' => 'stack',
        'route' => '/stok',
        'roles' => ['dev', 'admin', 'inventaris'],
    ],
    [
        'name' => 'Laporan',
        'icon' => 'files',
        'route' => '/daily-reports',
        'roles' => ['dev', 'admin', 'inventaris'],
    ],
    [
        'name' => 'Buat Laporan Harian',
        'icon' => 'note-pencil',
        'route' => '/daily-reports/create',
        'roles' => ['dev', 'admin', 'staff'],
    ],
    [
        'name' => 'Manajemen User',
        'icon' => 'user-gear',
        'route' => '/user-management',
        'roles' => ['dev', 'admin'],
    ],
    [
        'name' => 'Manajemen Outlet',
        'icon' => 'storefront',
        'route' => '/outlet-management',
        'roles' => ['dev', 'admin'],
    ],
    [
        'name' => 'Attendance',
        'icon' => 'user-focus',
        'route' => '/attendance',
        'roles' => ['dev', 'admin', 'staff'],
    ],
    [
        'name' => 'Absensi',
        'icon' => 'identification-badge',
        'route' => '/absensi',
        'roles' => ['dev', 'admin', 'inventaris', 'kurir', 'staff'],
    ],
    [
        'name' => 'Log Aktivitas',
        'icon' => 'note-pencil',
        'route' => '/log-aktivitas',
        'roles' => ['dev', 'admin'],
    ],
    [
        'name' => 'Keluar',
        'icon' => 'sign-out',
        'route' => '/logout',
        'roles' => ['dev', 'admin', 'inventaris', 'kurir', 'staff'],
    ],
];
