<div x-data="{ showSideBar: false }">
    {{-- Header Full Width (overlaps sidebar) --}}
    <div class="bg-primary fixed top-0 left-0 right-0 z-50 h-14 lg:h-16">
        <div class="flex items-center justify-between h-full px-4 lg:px-6">
            <!-- Left: Hamburger (mobile) + Logo + Title -->
            <div class="flex items-center gap-3">
                <i class="ph ph-list text-3xl cursor-pointer lg:hidden" x-on:click="showSideBar = !showSideBar"></i>
                <img src="{{ asset('favicon.svg') }}" class="w-9 lg:w-10" alt="Logo">
                <h1 class="text-l2 font-bold">Cerita Cireng</h1>
            </div>

            <!-- Right: Bell Icon -->
            <i class="ph ph-bell text-3xl"></i>
        </div>
    </div>

    {{-- Sidebar Overlay (Mobile only) --}}
    <div x-cloak x-show="showSideBar" class="fixed inset-0 z-30 bg-neutral-500/30 backdrop-blur-xs lg:hidden"
        x-on:click="showSideBar = false" x-transition.opacity>
    </div>

    {{-- Unified Sidebar (Mobile + Desktop) --}}
    <nav class="fixed left-0 top-0 w-56 h-screen bg-primary z-40 duration-300 overflow-y-auto
                lg:translate-x-0
                transition-transform"
        x-bind:class="showSideBar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
        <div class="pt-16 lg:pt-20 px-4 pb-4">
            {{-- Profile Section --}}
            <div class="px-3 py-4 border-dark border-b-2">
                <h1 class="text-reguler font-medium">Halo, {{ Auth::user()->display_name }}</h1>
                <p class="text-1">{{ Auth::user()->role->name }}</p>
            </div>

            {{-- Navigation Menu --}}
            <div class="nav-list mt-4 space-y-1">
                @foreach ($sidebarMenus as $menu)
                @if ($menu['route'] === '/logout')
                <button type="button" onclick="confirmLogout()"
                    class="flex px-3 py-2 items-center h-12 hover:bg-neutral-50/15 duration-300 relative w-full text-left rounded-lg">
                    <i class="ph ph-{{ $menu['icon'] }} text-2xl w-10 text-center"></i>
                    <p class="text-1 font-medium ml-2">{{ $menu['name'] }}</p>
                </button>
                @else
                <a href="{{ $menu['route'] }}"
                    class="flex px-3 py-2 items-center h-12 hover:bg-neutral-50/15 duration-300 relative rounded-lg {{ request()->is(ltrim($menu['route'], '/')) ? 'bg-neutral-50/15' : '' }}">
                    @if (request()->is(ltrim($menu['route'], '/')))
                    <div class="h-full absolute left-0 bg-neutral-50/90 w-[3px] top-0 rounded-r"></div>
                    @endif
                    <i class="ph ph-{{ $menu['icon'] }} text-2xl w-10 text-center"></i>
                    <p class="text-1 font-medium ml-2">{{ $menu['name'] }}</p>
                </a>
                @endif
                @endforeach
            </div>
        </div>
    </nav>

</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
    @csrf
</form>

<script>
    function confirmLogout() {
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Anda akan keluar dari sesi ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FFB504',
            cancelButtonColor: '#FF3704',
            confirmButtonText: 'Ya, Keluar!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        })
    }
</script>