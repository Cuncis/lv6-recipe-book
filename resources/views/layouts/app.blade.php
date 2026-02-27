<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Recipe Book')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-orange-50 text-gray-900 min-h-screen flex flex-col">

    <header class="bg-white border-b border-orange-100 shadow-sm">
        <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between gap-4">
            <a href="{{ route('recipes.index') }}" class="text-xl font-bold text-orange-600 flex-shrink-0">
                🍳 Recipe Book
            </a>
            <nav class="flex items-center gap-3 text-sm">
                <a href="{{ route('recipes.index') }}"
                    class="text-gray-500 hover:text-orange-600 transition">Recipes</a>
                <a href="{{ route('categories.index') }}"
                    class="text-gray-500 hover:text-orange-600 transition">Categories</a>
                <a href="{{ route('favorites.index') }}"
                    class="hover:text-red-500 transition {{ request()->routeIs('favorites.*') ? 'text-red-500 font-semibold' : 'text-gray-500' }}">
                    ❤️ Favorites
                </a>
                <a href="{{ route('recipes.create') }}"
                    class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-4 py-2 rounded-lg transition">
                    + Add Recipe
                </a>
            </nav>
        </div>
    </header>

    <main class="flex-1 max-w-5xl mx-auto w-full px-4 py-8">

        @if (session('success'))
            <div class="bg-green-100 border border-green-300 text-green-800 rounded-xl px-5 py-3 mb-6 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="text-center text-xs text-gray-400 py-6 border-t border-orange-100">
        &copy; {{ date('Y') }} Recipe Book — Laravel 12
    </footer>

</body>

</html>