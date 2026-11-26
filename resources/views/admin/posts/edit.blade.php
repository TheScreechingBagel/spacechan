<x-site-layout title="Edit a reply">

    <form method="POST" action="{{ route('admin.posts.update', $post) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <x-form-text-input label="Name (optional)" name="name" :value="$post->name"/>
        <x-form-textarea label="Text" name="text" :value="$post->text" required/>
        <x-breeze.primary-button>Save reply</x-breeze.primary-button>
    </form>

</x-site-layout>
