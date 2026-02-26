{{--
    Partial: recipes/_form.blade.php
    Used by both create.blade.php and edit.blade.php.
    $recipe is set to 'new Recipe' for create, or the existing model for edit.
--}}

<div class="space-y-6">

    {{-- Title --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Title <span class="text-red-400">*</span>
        </label>
        <input type="text" name="title" value="{{ old('title', $recipe->title ?? '') }}"
               placeholder="e.g. Classic Spaghetti Carbonara" autofocus
               class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400
                      {{ $errors->has('title') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Description --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Short Description</label>
        <textarea name="description" rows="2" placeholder="What makes this recipe special?"
                  class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">{{ old('description', $recipe->description ?? '') }}</textarea>
    </div>

    {{-- Categories checkboxes --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Categories</label>
        <div class="flex flex-wrap gap-2">
            @foreach ($categories as $cat)
                <label class="flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-3 py-1.5 cursor-pointer hover:border-orange-400 transition text-sm has-[:checked]:bg-orange-50 has-[:checked]:border-orange-400">
                    <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                           class="accent-orange-500"
                           @checked(
                               in_array($cat->id,
                                   old('categories', isset($recipe) ? $recipe->categories->pluck('id')->toArray() : [])
                               )
                           )>
                    {{ $cat->name }}
                </label>
            @endforeach
        </div>
        @error('categories') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Image upload --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Photo <span class="text-gray-400 font-normal">(jpg/png/webp, max 2MB)</span>
        </label>
        @if (isset($recipe) && $recipe->image_path)
            <img src="{{ $recipe->image_url }}" class="w-32 h-20 object-cover rounded-lg mb-2 border border-gray-200">
            <p class="text-xs text-gray-400 mb-2">Upload a new image to replace current one.</p>
        @endif
        <input type="file" name="image" accept="image/*"
               class="block text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                      file:text-sm file:font-semibold file:bg-orange-100 file:text-orange-700 hover:file:bg-orange-200">
        @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Time + Servings --}}
    <div class="grid grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Prep Time (min)</label>
            <input type="number" name="prep_time" min="0"
                   value="{{ old('prep_time', $recipe->prep_time ?? '') }}"
                   class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Cook Time (min)</label>
            <input type="number" name="cook_time" min="0"
                   value="{{ old('cook_time', $recipe->cook_time ?? '') }}"
                   class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Servings</label>
            <input type="number" name="servings" min="1"
                   value="{{ old('servings', $recipe->servings ?? '') }}"
                   class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
        </div>
    </div>

    {{-- Ingredients --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Ingredients <span class="text-red-400">*</span>
            <span class="text-gray-400 font-normal ml-1">(one per line)</span>
        </label>
        <textarea name="ingredients" rows="7"
                  placeholder="200g spaghetti&#10;2 large eggs&#10;100g pancetta&#10;50g Pecorino Romano"
                  class="w-full border rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-orange-400
                         {{ $errors->has('ingredients') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">{{ old('ingredients', $recipe->ingredients ?? '') }}</textarea>
        @error('ingredients') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Instructions --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Instructions <span class="text-red-400">*</span>
            <span class="text-gray-400 font-normal ml-1">(one step per line)</span>
        </label>
        <textarea name="instructions" rows="8"
                  placeholder="Boil salted water and cook pasta until al dente.&#10;Fry pancetta until crispy.&#10;Whisk eggs with cheese..."
                  class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400
                         {{ $errors->has('instructions') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">{{ old('instructions', $recipe->instructions ?? '') }}</textarea>
        @error('instructions') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

</div>