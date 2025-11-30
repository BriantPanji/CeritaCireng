<!DOCTYPE html>
<html lang="en" class="text-dark">
@props(['title' => ''])

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'Cerita Cireng' }}</title>

    {{-- icon browser --}}
    <link rel="icon" href="{{ asset('favicon.svg') }}?v={{ now()->timestamp }}">

    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&display=swap"
        rel="stylesheet">

    {{-- Tailwind CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Livewire Styles --}}
    @livewireStyles

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@5.0.27/dark.min.css">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.7.2/css/all.css">

    <!-- Font phospohor -->
    <link rel="stylesheet" type="text/css"
        href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/regular/style.css" />
    <link rel="stylesheet" type="text/css"
        href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/fill/style.css" />

    {{-- Alpine JS --}}
    {{-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}

    {{-- Chart JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    {{-- Sweet Alert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="text-dark lg:grid lg:grid-cols-12 lg:overflow-x-auto" 
      x-data
      @modal-opened.window="document.body.style.overflow = 'hidden'"
      @modal-closed.window="document.body.style.overflow = 'auto'">
    <x-layouts.app.navbar />
    <main class=" bg-neutral-25 lg:col-span-9 xl:col-span-10 min-h-screen relative ">
        <div class=" w-full justify-around items-center bg-primary fixed right-0 left-0 top-0 hidden lg:block z-50">
            <div class="flex justify-around items-center w-full h-[65px]">
                <img src={{ asset('favicon.svg') }} class="w-[40px]" alt="">
                <h1 class="text-l2 font-bold">Cerita Cireng</h1>
                <i class="ph ph-bell text-3xl"></i>
            </div>
        </div>
        <div class="px-4 xs:px-8 sm:px-20 mt-12">
            {{ $slot }}
        </div>
    </main>
    @livewireScripts
    {{-- <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script> --}}
</body>

</html>
