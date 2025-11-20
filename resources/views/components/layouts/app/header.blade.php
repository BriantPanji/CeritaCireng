<div x-data="{ showSideBar: false }">
    {{-- Header --}}
    <div class="bg-primary h-[56px] flex w-full justify-between items-center p-4">
        <i class="ph ph-list text-3xl" x-on:click="showSideBar = !showSideBar"></i>
        <h1 class="text-l2 font-bold">Cerita Cireng</h1>
        <i class="ph ph-bell text-3xl"></i>
    </div>
    {{-- End of header --}}

    {{-- Sidebar overlay --}}
    <div x-cloak x-show="showSideBar" class="fixed inset-0 z-10 bg-neutral-500/30 backdrop-blur-xs"
        x-on:click="showSideBar = false" x-transition.opacity>
    </div>
    {{-- End of sidebar overlay --}}

    {{-- Sidebar --}}
    <nav x-cloak class="fixed top-0 w-60 h-screen bg-primary-300 z-20 duration-300"
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
                    <a href="{{ $menu['route'] }}"
                        class="flex px-[12px] py-[8px] items-center h-[56px] hover:bg-neutral-50/15 duration-300 relative {{ request()->is(ltrim($menu['route'], '/')) ? 'bg-neutral-50/10' : '' }}">
                        {{-- Validasi indikator --}}
                        @if (request()->is(ltrim($menu['route'], '/')))
                            <div class="h-full absolute left-0 bg-neutral-50/80 w-[3px] top-0"></div>
                        @endif

                        <i class="ph ph-{{ $menu['icon'] }} text-center text-2xl w-[45px]"></i>

                        <p class="text-1 w-full text-right font-medium ml-[12px]">{{ $menu['name'] }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </nav>
    {{-- End of sidebar --}}


</div>
