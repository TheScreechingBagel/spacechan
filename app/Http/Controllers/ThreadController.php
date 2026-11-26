<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use Illuminate\View\View;

class ThreadController extends Controller
{

    /**
     * front page, list of threads with most recently bumped first
     */

    public function index()
    {
        return view('threads.index', [
            'threads' => Thread::with('user')->latest('bumped_at')->get(),
        ]);
    }

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
