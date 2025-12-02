<div x-data="{ showSideBar: false }">
    {{-- Header Mobile --}}
    <div class="bg-primary h-[56px] flex w-full justify-between items-center px-4 lg:hidden">
        <i class="ph ph-list text-3xl" x-on:click="showSideBar = !showSideBar"></i>
        <h1 class="text-l2 font-bold">Cerita Cireng</h1>
        <i class="ph ph-bell text-3xl"></i>
    </div>

    {{-- Header Desktop --}}
    <div class="bg-primary fixed right-0 top-0 hidden lg:block z-50 h-[65px] lg:left-64 xl:left-80">
        <div class="flex items-center w-full h-full px-6">
            <!-- Logo (Kiri) -->
            <div class="flex-shrink-0">
                <img src="{{ asset('favicon.svg') }}" class="w-[40px]" alt="">
            </div>
            
            <!-- Teks (TENGAH) -->
            <div class="flex-1 flex justify-center">
                <h1 class="text-l2 font-bold">Cerita Cireng</h1>
            </div>
            
            <!-- Bell (Kanan) -->
            <div class="flex-shrink-0">
                <i class="ph ph-bell text-3xl"></i>
            </div>
        </div>
    </div>

    {{-- Sidebar overlay --}}
    <div x-cloak x-show="showSideBar" class="fixed inset-0 z-10 bg-neutral-500/30 backdrop-blur-xs lg:hidden"
        x-on:click="showSideBar = false" x-transition.opacity>
    </div>

    {{-- Sidebar Mobile --}}
    <nav x-cloak class="fixed top-0 w-60 h-screen bg-primary-300 z-20 duration-300 lg:hidden no-scrollbar overflow-y-auto"
        x-bind:class="showSideBar ? '-translate-x-0' : '-translate-x-70'">
        <div class="p-[8px] pr-4 mt-[34px]">
            <div class="px-[12px] py-[8px] flex border-dark border-b-2">
                <img src="{{ asset('images/Conan.jpg') }}" class="rounded-full" alt="Foto Profil" width="42">
                <div class="ml-[12px]">
                    <h1 class="text-reguler font-medium">Hi, John Doe</h1>
                    <p class="text-1">Owner</p>
                </div>
            </div>
            <div class="nav-list mt-[12px] relative">
                @foreach ($sidebarMenus as $menu)
                @if ($menu['route'] === '/logout')
                <button type="button" onclick="confirmLogout()"
                    class="flex px-[12px] py-[8px] items-center h-[56px] hover:bg-neutral-50/15 duration-300 relative w-full text-left">
                    <i class="ph ph-{{ $menu['icon'] }} text-center text-2xl w-[45px]"></i>
                    <p class="text-1 w-full text-right font-medium ml-[12px]">{{ $menu['name'] }}</p>
                </button>
                @else
                <a href="{{ $menu['route'] }}"
                    class="flex px-[12px] py-[8px] items-center h-[56px] hover:bg-neutral-50/15 duration-300 relative {{ request()->is(ltrim($menu['route'], '/')) ? 'bg-neutral-50/10' : '' }}">
                    @if (request()->is(ltrim($menu['route'], '/')))
                    <div class="h-full absolute left-0 bg-neutral-50/80 w-[3px] top-0"></div>
                    @endif
                    <i class="ph ph-{{ $menu['icon'] }} text-center text-2xl w-[45px]"></i>
                    <p class="text-1 w-full text-right font-medium ml-[12px]">{{ $menu['name'] }}</p>
                </a>
                @endif
                @endforeach
            </div>
        </div>
    </nav>

    {{-- Sidebar Desktop --}}
    <nav class="fixed top-0 left-0 h-screen w-64 xl:w-80 bg-primary hidden lg:block overflow-y-auto no-scrollbar">
        <div class="p-[8px] pr-4">
            <div class="px-[12px] pb-[13px] flex border-dark border-b-2 mt-[64px]">
                <img src="{{ asset('images/Conan.jpg') }}" class="rounded-full" alt="Foto Profil" width="42">
                <div class="ml-[12px]">
                    <h1 class="text-reguler font-medium">Hi, John Doe</h1>
                    <p class="text-1">Owner</p>
                </div>
            </div>
            <div class="nav-list mt-4">
                @foreach ($sidebarMenus as $menu)
                @if ($menu['route'] === '/logout')
                <button type="button" onclick="confirmLogout()"
                    class="flex px-[12px] py-[8px] items-center h-[56px] hover:bg-neutral-50/25 duration-300 relative w-full text-left">
                    <i class="ph ph-{{ $menu['icon'] }} text-center text-2xl w-[45px]"></i>
                    <p class="text-1 w-full text-left font-medium ml-[12px]">{{ $menu['name'] }}</p>
                </button>
                @else
                <a href="{{ $menu['route'] }}"
                    class="flex px-[12px] py-[8px] items-center h-[56px] hover:bg-neutral-50/25 duration-300 relative {{ request()->is(ltrim($menu['route'], '/')) ? 'bg-neutral-50/25' : '' }}">
                    @if (request()->is(ltrim($menu['route'], '/')))
                    <div class="h-full absolute left-0 bg-neutral-50/90 w-[3px] top-0"></div>
                    @endif
                    <i class="ph ph-{{ $menu['icon'] }} text-center text-2xl w-[45px]"></i>
                    <p class="text-1 w-full text-left font-medium ml-[12px]">{{ $menu['name'] }}</p>
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