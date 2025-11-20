<div x-data="{ isOpen: false, isLoading: false }">
    {{-- Box Presensi --}}
    <div x-show="!isOpen" class="shadow-reguler px-2 py-3 rounded-md max-w-[600px] mx-auto">
        <h1 class="text-1 lg:text-reguler">Presensi</h1>
        <p class="text-reguler lg:text-l2 font-semibold">Anda belum absen hari ini</p>
        <button @click="isOpen = true"
            class="bg-primary text-white p-2 mt-2 rounded-lg hover:bg-primary-300 duration-300">
            Absen Sekarang
        </button>
    </div>

    {{-- Modal Presensi --}}
    <div :class="isOpen ? 'max-w-[600px] mx-auto text-right' : 'hidden'" x-show="isOpen"
        x-transition:enter="transition ease-out duration-500 transform"
        x-transition:enter="transition ease-out duration-400 transform" x-transition:enter-start="opacity-0 "
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-400 transform"
        x-transition:leave-start="opacity-100 scale-y-100" x-transition:leave-end="opacity-0 scale-y-75">

        <div class="flex justify-between items-center bg-primary-50 rounded-t-lg px-4 py-2">
            <h1 class="text-primary-300 font-bold text-reguler">Presensi</h1>
            <i @click="isOpen = false" class="fa-solid fa-xmark text-l2 cursor-pointer"></i>
        </div>

        <div class="px-4 py-3 text-reguler rounded-b-md shadow-reguler space-y-2">
            <div class="flex justify-between relative">
                <p class="before:content-[':'] before:block before:left-40 before:absolute">Nama</p>
                <p class="w-[45%] text-left">{{ Auth::user()->display_name }}</p>
            </div>
            <div class="flex justify-between relative">
                <p class="before:content-[':'] before:block before:left-40 before:absolute">Outlet</p>
                <p class="w-[45%] text-left">{{ $outlet->name }}</p>
            </div>
            <div class="flex justify-between relative">
                <p class="before:content-[':'] before:block before:left-40 before:absolute">Tanggal</p>
                <p class="w-[45%] text-left">
                    {{ now()->setTimezone('Asia/Jakarta')->translatedFormat('d F Y') }}
                </p>
            </div>
            <div class="flex justify-between relative">
                <p class="before:content-[':'] before:block before:left-40 before:absolute">Waktu Absen</p>
                <p class="w-[45%] text-left">15.00 - 16.30</p>
            </div>

            {{-- Tombol Absen --}}
            <div class="text-right mt-4">
                <button
                    @click="
                        isLoading = true;
                        setTimeout(() => {
                            isLoading = false;
                            isOpen = false;
                        }, 2000)
                    "
                    :disabled="isLoading"
                    :class="isLoading ? 'bg-gray-400 cursor-not-allowed' : 'bg-primary hover:bg-primary-300'"
                    class="text-white p-2 rounded-lg duration-300">

                    <template x-if="!isLoading">
                        <span>Absen Sekarang</span>
                    </template>
                    <template x-if="isLoading">
                        <div class="flex items-center gap-2">
                            <span>Memproses...</span>
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                </path>
                            </svg>
                        </div>
                    </template>
                </button>
            </div>
        </div>
    </div>
</div>
