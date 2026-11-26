<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SiteLayout extends Component
{
    public array $menu;

    public function __construct(public string $title = 'spacechan')
    {
        $this->menu = ['Board' => route('threads.index')];
    }

    public function render(): View
    {
        return view('components.site-layout');
    }
}
