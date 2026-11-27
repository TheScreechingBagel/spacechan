<x-site-layout :title="'/'.$board->slug.'/ - '.$board->title">

    <h1>/{{ $board->slug }}/ - {{ $board->title }}</h1>
    <p>{{ $board->description }}</p>
    <a href="{{ route('userzone.boards.threads.create', $board) }}" class="text-blue-800 hover:underline">
        Start a new thread
    </a>

    {{-- auto-fill makes as many 14rem columns as fit, so the grid adapts to the screen width --}}
    <div class="mt-4 grid grid-cols-[repeat(auto-fill,minmax(14rem,1fr))] gap-4">
    @forelse($threads as $thread)
        <article class="flex flex-col gap-2 rounded border border-gray-300 p-4">
            @if ($thread->hasMedia('image'))
                <img src="{{ $thread->getFirstMediaUrl('image', 'thumb') }}" alt="" class="max-h-40 self-center">
            @endif
            <a href="{{ route('threads.show', $thread) }}" class="hover:underline">
                <span class="text-gray-500">[{{$thread->id}}]</span>
                <strong class="text-lg">{{ $thread->subject }}</strong>
            </a>

            <p class="text-sm text-gray-600">Thread created by <strong class="text-gray-900">@if($thread->name)
                    {{ $thread->name }}
                @else
                    Anonymous
                @endif</strong>
                @if ($thread->isOwnedBy(auth()->user()))
                    (You)
                @endif
                on {{ $thread->created_at }}</p>

        </article>
    @empty
        <p>No threads found.</p>
    @endforelse
    </div>


</x-site-layout>
