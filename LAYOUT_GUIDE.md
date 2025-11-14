# Panduan Penggunaan Layout Components

## Overview

Proyek ini menyediakan dua layout utama untuk membangun aplikasi:
1. **App Layout** - Layout untuk halaman aplikasi dengan header dan sidebar
2. **Auth Layout** - Layout untuk halaman autentikasi (login)

## Cara Menggunakan

### 1. Menggunakan Layout dengan Livewire Volt Component

Untuk menggunakan layout dalam Volt component, gunakan attribute `#[Layout()]`:

```php
<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public string $message = 'Hello World';
    
    public function updateMessage(): void
    {
        $this->message = 'Updated!';
    }
};
?>

<div>
    <h1>{{ $message }}</h1>
    <button wire:click="updateMessage">Update</button>
</div>
```

**Mengatur Title Halaman:**

Untuk mengatur title halaman yang berbeda-beda di setiap Volt component, gunakan attribute `#[Title()]`:

```php
<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app'), Title('Dashboard - Cerita Cireng')] class extends Component {
    // Component logic
};
?>
```

**Catatan:**
- Attribute `#[Title()]` akan mengatur judul yang muncul di browser tab
- Anda bisa menggunakan multiple attributes dengan memisahkan menggunakan koma
- Title akan otomatis ditampilkan di tag `<title>` pada layout

### 2. Menggunakan Layout dengan Livewire Component (Class-based)

Untuk menggunakan layout dengan Livewire component biasa (bukan Volt), ada beberapa cara:

#### a. Menggunakan Property `$layout`

Di dalam class Livewire component Anda:

```php
<?php

namespace App\Livewire;

use Livewire\Component;

class Dashboard extends Component
{
    protected $layout = 'components.layouts.app';
    
    public int $totalUsers = 0;
    
    public function mount()
    {
        $this->totalUsers = User::count();
    }
    
    public function render()
    {
        return view('livewire.dashboard');
    }
}
```

#### b. Menggunakan Method `render()` dengan `->layout()`

```php
<?php

namespace App\Livewire;

use Livewire\Component;

class Inventory extends Component
{
    public function render()
    {
        return view('livewire.inventory')
            ->layout('components.layouts.app');
    }
}
```

#### c. Mengatur Title dengan `->title()`

```php
<?php

namespace App\Livewire;

use Livewire\Component;

class Reports extends Component
{
    public function render()
    {
        return view('livewire.reports')
            ->layout('components.layouts.app')
            ->title('Laporan - Cerita Cireng');
    }
}
```

#### d. Passing Data ke Layout

```php
<?php

namespace App\Livewire;

use Livewire\Component;

class Settings extends Component
{
    public function render()
    {
        return view('livewire.settings')
            ->layout('components.layouts.app', [
                'title' => 'Pengaturan - Cerita Cireng'
            ]);
    }
}
```

**Catatan untuk Livewire Class Components:**
- File view component terpisah dari class (di `resources/views/livewire/`)
- Gunakan property `$layout` untuk set layout default
- Method `->layout()` di `render()` lebih fleksibel
- Method `->title()` untuk set page title
- Bisa pass data ke layout dengan array di parameter kedua `->layout()`

### 3. Menggunakan Layout dengan Blade Views

Untuk menggunakan layout dalam Blade view biasa, gunakan syntax komponen Blade `<x-layouts.*>`:

```blade
<x-layouts.app title="Judul Halaman">
    <div class="p-4">
        <h1>Konten halaman Anda</h1>
    </div>
</x-layouts.app>
```

## Layout yang Tersedia

### App Layout (`components.layouts.app`)

Layout ini digunakan untuk halaman-halaman utama aplikasi. Fitur:
- Header dengan menu hamburger
- Sidebar navigasi
- Notifikasi bell
- Responsive design

**Parameter:**
- `title` (string, optional) - Judul halaman yang akan muncul di tab browser. Default: "Cerita Cireng"

**Contoh:**
```blade
<x-layouts.app title="Dashboard">
    <!-- Konten dashboard -->
</x-layouts.app>
```

### Auth Layout (`components.layouts.auth`)

Layout ini digunakan untuk halaman autentikasi seperti login, register, dll.

**Fitur:**
- Design minimal
- Background neutral
- Tidak ada header/sidebar
- Fokus pada form autentikasi

**Contoh:**
```php
// Dalam Volt component
new #[Layout('components.layouts.auth')] class extends Component {
    // Login logic
};
```

## Contoh Implementasi

### Contoh 1: Halaman Dashboard dengan Volt

File: `resources/views/livewire/dashboard.blade.php`

```php
<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;
use App\Models\User;

new #[Layout('components.layouts.app'), Title('Dashboard - Cerita Cireng')] class extends Component {
    public int $totalUsers = 0;
    
    public function mount(): void
    {
        $this->totalUsers = User::count();
    }
};
```

## Mengatur Title Halaman

### Untuk Volt Components

Gunakan attribute `#[Title()]` untuk mengatur judul halaman yang berbeda-beda:

```php
<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

// Halaman Dashboard
new #[Layout('components.layouts.app'), Title('Dashboard - Cerita Cireng')] class extends Component {
    // ...
};

// Halaman Inventory
new #[Layout('components.layouts.app'), Title('Inventory - Cerita Cireng')] class extends Component {
    // ...
};

// Halaman Laporan
new #[Layout('components.layouts.app'), Title('Laporan - Cerita Cireng')] class extends Component {
    // ...
};
?>
```

### Untuk Livewire Class Components

Gunakan method `->title()` di dalam `render()`:

