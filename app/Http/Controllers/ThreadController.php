<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use Illuminate\View\View;

class ThreadController extends Controller
{
    /**
     * thread with its posts, oldest first.
     */
    public function show(Thread $thread): View
    {
        $posts = $thread->posts()->oldest('created_at')->get();

        return view('threads.show', [
            'thread' => $thread, 'posts' => $posts,
        ]);
    }
}
