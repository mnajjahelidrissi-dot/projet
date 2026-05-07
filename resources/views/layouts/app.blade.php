<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'SAHAM Bank'))</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap Icons (optionnel) -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @hasSection('header')
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <h1 class="text-2xl font-bold text-gray-800">@yield('header')</h1>
                    </div>
                </header>
            @endif

            <!-- Page Title (alternative) -->
            @hasSection('page-title')
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between items-center">
                            <h1 class="text-2xl font-bold text-gray-800">@yield('page-title')</h1>
                            <div>
                                @yield('page-actions')
                            </div>
                        </div>
                    </div>
                </header>
            @endif

            <!-- Flash Messages -->
            @if(session('succes'))
                <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow" role="alert">
                        <p class="font-medium">Succès !</p>
                        <p>{{ session('succes') }}</p>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow" role="alert">
                        <p class="font-medium">Succès !</p>
                        <p>{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error') || session('erreur'))
                <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow" role="alert">
                        <p class="font-medium">Erreur !</p>
                        <p>{{ session('error') ?? session('erreur') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow" role="alert">
                        <p class="font-medium">Erreur de validation !</p>
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Page Content -->
            <main class="py-4">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    @yield('content')
                </div>
            </main>
        </div>

        @stack('scripts')
    </body>
</html>
