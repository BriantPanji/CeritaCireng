@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-center w-full">
        <div class="flex items-center gap-1 sm:gap-2">
            {{-- Previous Button --}}
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 text-gray-400 bg-white border border-gray-300 cursor-default rounded-lg">
                    <i class="ph ph-caret-left text-base sm:text-lg"></i>
                </span>
            @else
                <button 
                    type="button"
                    wire:click="previousPage('page')" 
                    rel="prev" 
                    class="relative cursor-pointer inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-primary hover:text-white hover:border-primary transition duration-150 active:scale-95">
                    <i class="ph ph-caret-left text-base sm:text-lg"></i>
                </button>
            @endif

            {{-- Page Numbers --}}
            <div class="flex items-center gap-1">
                @php
                    $currentPage = $paginator->currentPage();
                    $lastPage = $paginator->lastPage();
                    
                    // Logic untuk menentukan halaman mana yang ditampilkan
                    if ($lastPage <= 5) {
                        $startPage = 1;
                        $endPage = $lastPage;
                    } else {
                        if ($currentPage <= 3) {
                            $startPage = 1;
                            $endPage = 5;
                        } elseif ($currentPage >= $lastPage - 2) {
                            $startPage = $lastPage - 4;
                            $endPage = $lastPage;
                        } else {
                            $startPage = $currentPage - 2;
                            $endPage = $currentPage + 2;
                        }
                    }
                @endphp

                {{-- First Page --}}
                @if ($startPage > 1)
                    <button 
                        type="button"
                        wire:click="gotoPage(1, 'page')" 
                        class="relative cursor-pointer inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-primary hover:text-white hover:border-primary transition duration-150 active:scale-95">
                        1
                    </button>
                    @if ($startPage > 2)
                        <span class="relative inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 text-xs sm:text-sm font-medium text-gray-700">
                            ...
                        </span>
                    @endif
                @endif

                {{-- Page Number Range --}}
                @for ($page = $startPage; $page <= $endPage; $page++)
                    @if ($page == $currentPage)
                        <span class="relative inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 text-xs sm:text-sm font-semibold text-white bg-primary border border-primary cursor-default rounded-lg shadow-sm">
                            {{ $page }}
                        </span>
                    @else
                        <button 
                            type="button"
                            wire:click="gotoPage({{ $page }}, 'page')" 
                            class="relative cursor-pointer inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-primary hover:text-white hover:border-primary transition duration-150 active:scale-95">
                            {{ $page }}
                        </button>
                    @endif
                @endfor

                {{-- Last Page --}}
                @if ($endPage < $lastPage)
                    @if ($endPage < $lastPage - 1)
                        <span class="relative inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 text-xs sm:text-sm font-medium text-gray-700">
                            ...
                        </span>
                    @endif
                    <button 
                        type="button"
                        wire:click="gotoPage({{ $lastPage }}, 'page')" 
                        class="relative cursor-pointer inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-primary hover:text-white hover:border-primary transition duration-150 active:scale-95">
                        {{ $lastPage }}
                    </button>
                @endif
            </div>

            {{-- Next Button --}}
            @if ($paginator->hasMorePages())
                <button 
                    type="button"
                    wire:click="nextPage('page')" 
                    rel="next" 
                    class="relative cursor-pointer inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-primary hover:text-white hover:border-primary transition duration-150 active:scale-95">
                    <i class="ph ph-caret-right text-base sm:text-lg"></i>
                </button>
            @else
                <span class="relative inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 text-gray-400 bg-white border border-gray-300 cursor-default rounded-lg">
                    <i class="ph ph-caret-right text-base sm:text-lg"></i>
                </span>
            @endif
        </div>
    </nav>

    {{-- Optional: Info Text --}}
    <div class="hidden sm:flex justify-center mt-3">
        <p class="text-xs text-gray-600 select-none">
            Menampilkan <span class="font-medium">{{ $paginator->firstItem() }}</span> sampai <span class="font-medium">{{ $paginator->lastItem() }}</span> dari <span class="font-medium">{{ $paginator->total() }}</span> hasil
        </p>
    </div>
@endif