<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\View\View;

class BoardController extends Controller
{
    /**
     * front page, list of all boards
     */
    public function index(): View
    {
        return view('boards.index', [
            'boards' => Board::orderBy('slug')->get(),
        ]);
    }

    /**
     * one board with its threads, most recently bumped first
     */
    public function show(Board $board): View
    {
        return view('boards.show', [
            'board' => $board,
            'threads' => $board->threads()->latest('bumped_at')->get(),
        ]);
    }
}
