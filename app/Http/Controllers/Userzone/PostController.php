<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;

class PostController extends Controller
{
    public function store(): RedirectResponse
    {
        $validated = request()->validate([
            'thread_id' => ['required', 'exists:threads,id'],
            'name' => ['nullable', 'string', 'max:64'],
            'text' => ['required', 'string', 'max:10000'],
            'image' => ['nullable', 'image', 'max:4096', 'mimes:jpg,jpeg,png,gif,webp'],
        ]);

        $post = Post::create([
            'thread_id' => $validated['thread_id'],
            'name' => $validated['name'] ?? null,
            'text' => $validated['text'],
            'user_id' => auth()->id(),
            'created_at' => now(),
        ]);

        if (request()->hasFile('image')) {
            $post->addMediaFromRequest('image')->toMediaCollection('image');
        }

        // Update the thread's bumped_at timestamp
        $post->thread->update(['bumped_at' => now()]);

        return redirect()->route('threads.show', $post->thread);
    }

    public function destroy(Post $post): RedirectResponse
    {
        if (! $post->canDelete(auth()->user())) {
            abort(403);
        }

        $post->delete();

        return redirect()
            ->route('threads.show', $post->thread_id)
            ->with('success', 'Reply deleted.');
    }
}
