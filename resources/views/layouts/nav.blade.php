<header class="bg-blue-600 p-4 text-white flex items-center justify-between">
    <div class="text-lg font-semibold">Trello Clone</div>
    <div class="flex items-center space-x-4">
        <button class="bg-blue-700 px-3 py-1 rounded text-sm hover:bg-blue-800">Home</button>
        <button class="bg-blue-700 px-3 py-1 rounded text-sm hover:bg-blue-800">Boards</button>
        <button class="bg-blue-700 px-3 py-1 rounded text-sm hover:bg-blue-800">Settings</button>
        <button class="bg-blue-700 px-3 py-1 rounded text-sm hover:bg-blue-800">Profile</button>

        <!-- Dropdown User -->
        @auth
        <div class="relative">
            <button class="bg-blue-700 px-3 py-1 rounded text-sm hover:bg-blue-800 focus:outline-none" id="user-menu-button">
                {{ Auth::user()->name }} ▾
            </button>
            <div id="dropdown-menu" class="hidden absolute right-0 mt-2 w-48 bg-white text-gray-800 rounded shadow-lg">
                <a href="{{ route('profile') }}" class="block px-4 py-2 hover:bg-gray-100">Profile</a>
                <form method="POST" action="{{ route('logout') }}" class="block">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">Logout</button>
                </form>
            </div>
        </div>
        @else
        <a href="{{ route('login') }}" class="bg-blue-700 px-3 py-1 rounded text-sm hover:bg-blue-800">Login</a>
        @endauth
    </div>
</header>
