<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Board;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BoardController extends Controller
{
    public function index(): View
    {
        return view('admin.boards.index', [
            'boards' => Board::withCount('threads')->orderBy('slug')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.boards.create');
    }

    public function store(): RedirectResponse
    {
        $validated = request()->validate([
            'slug' => ['required', 'string', 'max:10', 'lowercase', 'alpha_num:ascii', 'unique:boards,slug'],
            'title' => ['required', 'string', 'max:64'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        Board::create($validated);

        return redirect()
            ->route('admin.boards.index')
            ->with('success', 'Board created.');
    }

    public function edit(Board $board): View
    {
        return view('admin.boards.edit', ['board' => $board]);
    }

    public function update(Board $board): RedirectResponse
    {
        $validated = request()->validate([
            'slug' => ['required', 'string', 'max:10', 'lowercase', 'alpha_num:ascii', Rule::unique('boards')->ignore($board)],
            'title' => ['required', 'string', 'max:64'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $board->update($validated);

        return redirect()
            ->route('admin.boards.index')
            ->with('success', 'Board updated.');
    }

    public function destroy(Board $board): RedirectResponse
    {
        if (! $board->canDelete()) {
            return redirect()
                ->route('admin.boards.index')
                ->with('error', 'Only empty boards can be deleted.');
        }

        $board->delete();

        return redirect()
            ->route('admin.boards.index')
            ->with('success', 'Board deleted.');
    }
}
