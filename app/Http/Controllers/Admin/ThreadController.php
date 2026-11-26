<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ThreadController extends Controller
{
    /**
     * form to moderate the opening post of a thread
     */
    public function edit(Thread $thread): View
    {
        return view('admin.threads.edit', ['thread' => $thread]);
    }

    public function update(Thread $thread): RedirectResponse
    {
        $validated = request()->validate([
            'name' => ['nullable', 'string', 'max:64'],
            'subject' => ['nullable', 'string', 'max:128'],
            'text' => ['required', 'string', 'max:10000'],
        ]);

        $thread->update($validated);

        return redirect()
            ->route('threads.show', $thread)
            ->with('success', 'Thread updated.');
    }
}
