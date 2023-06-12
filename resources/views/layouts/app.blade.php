<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- CSS -->
    <script src="https://cdn.tailwindcss.com/3.3.2"></script>

    <!-- JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.x/dist/cdn.min.js"></script>

</head>
<body class="font-sans antialiased">
<div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    @include('layouts.navigation')

    <!-- Page Heading -->
    @if (isset($header))
        <header class="bg-white shadow dark:bg-gray-800">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endif

    <!-- Flash Message -->
    @if ($message = Session::get('flash'))
        <section class="border-2 border-indigo-400 bg-indigo-100 py-4 shadow">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="ml-4">
                    <span class="font-semibold">{{ __('System') }}: {{ $message }}</span>
                </div>
            </div>
        </section>
    @endif

    <!-- Page Content -->
    <main>
        {{ $slot }}
    </main>
</div>
</body>
</html>
