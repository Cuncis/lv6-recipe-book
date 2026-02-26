@extends('layouts.app')

@section('title', 'Categories')

@section('content')

    <h1 class="text-2xl font-bold mb-8">Categories</h1>

    <div class="grid sm:grid-cols-2 gap-6">

        {{-- ===== ADD CATEGORY FORM ===== --}}
        <div class="bg-white border border-orange-100 rounded-2xl shadow-sm p-6">
            <h2 class="font-semibold text-gray-800 mb-4">Add New Category</h2>
            <form action="{{ route('categories.store') }}" method="POST" class="flex gap-3">
                @csrf
                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Gluten-Free" class="flex-1 border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400
                                  {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                <button type="submit"
                    class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-4 py-2.5 rounded-xl text-sm transition">
                    Add
                </button>
            </form>
            @error('name')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- ===== CATEGORY LIST ===== --}}
        <div class="bg-white border border-orange-100 rounded-2xl shadow-sm p-6">
            <h2 class="font-semibold text-gray-800 mb-4">All Categories</h2>

            @forelse ($categories as $cat)
                <div class="flex items-center justify-between py-2.5 border-b border-gray-50 last:border-0">
                    <div>
                        <a href="{{ route('recipes.index', ['category' => $cat->slug]) }}"
                            class="font-medium text-gray-800 hover:text-orange-600 transition">
                            {{ $cat->name }}
                        </a>
                        <span class="text-xs text-gray-400 ml-2">{{ $cat->recipes_count }} recipe(s)</span>
                    </div>
                    <form action="{{ route('categories.destroy', $cat) }}" method="POST"
                        onsubmit="return confirm('Delete category \'{{ $cat->name }}\'?')">
                        @csrf @method('DELETE')
                        <button class="text-xs text-red-400 hover:text-red-600 transition">Delete</button>
                    </form>
                </div>
            @empty
                <p class="text-gray-400 text-sm py-4 text-center">No categories yet.</p>
            @endforelse
        </div>

    </div>

@endsection