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
        'route' => '/pengantaran',
        'roles' => ['dev', 'admin', 'inventaris', 'kurir', 'staff'],
    ],
    [
        'name' => 'Inventory',
        'icon' => 'warehouse',
        'route' => '/inventory',
        'roles' => ['dev', 'admin'],
    ],
    [
        'name' => 'Laporan',
        'icon' => 'files',
        'route' => '/laporan',
        'roles' => ['dev', 'admin', 'inventaris'],
    ],
    [
        'name' => 'Manajemen User',
        'icon' => 'user-gear',
        'route' => '/user-management',
        'roles' => ['dev', 'admin'],
    ],
    [
        'name' => 'Absensi',
        'icon' => 'identification-badge',
        'route' => '/absensi',
        'roles' => ['dev', 'admin', 'inventaris', 'kurir', 'staff'],
    ],
    [
        'name' => 'Manajemen Outlet',
        'icon' => 'storefront',
        'route' => '/outlet',
        'roles' => ['dev', 'admin'],
    ],
    [
        'name' => 'Keluar',
        'icon' => 'sign-out',
        'route' => '/logout',
        'roles' => ['dev', 'admin', 'inventaris', 'kurir', 'staff'],
    ],
];
