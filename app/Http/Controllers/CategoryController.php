<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::withCount('recipes')->orderBy('name')->get(); // Get all categories with count of related recipes
        return view('categories.index', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $requested = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create($requested);

        return redirect()->route('categories.index')->with('success', '✅ Category created!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete(); // Delete the category

        return redirect()->route('categories.index')->with('success', '🗑️ Category deleted!');
    }
}
