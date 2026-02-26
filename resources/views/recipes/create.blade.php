@extends('layouts.app')

@section('title', 'New Recipe')

@section('content')

    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('recipes.index') }}" class="text-sm text-orange-500 hover:underline">← Back</a>
        <h1 class="text-2xl font-bold">New Recipe</h1>
    </div>

    <div class="bg-white border border-orange-100 rounded-2xl shadow-sm p-8">
        {{-- enctype is required for file uploads --}}
        <form action="{{ route('recipes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('recipes._form')
            <div class="flex gap-3 mt-8 pt-6 border-t border-gray-100">
                <button type="submit"
                    class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-6 py-2.5 rounded-xl transition">
                    Save Recipe
                </button>
                <a href="{{ route('recipes.index') }}"
                    class="text-gray-500 px-5 py-2.5 rounded-xl border border-gray-300 text-sm hover:bg-gray-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>

@endsection