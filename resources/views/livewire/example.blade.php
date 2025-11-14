<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public string $message = 'Selamat datang di Cerita Cireng!';

    public function updateMessage(): void
    {
        $this->message = 'Pesan telah diperbarui pada ' . now()->format('H:i:s');
    }
}; ?>

<div class="p-4">
    <div class="max-w-2xl mx-auto">
        {{-- Card dengan contoh Volt component --}}
        <div class="bg-white rounded-lg shadow-reguler p-6">
            <h2 class="text-l1 font-semibold text-dark mb-4">Contoh Volt Component</h2>
            
            <div class="mb-4">
                <p class="text-reguler text-dark">{{ $message }}</p>
            </div>

            <button 
                wire:click="updateMessage"
                class="bg-primary text-white px-4 py-2 rounded-lg font-semibold text-reguler hover:bg-primary-200 transition-colors"
            >
                Update Pesan
            </button>
        </div>

        {{-- Informasi cara penggunaan --}}
        <div class="mt-6 bg-neutral-50 rounded-lg p-6">
            <h3 class="text-reguler font-semibold text-dark mb-3">Cara Menggunakan Layout:</h3>
            
            <div class="space-y-2 text-1 text-dark">
                <p><strong>1. Untuk Volt Component:</strong></p>
                <pre class="bg-white p-3 rounded border border-neutral-100 overflow-x-auto"><code>use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component {
    // Component logic here
};</code></pre>

                <p class="mt-4"><strong>2. Untuk Blade Views:</strong></p>
                <pre class="bg-white p-3 rounded border border-neutral-100 overflow-x-auto"><code>&lt;x-layouts.app title="Judul Halaman"&gt;
    &lt;!-- Konten halaman di sini --&gt;
&lt;/x-layouts.app&gt;</code></pre>

                <p class="mt-4"><strong>3. Layout yang tersedia:</strong></p>
                <ul class="list-disc list-inside pl-4">
                    <li><code>components.layouts.app</code> - Layout untuk halaman aplikasi (dengan header & sidebar)</li>
                    <li><code>components.layouts.auth</code> - Layout untuk halaman autentikasi (login)</li>
                </ul>
            </div>
        </div>
    </div>
</div>
