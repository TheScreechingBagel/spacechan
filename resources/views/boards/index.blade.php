<x-site-layout title="spacechan">

    @forelse($boards as $board)
        <article class="mb-4 rounded border border-gray-300 p-4">
            <a href="{{ route('boards.show', $board) }}" class="hover:underline">
                /{{ $board->slug }}/ - {{ $board->title }}
            </a>
            <p>{{ $board->description }}</p>
        </article>
    @empty
        <p>No boards found.</p>
    @endforelse

</x-site-layout>
