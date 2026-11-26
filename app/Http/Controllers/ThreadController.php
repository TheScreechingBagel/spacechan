<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use Illuminate\View\View;

class ThreadController extends Controller
{
    /**
     * thread with its posts, oldest first. the users are loaded so admins can see who wrote each post
     */
    public function show(Thread $thread): View
    {
        $posts = $thread->posts()->with(['user', 'media'])->oldest('created_at')->get();

        return view('threads.show', [
            'thread' => $thread, 'posts' => $posts,
        ]);
    }
}
