<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Recipe extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'ingredients',
        'instructions',
        'image_path',
        'prep_time',
        'cook_time',
        'servings'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($recipe) {
            $recipe->slug = Str::slug($recipe->title) . '-' . Str::random(5);
        });
    }

    public function getImageUrlAttribute()
    {
        if ($this->image_path && Storage::exists($this->image_path)) {
            return Storage::url($this->image_path);
        }
        return 'https://placehold.co/800x500/fef3c7/92400e?text=' . urlencode($this->title);
    }

    public function getTotalTimeAttribute()
    {
        $prep = $this->prep_time ?? 0;
        $cook = $this->cook_time ?? 0;
        return $prep + $cook;
    }

    public function getIngredientsListAttribute()
    {
        return array_filter(
            array_map('trim', explode("\n", $this->ingredients))
        );
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function scopeSearch($query, $term)
    {
        if (!$term) {
            return $query;
        }
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', '%' . $term . '%')
                ->orWhere('ingredients', 'like', '%' . $term . '%')
                ->orWhere('description', 'like', '%' . $term . '%');
        });
    }
}
