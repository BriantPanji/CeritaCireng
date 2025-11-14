<?php

namespace App\Livewire;

use Livewire\Component;

class ExampleComponent extends Component
{
    public string $message = 'Ini adalah Livewire Class Component!';
    public int $counter = 0;

    public function increment()
    {
        $this->counter++;
    }

    public function updateMessage()
    {
        $this->message = 'Pesan diperbarui pada ' . now()->format('H:i:s');
    }

    public function render()
    {
        return view('livewire.example-component')
            ->layout('components.layouts.app')
            ->title('Contoh Livewire Component - Cerita Cireng');
    }
}
