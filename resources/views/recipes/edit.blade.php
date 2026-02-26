@extends('layouts.app')

@section('title', 'Edit — ' . $recipe->title)

@section('content')

    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('recipes.show', $recipe) }}" class="text-sm text-orange-500 hover:underline">← Back</a>
        <h1 class="text-2xl font-bold">Edit Recipe</h1>
    </div>

    <div class="bg-white border border-orange-100 rounded-2xl shadow-sm p-8">
        <form action="{{ route('recipes.update', $recipe) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('recipes._form')
            <div class="flex gap-3 mt-8 pt-6 border-t border-gray-100">
                <button type="submit"
                    class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-6 py-2.5 rounded-xl transition">
                    Save Changes
                </button>
                <a href="{{ route('recipes.show', $recipe) }}"
                    class="text-gray-500 px-5 py-2.5 rounded-xl border border-gray-300 text-sm hover:bg-gray-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <div class="mt-6 bg-red-50 border border-red-100 rounded-2xl p-5">
        <p class="text-sm font-semibold text-red-600 mb-3">Danger Zone</p>
        <form action="{{ route('recipes.destroy', $recipe) }}" method="POST"
            onsubmit="return confirm('Delete this recipe?')">
            @csrf @method('DELETE')
            <button class="text-sm text-red-500 hover:underline">🗑 Delete this recipe</button>
        </form>
    </div>

@endsection