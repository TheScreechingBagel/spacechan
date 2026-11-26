<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title }}</title>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    @endif
</head>
<body>

<x-site-header :menu="$menu"></x-site-header>

<main class="mx-auto max-w-4xl space-y-4 px-4 py-6">
    @if (session('success'))
        <p class="rounded bg-green-100 p-3 text-green-800">{{ session('success') }}</p>
    @endif

    @if (session('error'))
        <p class="rounded bg-red-100 p-3 text-red-800">{{ session('error') }}</p>
    @endif

    {{ $slot }}
</main>

<x-site-footer :menu="$menu"></x-site-footer>

</body>
</html>
