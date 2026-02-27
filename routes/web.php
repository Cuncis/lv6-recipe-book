<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\RecipeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RecipeController::class, 'index']);

Route::resource('recipes', RecipeController::class);
Route::post('recipes/{recipe}/ratings', [RatingController::class, 'store'])->name('ratings.store');

Route::resource('categories', CategoryController::class)->only(['index', 'store', 'destroy']);
