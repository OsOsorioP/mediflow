<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 flex flex-row gap-2">
        <!-- Page Content -->
        <aside class="w-[15%]">
            <livewire:layout.navigation />
        </aside>
        <main class="w-[85%]">
            <header class="">
                <h1></h1>
                <livewire:layout.settings />
            </header>
            <section class="">
                {{ $slot }}
            </section>
        </main>
    </div>
    <x-toast />
    @livewireScripts
    @livewire('wire-elements-modal')
</body>

</html>
