<x-layouts.app title="Dashboard">
    <div class="p-4 space-y-4">

        <!-- Search + Role -->
        <form method="GET" action="{{ route('users.management') }}" class="flex items-center justify-between">
            <div class="flex items-center bg-white p-2 rounded-lg w-full mr-3 shadow-sm border hover:border-primary">
                <i class="ph ph-magnifying-glass text-gray-400 text-base"></i>
                <input type="text"
                       name="search"
                       placeholder="Cari pengguna"
                       value="{{ request('search') }}"
                       class="ml-2 w-full outline-none text-sm">
            </div>

            <!-- Tombol Role -->
            <div x-data="{ open: false }" class="relative">
                <button type="button"
                        @click="open = !open"
                        class="bg-primary text-white px-4 py-1 rounded-lg shadow text-sm flex items-center gap-1">
                    Role <i class="ph ph-funnel-simple ml-1"></i>
                </button>

                <div x-show="open"
                     @click.outside="open = false"
                     class="absolute right-0 mt-2 bg-white shadow-lg rounded-lg w-44 text-sm z-50 overflow-hidden">
                    <form method="GET" action="{{ url('/user-management') }}" class="flex flex-col">
                        <button name="role" value=""
                                class="w-full text-left px-4 py-2 hover:bg-gray-300 border-b">
                            Semua Role
                        </button>
                        <button name="role" value="1"
                                class="w-full text-left px-4 py-2 hover:bg-gray-300 border-b">
                            Admin
                        </button>
                        <button name="role" value="2"
                                class="w-full text-left px-4 py-2 hover:bg-gray-300 border-b">
                            Inventaris
                        </button>
                        <button name="role" value="3"
                                class="w-full text-left px-4 py-2 hover:bg-gray-300 border-b">
                            Kurir
                        </button>
                        <button name="role" value="4"
                                class="w-full text-left px-4 py-2 hover:bg-gray-300">
                            Staff
                        </button>
                    </form>
                </div>
            </div>
        </form>

        <!-- Judul + Tambah -->
        <div class="flex justify-between items-center flex-wrap sm:flex-nowrap">
            <h2 class="font-semibold text-base">
                Semua Pengguna <span class="text-gray-400">{{ $users->total() }}</span>
            </h2>

            <button class="flex items-center gap-1 bg-white px-3 py-1 rounded-lg shadow text-sm border border-gray-400">
                Tambah Pengguna
                <i class="ph ph-plus text-sm"></i>
            </button>
        </div>

        <!-- ========================= -->
        <!--   ALL USER + DELETE BTN   -->
        <!-- ========================= -->

        <div 
            x-data="{
                selectAll: false,
                selectedUsers: [],
                toggleSelectAll() {
                    this.selectAll = !this.selectAll;
                    if (this.selectAll) {
                        this.selectedUsers = @json($users->pluck('id')->toArray());
                    } else {
                        this.selectedUsers = [];
                    }
                },
                toggleUser(id) {
                    if (this.selectedUsers.includes(id)) {
                        this.selectedUsers = this.selectedUsers.filter(x => x !== id);
                    } else {
                        this.selectedUsers.push(id);
                    }
                    this.selectAll = this.selectedUsers.length === @json($users->count());
                }
            }"
        >
            <!-- List Users -->
            <div class="bg-white rounded-lg shadow p-3">

                <!-- Header -->
                <div class="flex items-center border-b p-2 mb-2 hover:bg-gray-300 cursor-pointer"
                    @click="toggleSelectAll()">

                    <input type="checkbox" class="mr-3"
                        x-bind:checked="selectAll"
                        @click.stop
                        @change="toggleSelectAll()">

                    <span class="text-sm font-semibold ml-2">Nama Pengguna</span>
                </div>

                @forelse ($users as $user)
                <div class="flex items-center justify-between p-2 border-b last:border-0 hover:bg-gray-300 cursor-pointer"
                    @click="toggleUser({{ $user->id }})">

                    <div class="flex items-center">
                        <input type="checkbox"
                            class="mr-3"
                            x-bind:checked="selectedUsers.includes({{ $user->id }})"
                            @click.stop
                            @change="toggleUser({{ $user->id }})">

                        <span class="text-sm ml-2">{{ $user->display_name }}</span>
                    </div>

                    <a href="#" class="bg-primary text-white px-4 py-1 rounded-lg text-xs shadow"
                        @click.stop>
                        Detail
                    </a>
                </div>

                @empty
                    <p class="text-gray-500 text-sm text-center py-4">Tidak ada pengguna ditemukan.</p>
                @endforelse
            </div>


            <!-- Tombol Hapus Terpilih -->
            <form method="POST" action="{{ route('users.destroy') }}"
                class="flex justify-end mt-3"
                @submit.prevent="
                    $refs.hidden.value = JSON.stringify(selectedUsers);
                    $el.submit();
                ">
                @csrf
                @method('DELETE')

                <input type="hidden" name="ids" x-ref="hidden">

                <button type="submit"
                    class="bg-red-500 text-white px-4 py-1 rounded-lg shadow text-sm disabled:opacity-50 disabled:cursor-not-allowed w-21"
                    :disabled="selectedUsers.length === 0">
                    Hapus
                </button>
            </form>

        </div>


        <!-- PAGINATION -->
        <div class="fixed bottom-0 left-0 right-0 bg-white shadow-lg p-3 border-t z-50">
            <div class="flex justify-center">
                {{ $users->links('pagination::tailwind') }}
            </div>
        </div>

    </div>
</x-layouts.app>
