@props(['menu'])

<header class="border-b border-orange-200 bg-orange-100">
    <div class="mx-auto flex max-w-4xl items-center justify-between px-4 py-3">
        <nav class="flex items-center gap-4">
            <a href="{{ route('boards.index') }}" class="hover:underline">
                <span class="text-lg font-bold text-red-800">spacechan</span>
            </a>
            @foreach ($menu as $label => $url)
                <a href="{{ $url }}" class="text-blue-800 hover:underline">{{ $label }}</a>
            @endforeach
        </nav>

        <div class="flex items-center gap-4 text-sm">
            @auth
                <a href="{{ route('profile.edit') }}"
                   class="hover:underline border border-beige p-1">{{ auth()->user()->name }}</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-blue-800 hover:underline">Log out</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-blue-800 hover:underline">Log in</a>
                <a href="{{ route('register') }}" class="text-blue-800 hover:underline">Register</a>
            @endauth
        </div>
    </div>
</header>
