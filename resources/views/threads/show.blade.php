<x-site-layout title="spacechan">

    <h1>{{ $thread->subject }}</h1>
    <h2>Thread created by {{ $thread->user->name }} on {{ $thread->created_at }}</h2>
    <p>{{$thread->text}}</p>

    @foreach($posts as $post)
        <article class="mb-4 rounded border border-gray-300 p-4">
            <h3><p>post'd by @if($thread->name)
                        {{ $thread->name }}
                    @else
                        Anonymous
                    @endif
                    on {{ $thread->created_at }}</p></h3>
            <p>{{ $post->text }}</p>
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
