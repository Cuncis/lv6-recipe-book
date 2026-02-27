<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Toggle a recipe in/out of favorites.
     * Uses the DB pivot when authenticated; falls back to session for guests.
     */
    public function toggle(Request $request, Recipe $recipe)
    {
        if (auth()->check()) {
            auth()->user()->favorites()->toggle($recipe->id);
        } else {
            $favorites = session('favorites', []);
            if (in_array($recipe->id, $favorites)) {
                $favorites = array_values(array_diff($favorites, [$recipe->id]));
            } else {
                $favorites[] = $recipe->id;
            }
            session(['favorites' => $favorites]);
        }

        return back();
    }

    /**
     * Show the user's /my-favorites page.
     */
    public function index()
    {
        if (auth()->check()) {
            $recipes = auth()->user()->favorites()->with('categories')
                ->withAvg('ratings', 'stars')
                ->withCount('ratings')
                ->latest()
                ->get();
        } else {
            $ids = session('favorites', []);
            $recipes = Recipe::with('categories')
                ->withAvg('ratings', 'stars')
                ->withCount('ratings')
                ->whereIn('id', $ids)
                ->latest()
                ->get();
        }

        return view('favorites.index', compact('recipes'));
    }
}
