<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Thread;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ThreadController extends Controller
{
    /**
     * form to make a new thread
     */
    public function create(Board $board): View
    {
        return view('userzone.threads.create', ['board' => $board]);
    }

    public function store(Board $board): RedirectResponse
    {
        $validated = request()->validate([
            'name' => ['nullable', 'string', 'max:64'],
            'subject' => ['nullable', 'string', 'max:128'],
            'text' => ['required', 'string', 'max:10000'],
            'image' => ['nullable', 'image', 'max:4096', 'mimes:jpg,jpeg,png,gif,webp'],
        ]);

        $thread = $board->threads()->create([
            'name' => $validated['name'] ?? null,
            'subject' => $validated['subject'] ?? null,
            'text' => $validated['text'],
            'user_id' => auth()->id(),
            'bumped_at' => now(),
        ]
        );

        if (request()->hasFile('image')) {
            $thread->addMediaFromRequest('image')->toMediaCollection('image');
        }

        return redirect()
            ->route('threads.show', $thread)
            ->with('success', 'Thread created successfully.');
    }

    /**
     * delete a thread and its replies. the replies are deleted one by one
     * so media library also removes their images
     */
    public function destroy(Thread $thread): RedirectResponse
    {
        if (! $thread->canDelete(auth()->user())) {
            abort(403);
        }

        foreach ($thread->posts as $post) {
            $post->delete();
        }

        $thread->delete();

        return redirect()
            ->route('boards.show', $thread->board)
            ->with('success', 'Thread deleted.');
    }
}
