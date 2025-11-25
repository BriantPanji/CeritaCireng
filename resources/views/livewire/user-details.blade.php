<div class="space-y-4">

    <!-- Back -->
    <a href="{{ route('users.management') }}" class="inline-flex items-center text-sm text-blue-700 hover:underline">
        <i class="text-2xl ph ph-caret-circle-left pr-2"></i> Kembali ke Manajemen User
    </a>

    <!-- HEADER -->
    <div class="bg-white shadow p-5 rounded-lg flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold">{{ $user->display_name }}</h2>
            <p class="text-gray-500 text-sm">{{ '@' . $user->username }}</p>
        </div>

        <div class="text-center">
            <span class="px-3 py-1 rounded-full text-xs border 
                {{ $user->status === 'AKTIF' ? 'border-blue-700 text-blue-700' : 'border-red-700 text-red-700' }}">
                {{ $user->status }}
            </span>
            <div class="mt-1 text-sm text-gray-600">{{ $user->role_name }}</div>
        </div>
    </div>


    <!-- Overview -->
    <div class="flex flex-col md:flex-row gap-4">

        <div class="flex-1 flex flex-col gap-4">

            <!-- Profil -->
            <div class="bg-white shadow rounded-lg p-4 space-y-3 md:flex-[1.5]">
                <h3 class="font-semibold text-sm text-gray-700">Informasi Pengguna</h3>

                <div class="relative overflow-x-auto scroll-smooth">
                    <table class="min-w-max text-left text-reguler [&_td]:py-0.5 [&_th]:py-0.5">
                        <tbody>
                            <tr>
                                <th class="font-semibold whitespace-nowrap">Nama</th>
                                <th class="px-1 font-semibold">:</th>
                                <td class="px-4">{{ $user->display_name }}</td>
                            </tr>
                            <tr>
                                <th class="font-semibold whitespace-nowrap">Username</th>
                                <th class="px-1 font-semibold">:</th>
                                <td class="px-4">{{ $user->username }}</td>
                            </tr>
                            <tr>
                                <th class="font-semibold whitespace-nowrap">No. Telepon</th>
                                <th class="px-1 font-semibold">:</th>
                                <td class="px-4">{{ $user->phone }}</td>
                            </tr>
                            <tr>
                                <th class="font-semibold whitespace-nowrap">Outlet</th>
                                <th class="px-1 font-semibold">:</th>
                                <td class="px-4">{{ $user->outlet_name }}</td>
                            </tr>
                            <tr>
                                <th class="font-semibold whitespace-nowrap">Role</th>
                                <th class="px-1 font-semibold">:</th>
                                <td class="px-4">{{ $user->role_name }}</td>
                            </tr>
                            <tr>
                                <th class="font-semibold whitespace-nowrap">Dibuat</th>
                                <th class="px-1 font-semibold">:</th>
                                <td class="px-4">{{ $user->created_at }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mistake -->
            <div class="bg-white shadow rounded-lg p-4 space-y-3">
                <h3 class="font-semibold text-sm text-gray-700">Mistake</h3>

                <div class="relative overflow-x-auto scroll-smooth">
                    <table class="min-w-max text-left text-reguler [&_td]:py-0.5 [&_th]:py-0.5">
                        <tbody>
                            <tr>
                                <th class="font-semibold whitespace-nowrap">Dilaporkan</th>
                                <th class="px-1 font-semibold">:</th>
                                <td class="px-4">{{ $user->mistakes_reported }}</td>
                            </tr>
                            <tr>
                                <th class="font-semibold whitespace-nowrap">Dikonfirmasi</th>
                                <th class="px-1 font-semibold">:</th>
                                <td class="px-4">{{ $user->mistakes_confirmed }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>


        <!-- ========================================= -->
        <!-- KANAN: Absensi + Delivery + Expenses -->
        <!-- ========================================= -->
        <div class="flex-1 flex flex-col gap-4">

            <!-- Absensi -->
            <div class="bg-white shadow rounded-lg p-4 space-y-3">
                <h3 class="font-semibold text-sm text-gray-700">Rekap Absensi</h3>

                <div class="relative overflow-x-auto scroll-smooth">
                    <table class="min-w-max text-left text-reguler [&_td]:py-0.5 [&_th]:py-0.5">
                        <tbody>
                            <tr>
                                <th class="font-semibold whitespace-nowrap">Hadir</th>
                                <th class="px-1 font-semibold">:</th>
                                <td class="px-4">{{ $user->total_hadir }}</td>
                            </tr>
                            <tr>
                                <th class="font-semibold whitespace-nowrap">Izin</th>
                                <th class="px-1 font-semibold">:</th>
                                <td class="px-4">{{ $user->total_izin }}</td>
                            </tr>
                            <tr>
                                <th class="font-semibold whitespace-nowrap">Sakit</th>
                                <th class="px-1 font-semibold">:</th>
                                <td class="px-4">{{ $user->total_sakit }}</td>
                            </tr>
                            <tr>
                                <th class="font-semibold whitespace-nowrap">Absen</th>
                                <th class="px-1 font-semibold">:</th>
                                <td class="px-4">{{ $user->total_absen }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Delivery -->
            <div class="bg-white shadow rounded-lg p-4 space-y-3">
                <h3 class="font-semibold text-sm text-gray-700">Rekap Delivery</h3>

                <div class="relative overflow-x-auto scroll-smooth">
                    <table class="min-w-max text-left text-reguler [&_td]:py-0.5 [&_th]:py-0.5">
                        <tbody>
                            <tr>
                                <th class="font-semibold whitespace-nowrap">Kurir</th>
                                <th class="px-1 font-semibold">:</th>
                                <td class="px-4">{{ $user->delivery_as_kurir }}</td>
                            </tr>
                            <tr>
                                <th class="font-semibold whitespace-nowrap">Inventaris</th>
                                <th class="px-1 font-semibold">:</th>
                                <td class="px-4">{{ $user->delivery_as_inventaris }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Other Expenses -->
            <div class="bg-white shadow rounded-lg p-4 space-y-3">
                <h3 class="font-semibold text-sm text-gray-700">Other Expenses</h3>

                <div class="relative overflow-x-auto scroll-smooth">
                    <table class="min-w-max text-left text-reguler [&_td]:py-0.5 [&_th]:py-0.5">
                        <tbody>
                            <tr>
                                <th class="font-semibold whitespace-nowrap">Pengeluaran Dilaporkan</th>
                                <th class="px-1 font-semibold">:</th>
                                <td class="px-4">{{ $user->total_other_expenses }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

</div>
