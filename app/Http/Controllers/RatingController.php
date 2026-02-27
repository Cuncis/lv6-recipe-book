<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request, Recipe $recipe)
    {
        $request->validate([
            'stars' => 'required|integer|min:1|max:5',
        ]);

        $recipe->ratings()->create(['stars' => $request->stars]);

        return back()->with('success', '⭐ Thanks for rating this recipe!');
    }
}