```php
<?php

namespace App\Livewire;

use Livewire\Component;

// Dashboard Component
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard')
            ->layout('components.layouts.app')
            ->title('Dashboard - Cerita Cireng');
    }
}

// Inventory Component
class Inventory extends Component
{
    public function render()
    {
        return view('livewire.inventory')
            ->layout('components.layouts.app')
            ->title('Inventory - Cerita Cireng');
    }
}

// Laporan Component
class Reports extends Component
{
    public function render()
    {
        return view('livewire.reports')
            ->layout('components.layouts.app')
            ->title('Laporan - Cerita Cireng');
    }
}
```

### Untuk Blade Views

Pass attribute `title` ke component layout:

```blade
{{-- Dashboard --}}
<x-layouts.app title="Dashboard - Cerita Cireng">
    <!-- Konten -->
</x-layouts.app>

{{-- Inventory --}}
<x-layouts.app title="Inventory - Cerita Cireng">
    <!-- Konten -->
</x-layouts.app>

{{-- Laporan --}}
<x-layouts.app title="Laporan - Cerita Cireng">
    <!-- Konten -->
</x-layouts.app>
```

**Tips:**
- Gunakan format yang konsisten, misalnya: "Nama Halaman - Cerita Cireng"
- Title akan muncul di tab browser dan bookmarks
- Jika tidak diset, akan menggunakan default "Cerita Cireng"

## Contoh Implementasi

### Contoh 1: Halaman Dashboard dengan Volt

File: `resources/views/livewire/dashboard.blade.php`

```php
<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;
use App\Models\User;

new #[Layout('components.layouts.app'), Title('Dashboard - Cerita Cireng')] class extends Component {
    public int $totalUsers = 0;
    
    public function mount(): void
    {
        $this->totalUsers = User::count();
    }
};
?>

<div class="p-4">
    <h1 class="text-h2 font-bold">Dashboard</h1>
    <p>Total Users: {{ $totalUsers }}</p>
</div>
```

### Contoh 2: Halaman Inventory dengan Blade View

File: `resources/views/inventory.blade.php`

```blade
<x-layouts.app title="Inventory">
    <div class="p-3">
        <h1 class="text-l1 font-semibold">Daftar Inventory</h1>
        <!-- Tabel inventory -->
    </div>
</x-layouts.app>
```

### Contoh 3: Halaman dengan Livewire Class Component

**File Class:** `app/Livewire/ProductList.php`

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

class ProductList extends Component
{
    public $search = '';
    public $products = [];
    
    public function mount()
    {
        $this->loadProducts();
    }
    
    public function loadProducts()
    {
        $this->products = Product::where('name', 'like', '%' . $this->search . '%')
            ->get();
    }
    
    public function render()
    {
        return view('livewire.product-list')
            ->layout('components.layouts.app')
            ->title('Daftar Produk - Cerita Cireng');
    }
}
```

**File View:** `resources/views/livewire/product-list.blade.php`

```blade
<div class="p-4">
    <h1 class="text-h2 font-bold mb-4">Daftar Produk</h1>
    
    <input 
        type="text" 
        wire:model.live="search" 
        placeholder="Cari produk..."
        class="border px-4 py-2 rounded"
    />
    
    <div class="mt-4">
        @foreach($products as $product)
            <div class="border p-3 mb-2">
                <h3>{{ $product->name }}</h3>
                <p>{{ $product->description }}</p>
            </div>
        @endforeach
    </div>
</div>
```

**Route:** `routes/web.php`

```php
use App\Livewire\ProductList;

Route::get('/products', ProductList::class)->name('products');
```

### Contoh 4: Halaman Login dengan Volt

File: `resources/views/livewire/auth/login.blade.php`

```php
<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth'), Title('Login - Cerita Cireng')] class extends Component {
    public string $username = '';
    public string $password = '';
    
    public function login(): void
    {
        // Login logic
    }
};
?>

<div class="flex items-center justify-center min-h-screen">
    <form wire:submit="login">
        <!-- Form fields -->
    </form>
</div>
```

## Troubleshooting

### Error: "Undefined variable $slot"
**Solusi:** Pastikan Anda telah membuat component class untuk layout. Component class harus ada di:
- `app/View/Components/Layouts/App.php`
- `app/View/Components/Layouts/Auth.php`

### Error: "Undefined variable $title"
**Solusi:** 
1. Pastikan component class `App.php` memiliki property `$title` di constructor
2. Atau berikan nilai default saat menggunakan: `<x-layouts.app title="Judul">`

### Layout tidak terbaca
**Solusi:**
1. Clear cache Laravel: `php artisan view:clear`
2. Clear config cache: `php artisan config:clear`
3. Pastikan Volt service provider terdaftar di `config/app.php`

## Halaman Contoh

Untuk melihat contoh penggunaan layout, kunjungi route `/example` setelah menjalankan aplikasi.

## Fitur Sidebar

Sidebar menu dikonfigurasi di `app/Providers/AppServiceProvider.php`. Untuk menambah/mengubah menu:

```php
View::share('sidebarMenus', [
    [
        'name' => 'Dashboard',
        'icon' => 'list-dashes',  // Phosphor icon name
        'route' => '/dashboard',
    ],
    // Tambahkan menu lainnya...
]);
```

## Integrasi dengan Livewire

Layout ini sudah terintegrasi dengan Livewire 3. Anda dapat menggunakan semua fitur Livewire seperti:
- `wire:model`
- `wire:click`
- `wire:submit`
- Real-time updates
- Loading states

## CSS Framework

Proyek ini menggunakan Tailwind CSS dengan kustomisasi warna:
- `primary` - Warna utama aplikasi
- `secondary` - Warna sekunder
- `dark` - Warna teks gelap
- `neutral-*` - Warna netral

Lihat `tailwind.config.js` untuk konfigurasi lengkap.
