<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * form to moderate a reply
     */
    public function edit(Post $post): View
    {
        return view('admin.posts.edit', ['post' => $post]);
    }

    public function update(Post $post): RedirectResponse
    {
        $validated = request()->validate([
            'name' => ['nullable', 'string', 'max:64'],
            'text' => ['required', 'string', 'max:10000'],
        ]);

        $post->update($validated);

        return redirect()
            ->route('threads.show', $post->thread_id)
            ->with('success', 'Reply updated.');
    }
}
