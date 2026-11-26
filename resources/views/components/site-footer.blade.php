@props(['menu'])

<footer class="mx-auto max-w-4xl border-t border-orange-200 px-4 py-6 text-sm">
    <nav class="flex gap-4">
        @foreach ($menu as $label => $url)
            <a href="{{ $url }}" class="text-blue-800 hover:underline">{{ $label }}</a>
        @endforeach
    </nav>
</footer>
