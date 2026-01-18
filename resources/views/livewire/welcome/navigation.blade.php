<nav class="px-4 flex gap-2">
    @auth
        <a
            href="{{ url('/dashboard') }}"
            class=""
        >
            Dashboard
        </a>
    @else
        {{-- <a
            href="{{ route('login') }}"
            class=""
        >
            Log in
        </a> --}}

        @if (Route::has('register'))
            <a
                href="{{ route('register') }}"
                class="bg-white py-2 px-6 rounded-full hover:bg-zinc-100 font-semibold"
            >
                Comenzar
            </a>
        @endif
    @endauth
</nav>
