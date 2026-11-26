<x-site-layout title="spacechan">


    @forelse($threads as $thread)
        <article class="mb-4 rounded border border-gray-300 p-4">
            <h2>{{ $thread->subject }}</h2>
            <p>Created by {{ $thread->user->name }}
                on {{ $thread->created_at->format('M d, Y') }}</p>
        </article>
    @empty
        <p>No threads found.</p>
    @endforelse


</x-site-layout>
