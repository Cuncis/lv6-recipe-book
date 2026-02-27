@extends('layouts.app')

@section('title', $recipe->title)

@section('content')

    <div class="mb-6">
        <a href="{{ route('recipes.index') }}" class="text-sm text-orange-500 hover:underline">← All Recipes</a>
    </div>

    <div class="bg-white rounded-2xl border border-orange-100 shadow-sm overflow-hidden mb-8">

        {{-- Hero image --}}
        <img src="{{ $recipe->image_url }}" alt="{{ $recipe->title }}" class="w-full h-72 object-cover">

        <div class="p-8">

            {{-- Categories --}}
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach ($recipe->categories as $cat)
                    <a href="{{ route('recipes.index', ['category' => $cat->slug]) }}"
                        class="bg-orange-100 text-orange-700 text-xs font-medium px-3 py-1 rounded-full hover:bg-orange-200 transition">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>

            <h1 class="text-3xl font-bold mb-3">{{ $recipe->title }}</h1>

            @if ($recipe->description)
                <p class="text-gray-600 text-lg mb-6 leading-relaxed">{{ $recipe->description }}</p>
            @endif

            {{-- Stats bar --}}
            <div class="flex flex-wrap gap-6 text-sm text-gray-500 py-4 border-y border-gray-100 mb-8">
                @if ($recipe->prep_time)
                    <div class="text-center">
                        <p class="font-bold text-gray-900 text-lg">{{ $recipe->prep_time }}</p>
                        <p>Prep (min)</p>
                    </div>
                @endif
                @if ($recipe->cook_time)
                    <div class="text-center">
                        <p class="font-bold text-gray-900 text-lg">{{ $recipe->cook_time }}</p>
                        <p>Cook (min)</p>
                    </div>
                @endif
                @if ($recipe->total_time)
                    <div class="text-center">
                        <p class="font-bold text-orange-600 text-lg">{{ $recipe->total_time }}</p>
                        <p>Total (min)</p>
                    </div>
                @endif
                @if ($recipe->servings)
                    <div class="text-center">
                        <p class="font-bold text-gray-900 text-lg">{{ $recipe->servings }}</p>
                        <p>Servings</p>
                    </div>
                @endif

                {{-- ★ Average rating --}}
                <div class="text-center">
                    <div class="flex justify-center text-yellow-400 text-xl leading-none">
                        @for ($i = 1; $i <= 5; $i++)
                            {{ $i <= round($recipe->ratings_avg_stars ?? 0) ? '★' : '☆' }}
                        @endfor
                    </div>
                    <p class="mt-0.5">
                        @if ($recipe->ratings_count > 0)
                            {{ number_format($recipe->ratings_avg_stars, 1) }} / 5
                            &middot; {{ $recipe->ratings_count }} {{ $recipe->ratings_count === 1 ? 'rating' : 'ratings' }}
                        @else
                            No ratings yet
                        @endif
                    </p>
                </div>
            </div>

            {{-- ★ Rate this recipe --}}
            <div class="mb-8 p-4 bg-orange-50 rounded-xl border border-orange-100">
                <p class="text-sm font-semibold text-gray-700 mb-2">Rate this recipe:</p>
                <form action="{{ route('ratings.store', $recipe) }}" method="POST" class="flex items-center gap-1">
                    @csrf
                    @for ($i = 1; $i <= 5; $i++)
                        <button type="submit" name="stars" value="{{ $i }}" title="{{ $i }} star{{ $i > 1 ? 's' : '' }}"
                            class="text-3xl leading-none text-yellow-300 hover:text-yellow-400 transition focus:outline-none">★</button>
                    @endfor
                    @if (session('success'))
                        <span class="ml-3 text-sm text-green-600">{{ session('success') }}</span>
                    @endif
                </form>
                <script>
                    (function () {
                        const form = document.currentScript.closest('.bg-orange-50');
                        const stars = form.querySelectorAll('button[name="stars"]');
                        stars.forEach((btn, idx) => {
                            btn.addEventListener('mouseover', () => {
                                stars.forEach((s, i) => s.style.color = i <= idx ? '#f59e0b' : '#fde68a');
                            });
                            btn.addEventListener('mouseout', () => {
                                stars.forEach(s => s.style.color = '');
                            });
                        });
                    })();
                </script>
            </div>

            {{-- Two-column layout for ingredients + instructions --}}
            <div class="grid md:grid-cols-5 gap-8">

                {{-- Ingredients --}}
                <div class="md:col-span-2">
                    <h2 class="text-lg font-bold mb-4">🛒 Ingredients</h2>
                    <ul class="space-y-2">
                        @foreach ($recipe->ingredients_list as $ingredient)
                            <li class="flex items-start gap-2 text-sm text-gray-700">
                                <span class="text-orange-400 mt-0.5">•</span>
                                {{ $ingredient }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Instructions --}}
                <div class="md:col-span-3">
                    <h2 class="text-lg font-bold mb-4">📋 Instructions</h2>
                    <div class="space-y-4">
                        @foreach (array_filter(array_map('trim', explode("\n", $recipe->instructions))) as $i => $step)
                            <div class="flex gap-3">
                                <span
                                    class="flex-shrink-0 w-7 h-7 rounded-full bg-orange-100 text-orange-700 text-xs font-bold flex items-center justify-center">
                                    {{ $i + 1 }}
                                </span>
                                <p class="text-sm text-gray-700 leading-relaxed pt-1">{{ $step }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Edit / Delete --}}
            <div class="flex gap-3 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('recipes.edit', $recipe) }}"
                    class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-5 py-2 rounded-lg text-sm transition">
                    ✏️ Edit Recipe
                </a>
                <form action="{{ route('recipes.destroy', $recipe) }}" method="POST"
                    onsubmit="return confirm('Delete this recipe?')">
                    @csrf @method('DELETE')
                    <button
                        class="border border-red-300 text-red-500 hover:bg-red-50 font-semibold px-5 py-2 rounded-lg text-sm transition">
                        🗑 Delete
                    </button>
                </form>
            </div>

        </div>
    </div>

    {{-- ===== RELATED RECIPES ===== --}}
    @if ($related->count())
        <h2 class="text-lg font-bold mb-4">You might also like</h2>
        <div class="grid sm:grid-cols-3 gap-4">
            @foreach ($related as $r)
                <a href="{{ route('recipes.show', $r) }}"
                    class="bg-white rounded-xl border border-orange-100 overflow-hidden hover:shadow-md transition">
                    <img src="{{ $r->image_url }}" alt="{{ $r->title }}" class="w-full h-32 object-cover">
                    <div class="p-3">
                        <p class="font-semibold text-sm truncate">{{ $r->title }}</p>
                        <div class="flex flex-wrap gap-1 mt-1">
                            @foreach ($r->categories->take(2) as $cat)
                                <span class="text-xs text-orange-500">{{ $cat->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

@endsection