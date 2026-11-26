<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ThreadController extends Controller
{
    /**
     * form to make a new thread
     */
    public function create(): View
    {
        return view('userzone.threads.create');
    }

    public function store(): RedirectResponse
    {
        $validated = request()->validate([
            'name' => ['nullable', 'string', 'max:64'],
            'subject' => ['nullable', 'string', 'max:128'],
            'text' => ['required', 'string', 'max:10000'],
        ]);

        $thread = Thread::create([
                ...$validated,
                'user_id' => auth()->id(),
                'bumped_at' => now(),
            ]
        );

        return redirect()
            ->route('threads.show', $thread)
            ->with('success', 'Thread created successfully.');
    }
}
