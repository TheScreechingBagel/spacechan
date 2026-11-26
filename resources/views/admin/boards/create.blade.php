<x-site-layout title="Add a board">

    <form method="POST" action="{{ route('admin.boards.store') }}" class="space-y-4">
        @csrf
        <x-form-text-input label="Slug (short, like g or hs)" name="slug" required/>
        <x-form-text-input label="Title" name="title" required/>
        <x-form-text-input label="Description (optional)" name="description"/>
        <x-breeze.primary-button>Add board</x-breeze.primary-button>
    </form>

</x-site-layout>
