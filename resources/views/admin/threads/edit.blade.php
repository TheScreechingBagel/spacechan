<x-site-layout title="Edit a thread">

    <form method="POST" action="{{ route('admin.threads.update', $thread) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <x-form-text-input label="Name (optional)" name="name" :value="$thread->name"/>
        <x-form-text-input label="Subject (optional)" name="subject" :value="$thread->subject"/>
        <x-form-textarea label="Text" name="text" :value="$thread->text" required/>
        <x-breeze.primary-button>Save thread</x-breeze.primary-button>
    </form>

</x-site-layout>
