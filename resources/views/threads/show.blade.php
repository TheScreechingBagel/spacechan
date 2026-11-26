<x-site-layout title="spacechan">

    <a href="{{ route('boards.show', $thread->board) }}" class="hover:underline">
        back to /{{ $thread->board->slug }}/
    </a>
    <h1>{{ $thread->subject }}</h1>
    <h2>Thread created by @if($thread->name)
            {{ $thread->name }}
        @else
            Anonymous
        @endif
        @if ($thread->isOwnedBy(auth()->user()))
            (You)
        @endif
        @if (auth()->user()?->is_admin)
            [{{ $thread->user->name }}]
        @endif
        on {{ $thread->created_at }}</h2>
    @if ($thread->hasMedia('image'))
        <img src="{{ $thread->getFirstMediaUrl('image', 'web') }}" alt="" class="max-w-md">
    @endif
    <p>{{$thread->text}}</p>
    @if (auth()->user()?->is_admin)
        <a href="{{ route('admin.threads.edit', $thread) }}" class="text-blue-800 hover:underline">Edit thread</a>
    @endif
    @auth
        @if ($thread->canDelete(auth()->user()))
            <form method="POST" action="{{ route('userzone.threads.destroy', $thread) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:underline">Delete thread</button>
            </form>
        @endif
    @endauth

    @foreach($posts as $post)
        <article class="mb-4 rounded border border-gray-300 p-4">
            <h3><p>post'd by @if($post->name)
                        {{ $post->name }}
                    @else
                        Anonymous
                    @endif
                    @if ($post->isOwnedBy(auth()->user()))
                        (You)
                    @endif
                    @if (auth()->user()?->is_admin)
                        [{{ $post->user->name }}]
                    @endif
                    on {{ $post->created_at }}</p></h3>
            <p>{{ $post->text }}</p>
            @if (auth()->user()?->is_admin)
                <a href="{{ route('admin.posts.edit', $post) }}" class="text-blue-800 hover:underline">Edit reply</a>
            @endif
            @auth
                @if ($post->canDelete(auth()->user()))
                    <form method="POST" action="{{ route('userzone.posts.destroy', $post) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Delete reply</button>
                    </form>
                @endif
            @endauth
        </article>

    @endforeach

    <p>{{ $posts->count() }} posts in this thread.</p>

    @auth
        <form method="POST" action="{{ route('userzone.posts.store') }}"
              class="space-y-4 bg-white p-6 shadow-xs sm:rounded-lg">
            @csrf
            <input type="hidden" name="thread_id" value="{{ $thread->id }}">
            <x-form-text-input label="Name (optional, leave empty to post as Anonymous)" name="name"/>
            <x-form-textarea label="Text" name="text" required/>
            <x-breeze.primary-button>Post reply</x-breeze.primary-button>
        </form>
    @endauth
    @guest
        <p>Please <a href="{{ route('login') }}" class="text-blue-500 hover:underline">log in</a> to post a reply.</p>
    @endguest

</x-site-layout>
