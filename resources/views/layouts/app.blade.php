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

<body class="min-w-full font-sans antialiased">
    <div class="min-w-full min-h-screen bg-gray-100 flex flex-row">
        <!-- Page Content -->
        <aside class="min-w-[15%] sticky top-0 h-screen">
            <livewire:layout.navigation />
        </aside>
        <main class="min-w-[85%] flex flex-col">
            <livewire:layout.settings />
            <section class="p-6">
                {{ $slot }}
            </section>
        </main>
    </div>
    <x-toast />
    @livewireScripts
    @livewire('wire-elements-modal')
</body>

</html>
