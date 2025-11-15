<?php

namespace App\View\Components\Layouts;

use Illuminate\View\Component;
use Illuminate\View\View;

class App extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $title = 'Cerita Cireng'
    ) {
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.layouts.app');
    }
}
