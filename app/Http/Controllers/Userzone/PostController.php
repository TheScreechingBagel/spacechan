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
        ]);

        $post = Post::create([
            ...$validated,
            'user_id' => auth()->id(),
            'created_at' => now(),
        ]);

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
