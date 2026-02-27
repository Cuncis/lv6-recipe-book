<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');

        $recipes = Recipe::query()
            // Eager load categories to avoid N+1 queries
            // Without this: 1 query for recipes + 1 per recipe for categories = N+1 problem
            // With this:    1 query for recipes + 1 query for ALL their categories = 2 total
            ->with('categories')
            ->withAvg('ratings', 'stars')
            ->withCount('ratings')
            ->search($search)
            // Filter by category if selected
            ->when($category, function ($q) use ($category) {
                $q->whereHas('categories', fn($q) => $q->where('slug', $category));
            })
            ->latest()
            ->paginate(9)                   // 9 per page — returns LengthAwarePaginator
            ->withQueryString();

        $categories = Category::withCount('recipes')->orderBy('name')->get(); // Get all categories with count of related recipes

        $favoriteIds = auth()->check()
            ? auth()->user()->favorites()->pluck('recipes.id')->toArray()
            : session('favorites', []);

        return view('recipes.index', compact('recipes', 'categories', 'search', 'category', 'favoriteIds'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get(); // Get all categories for the form
        $ingredientSuggestions = $this->ingredientSuggestions();
        return view('recipes.create', compact('categories', 'ingredientSuggestions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ingredients' => 'required|string',
            'instructions' => 'required|string',
            'prep_time' => 'nullable|integer|min:0',
            'cook_time' => 'nullable|integer|min:0',
            'servings' => 'nullable|integer|min:1',
            'categories' => 'array',
            'categories.*' => 'integer|exists:categories,id', // Validate categories as an array of existing category IDs
            'image' => 'nullable|image|max:2048', // Optional image upload, max 2MB

        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('recipes', 'public'); // Store image in public storage
        }
        unset($validated['image']);

        $recipe = Recipe::create($validated); // Create the recipe

        if (isset($validated['categories'])) {
            $recipe->categories()->sync($validated['categories']); // Attach selected categories to the recipe
        }

        return redirect()->route('recipes.index')->with('success', '🍳 Recipe created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe)
    {
        $recipe->load('categories'); // Eager load categories for the recipe
        $recipe->loadAvg('ratings', 'stars');
        $recipe->loadCount('ratings');

        $related = Recipe::with('categories')
            ->withAvg('ratings', 'stars')
            ->withCount('ratings')
            ->whereHas('categories', fn($q) => $q->whereIn('id', $recipe->categories->pluck('id'))) // Find recipes with any of the same categories
            ->where('id', '!=', $recipe->id) // Exclude the current recipe
            ->latest()
            ->take(3) // Limit to 4 related recipes
            ->get();

        $favoriteIds = auth()->check()
            ? auth()->user()->favorites()->pluck('recipes.id')->toArray()
            : session('favorites', []);

        return view('recipes.show', compact('recipe', 'related', 'favoriteIds'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Recipe $recipe)
    {
        $recipe->load('categories'); // Eager load categories for the recipe
        $categories = Category::orderBy('name')->get(); // Get all categories for the form
        $ingredientSuggestions = $this->ingredientSuggestions();

        return view('recipes.edit', compact('recipe', 'categories', 'ingredientSuggestions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Recipe $recipe)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ingredients' => 'required|string',
            'instructions' => 'required|string',
            'prep_time' => 'nullable|integer|min:0',
            'cook_time' => 'nullable|integer|min:0',
            'servings' => 'nullable|integer|min:1',
            'categories' => 'nullable|array',
            'categories.*' => 'integer|exists:categories,id', // Validate categories as an array of existing category IDs
            'image' => 'nullable|image|max:2048', // Optional image upload, max 2MB
        ]);

        if ($request->hasFile('image')) {
            if ($recipe->image_path) {
                Storage::disk('public')->delete($recipe->image_path); // Delete old image
            }
            $validated['image_path'] = $request->file('image')->store('recipes', 'public'); // Store new image
        }
        unset($validated['image']);

        $recipe->update($validated); // Update the recipe

        if (isset($validated['categories'])) {
            $recipe->categories()->sync($validated['categories']); // Sync selected categories to the recipe
        } else {
            $recipe->categories()->detach(); // If no categories selected, detach all
        }

        return redirect()->route('recipes.show', $recipe)->with('success', '✏️ Recipe updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Recipe $recipe)
    {
        if ($recipe->image_url) {
            Storage::disk('public')->delete($recipe->image_url); // Delete the image from storage if it exists
        }

        $recipe->categories()->detach(); // Detach all categories from the recipe
        $recipe->delete(); // Delete the recipe

        return redirect()->route('recipes.index')->with('success', '🗑️ Recipe deleted!');
    }

    /**
     * Parse every ingredient line from all recipes into a unique sorted suggestion list.
     */
    private function ingredientSuggestions(): array
    {
        return Recipe::pluck('ingredients')
            ->flatMap(fn($text) => array_filter(array_map('trim', explode("\n", $text ?? ''))))
            ->map(fn($line) => mb_strtolower($line))
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }
}
