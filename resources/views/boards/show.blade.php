<x-site-layout :title="'/'.$board->slug.'/ - '.$board->title">

    <h1>/{{ $board->slug }}/ - {{ $board->title }}</h1>
    <p>{{ $board->description }}</p>
    <a href="{{ route('userzone.boards.threads.create', $board) }}" class="text-blue-800 hover:underline">
        Start a new thread
    </a>

    @forelse($threads as $thread)
        <article class="mb-4 rounded border border-gray-300 p-4">
            <a href="{{ route('threads.show', $thread) }}" class="hover:underline">
                [{{$thread->id}}] {{ $thread->subject }}
            </a>

            <p>Thread created by @if($thread->name)
                    {{ $thread->name }}
                @else
                    Anonymous
                @endif
                @if ($thread->isOwnedBy(auth()->user()))
                    (You)
                @endif
                on {{ $thread->created_at }}</p>

        </article>
    @empty
        <p>No threads found.</p>
    @endforelse


</x-site-layout>
