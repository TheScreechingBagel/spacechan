<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Start a new thread on /{{ $board->slug }}/
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('userzone.boards.threads.store', $board) }}" enctype="multipart/form-data"
                  class="space-y-4 bg-white p-6 shadow-xs sm:rounded-lg">
                @csrf

                <x-form-text-input label="Name (optional, leave empty to post as Anonymous)" name="name"/>
                <x-form-text-input label="Subject (optional)" name="subject"/>
                <x-form-textarea label="Text" name="text" required/>
                <x-form-file-input label="Image (optional)" name="image"/>

                <x-breeze.primary-button>Post thread</x-breeze.primary-button>
            </form>
        </div>
    </div>
</x-app-layout>
