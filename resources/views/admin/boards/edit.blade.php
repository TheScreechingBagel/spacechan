<x-site-layout title="Edit a board">

    <form method="POST" action="{{ route('admin.boards.update', $board) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <x-form-text-input label="Slug (short, like g or hs)" name="slug" :value="$board->slug" required/>
        <x-form-text-input label="Title" name="title" :value="$board->title" required/>
        <x-form-text-input label="Description (optional)" name="description" :value="$board->description"/>
        <x-breeze.primary-button>Save board</x-breeze.primary-button>
    </form>

</x-site-layout>
