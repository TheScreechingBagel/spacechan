<x-site-layout title="Manage boards">

    <a href="{{ route('admin.boards.create') }}" class="text-blue-800 hover:underline">Add a board</a>

    @forelse($boards as $board)
        <article class="mb-4 rounded border border-gray-300 p-4">
            <a href="{{ route('boards.show', $board) }}" class="hover:underline">
                /{{ $board->slug }}/ - {{ $board->title }}
            </a>
            <p>{{ $board->threads_count }} threads</p>
            <a href="{{ route('admin.boards.edit', $board) }}" class="text-blue-800 hover:underline">Edit</a>
            <form method="POST" action="{{ route('admin.boards.destroy', $board) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:underline">Delete</button>
            </form>
        </article>
    @empty
        <p>No boards found.</p>
    @endforelse

</x-site-layout>
