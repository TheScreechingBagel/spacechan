<x-site-layout title="spacechan">


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
                on {{ $thread->created_at }}</p>

        </article>
    @empty
        <p>No threads found.</p>
    @endforelse


</x-site-layout>
