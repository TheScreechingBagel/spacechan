<x-site-layout title="spacechan">

    <h1>{{ $thread->subject }}</h1>
    <h2>Thread created by {{ $thread->user->name }} on {{ $thread->created_at }}</h2>
    <p>{{$thread->text}}</p>

    @foreach($posts as $post)
        <article class="mb-4 rounded border border-gray-300 p-4">
            <h3>Post by {{ $post->name }} on {{ $post->created_at}}</h3>
            <p>{{ $post->text }}</p>
        </article>

    @endforeach

    <p>{{ $posts->count() }} posts in this thread.</p>

</x-site-layout>
