@extends('layouts.app')

@section('title', 'Recipes')

@section('content')

    {{-- ===== SEARCH + FILTER BAR ===== --}}
    <form method="GET" action="{{ route('recipes.index') }}" class="flex flex-wrap gap-3 mb-8">
        <div class="flex-1 min-w-48">
            <input type="text" name="search" value="{{ $search }}" placeholder="🔍 Search by name or ingredient..."
                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 bg-white">
        </div>

        <select name="category"
            class="border border-gray-300 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-400">
            <option value="">All Categories</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->slug }}" @selected($category === $cat->slug)>
                    {{ $cat->name }} ({{ $cat->recipes_count }})
                </option>
            @endforeach
        </select>

        <button type="submit"
            class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-5 py-2.5 rounded-xl transition text-sm">
            Filter
        </button>

        @if ($search || $category)
            <a href="{{ route('recipes.index') }}"
                class="text-gray-500 hover:text-gray-700 px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-sm transition">
                Clear
            </a>
        @endif
    </form>

    {{-- ===== RESULTS COUNT ===== --}}
    <div class="flex items-center justify-between mb-5">
        <p class="text-sm text-gray-500">
            {{ $recipes->total() }} recipe(s)
            @if ($search) matching <strong>"{{ $search }}"</strong> @endif
        </p>
    </div>

    {{-- ===== RECIPE GRID ===== --}}
    @forelse ($recipes as $recipe)
        {{-- Card --}}
        <div
            class="bg-white rounded-2xl border border-orange-100 shadow-sm overflow-hidden mb-5 flex flex-col sm:flex-row hover:shadow-md transition">

            {{-- Image --}}
            <a href="{{ route('recipes.show', $recipe) }}" class="flex-shrink-0">
                <img src="{{ $recipe->image_url }}" alt="{{ $recipe->title }}"
                    class="w-full sm:w-48 h-40 sm:h-full object-cover">
            </a>

            {{-- Content --}}
            <div class="p-5 flex flex-col justify-between flex-1">
                <div>
                    {{-- Categories --}}
                    <div class="flex flex-wrap gap-1.5 mb-2">
                        {{-- $recipe->categories already loaded via eager load — no extra query --}}
                        @foreach ($recipe->categories as $cat)
                            <a href="{{ route('recipes.index', ['category' => $cat->slug]) }}"
                                class="bg-orange-100 text-orange-700 text-xs font-medium px-2.5 py-0.5 rounded-full hover:bg-orange-200 transition">
                                {{ $cat->name }}
                            </a>
                        @endforeach
                    </div>

                    <h2 class="text-lg font-bold text-gray-900 mb-1">
                        <a href="{{ route('recipes.show', $recipe) }}" class="hover:text-orange-600 transition">
                            {{ $recipe->title }}
                        </a>
                    </h2>

                    @if ($recipe->description)
                        <p class="text-gray-500 text-sm line-clamp-2">{{ $recipe->description }}</p>
                    @endif
                </div>

                {{-- Meta + actions --}}
                <div class="flex items-center justify-between mt-4">
                    <div class="flex gap-4 text-xs text-gray-400">
                        @if ($recipe->total_time)
                            <span>⏱ {{ $recipe->total_time }} min</span>
                        @endif
                        @if ($recipe->servings)
                            <span>🍽 {{ $recipe->servings }} servings</span>
                        @endif
                        {{-- ★ Average rating badge --}}
                        @if ($recipe->ratings_count > 0)
                            <span class="flex items-center gap-0.5 text-yellow-400 font-medium">
                                @for ($i = 1; $i <= 5; $i++)
                                    {{ $i <= round($recipe->ratings_avg_stars) ? '★' : '☆' }}
                                @endfor
                                <span class="text-gray-400 ml-1 font-normal">
                                    {{ number_format($recipe->ratings_avg_stars, 1) }}
                                    ({{ $recipe->ratings_count }})
                                </span>
                            </span>
                        @else
                            <span class="text-gray-300">☆ No ratings</span>
                        @endif
                    </div>

                    <div class="flex gap-3 text-sm">
                        <a href="{{ route('recipes.edit', $recipe) }}" class="text-orange-500 hover:underline">Edit</a>
                        <form action="{{ route('recipes.destroy', $recipe) }}" method="POST"
                            onsubmit="return confirm('Delete this recipe?')">
                            @csrf @method('DELETE')
                            <button class="text-red-400 hover:text-red-600 transition">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-20 text-gray-400">
            <p class="text-5xl mb-4">🥘</p>
            <p class="text-lg font-medium text-gray-500">No recipes found</p>
            @if ($search || $category)
                <p class="text-sm mt-1">Try a different search or <a href="{{ route('recipes.index') }}"
                        class="text-orange-500 underline">clear filters</a>.</p>
            @else
                <a href="{{ route('recipes.create') }}"
                    class="inline-block mt-4 bg-orange-500 text-white font-bold px-6 py-2 rounded-lg text-sm hover:bg-orange-600 transition">
                    Add your first recipe
                </a>
            @endif
        </div>
    @endforelse

    {{-- ===== PAGINATION ===== --}}
    {{-- $recipes->links() renders the paginator UI using Laravel's built-in Tailwind view --}}
    @if ($recipes->hasPages())
        <div class="mt-8">
            {{ $recipes->links() }}
        </div>
    @endif

@endsection