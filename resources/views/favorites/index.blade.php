@extends('layouts.app')

@section('title', 'My Favorites')

@section('content')

    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold">❤️ My Favorites</h1>
        <a href="{{ route('recipes.index') }}" class="text-sm text-orange-500 hover:underline">← All Recipes</a>
    </div>

    @if ($recipes->isEmpty())
        <div class="text-center py-20 text-gray-400">
            <p class="text-5xl mb-4">🤍</p>
            <p class="text-lg font-medium text-gray-500">No favorites yet</p>
            <p class="text-sm mt-1">Hit the ❤️ on any recipe to save it here.</p>
            <a href="{{ route('recipes.index') }}"
                class="inline-block mt-5 bg-orange-500 text-white font-bold px-6 py-2 rounded-lg text-sm hover:bg-orange-600 transition">
                Browse Recipes
            </a>
        </div>
    @else
        <div class="space-y-5">
            @foreach ($recipes as $recipe)
                <div
                    class="bg-white rounded-2xl border border-orange-100 shadow-sm overflow-hidden flex flex-col sm:flex-row hover:shadow-md transition">

                    {{-- Image --}}
                    <a href="{{ route('recipes.show', $recipe) }}" class="flex-shrink-0">
                        <img src="{{ $recipe->image_url }}" alt="{{ $recipe->title }}"
                            class="w-full sm:w-48 h-40 sm:h-full object-cover">
                    </a>

                    {{-- Content --}}
                    <div class="p-5 flex flex-col justify-between flex-1">
                        <div>
                            <div class="flex flex-wrap gap-1.5 mb-2">
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

                        <div class="flex items-center justify-between mt-4">
                            <div class="flex gap-4 text-xs text-gray-400">
                                @if ($recipe->total_time)
                                    <span>⏱ {{ $recipe->total_time }} min</span>
                                @endif
                                @if ($recipe->servings)
                                    <span>🍽 {{ $recipe->servings }} servings</span>
                                @endif
                                @if ($recipe->ratings_count > 0)
                                    <span class="flex items-center gap-0.5 text-yellow-400 font-medium">
                                        @for ($i = 1; $i <= 5; $i++)
                                            {{ $i <= round($recipe->ratings_avg_stars) ? '★' : '☆' }}
                                        @endfor
                                        <span class="text-gray-400 ml-1 font-normal">
                                            {{ number_format($recipe->ratings_avg_stars, 1) }} ({{ $recipe->ratings_count }})
                                        </span>
                                    </span>
                                @endif
                            </div>

                            {{-- Unfavorite button --}}
                            <form action="{{ route('favorites.toggle', $recipe) }}" method="POST">
                                @csrf
                                <button type="submit" title="Remove from favorites"
                                    class="text-red-400 hover:text-red-600 transition text-xl leading-none">❤️</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@endsection