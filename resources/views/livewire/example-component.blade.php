<div class="p-4">
    <div class="max-w-2xl mx-auto">
        {{-- Card dengan contoh Livewire Class Component --}}
        <div class="bg-white rounded-lg shadow-reguler p-6">
            <h2 class="text-l1 font-semibold text-dark mb-4">Contoh Livewire Class Component</h2>
            
            <div class="mb-4">
                <p class="text-reguler text-dark mb-2">{{ $message }}</p>
                <p class="text-reguler text-dark">Counter: <strong>{{ $counter }}</strong></p>
            </div>

            <div class="flex gap-2">
                <button 
                    wire:click="updateMessage"
                    class="bg-primary text-white px-4 py-2 rounded-lg font-semibold text-reguler hover:bg-primary-200 transition-colors"
                >
                    Update Pesan
                </button>
                
                <button 
                    wire:click="increment"
                    class="bg-secondary text-white px-4 py-2 rounded-lg font-semibold text-reguler hover:bg-secondary-200 transition-colors"
                >
                    Tambah Counter
                </button>
            </div>
        </div>

        {{-- Informasi cara penggunaan --}}
        <div class="mt-6 bg-neutral-50 rounded-lg p-6">
            <h3 class="text-reguler font-semibold text-dark mb-3">Cara Menggunakan Livewire Class Component:</h3>
            
            <div class="space-y-4 text-1 text-dark">
                <div>
                    <p class="font-semibold mb-2">1. Buat Class Component:</p>
                    <pre class="bg-white p-3 rounded border border-neutral-100 overflow-x-auto text-xs"><code>namespace App\Livewire;

use Livewire\Component;

class ExampleComponent extends Component
{
    public string $message = 'Hello';
    
    public function render()
    {
        return view('livewire.example-component')
            ->layout('components.layouts.app')
            ->title('Page Title');
    }
}</code></pre>
                </div>

                <div>
                    <p class="font-semibold mb-2">2. Buat View Blade:</p>
                    <pre class="bg-white p-3 rounded border border-neutral-100 overflow-x-auto text-xs"><code>&lt;div&gt;
    &lt;h1&gt;{{ $message }}&lt;/h1&gt;
    &lt;button wire:click="updateMessage"&gt;Update&lt;/button&gt;
&lt;/div&gt;</code></pre>
                </div>

                <div>
                    <p class="font-semibold mb-2">3. Daftarkan Route:</p>
                    <pre class="bg-white p-3 rounded border border-neutral-100 overflow-x-auto text-xs"><code>use App\Livewire\ExampleComponent;

Route::get('/example-component', ExampleComponent::class);</code></pre>
                </div>

                <div class="mt-4 p-3 bg-blue-50 rounded border border-blue-200">
                    <p class="font-semibold text-blue-800 mb-2">Perbedaan dengan Volt:</p>
                    <ul class="list-disc list-inside space-y-1 text-blue-700">
                        <li>Class terpisah dari view</li>
                        <li>Lebih cocok untuk logic yang kompleks</li>
                        <li>Bisa di-reuse dengan mudah</li>
                        <li>Testing lebih mudah</li>
                    </ul>
                </div>

                <div class="mt-4 p-3 bg-green-50 rounded border border-green-200">
                    <p class="font-semibold text-green-800 mb-2">Dokumentasi lengkap:</p>
                    <p class="text-green-700">Lihat <code>LAYOUT_GUIDE.md</code> bagian "Menggunakan Layout dengan Livewire Component (Class-based)"</p>
                </div>
            </div>
        </div>
    </div>
</div>
