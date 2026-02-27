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
            placeholder="e.g. Classic Spaghetti Carbonara" autofocus class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400
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
                <label
                    class="flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-3 py-1.5 cursor-pointer hover:border-orange-400 transition text-sm has-[:checked]:bg-orange-50 has-[:checked]:border-orange-400">
                    <input type="checkbox" name="categories[]" value="{{ $cat->id }}" class="accent-orange-500" @checked(
                        in_array(
                            $cat->id,
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
            <input type="number" name="prep_time" min="0" value="{{ old('prep_time', $recipe->prep_time ?? '') }}"
                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Cook Time (min)</label>
            <input type="number" name="cook_time" min="0" value="{{ old('cook_time', $recipe->cook_time ?? '') }}"
                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Servings</label>
            <input type="number" name="servings" min="1" value="{{ old('servings', $recipe->servings ?? '') }}"
                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
        </div>
    </div>

    {{-- Ingredients --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Ingredients <span class="text-red-400">*</span>
            <span class="text-gray-400 font-normal ml-1">(one per line)</span>
        </label>

        {{-- Autocomplete wrapper --}}
        <div class="relative" id="ingredient-wrap">
            <textarea id="ingredients-input" name="ingredients" rows="7"
                placeholder="200g spaghetti&#10;2 large eggs&#10;100g pancetta&#10;50g Pecorino Romano"
                class="w-full border rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-orange-400
                             {{ $errors->has('ingredients') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">{{ old('ingredients', $recipe->ingredients ?? '') }}</textarea>

            {{-- Suggestion dropdown --}}
            <ul id="ingredient-suggestions"
                class="absolute z-50 left-0 right-0 bg-white border border-orange-200 rounded-xl shadow-lg mt-0.5 max-h-48 overflow-y-auto hidden text-sm">
            </ul>
        </div>

        <p class="text-xs text-gray-400 mt-1">Start typing a line — matching ingredients from your recipe book will
            appear.</p>
        @error('ingredients') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

        <script>
            (function () {
                const SUGGESTIONS = @json($ingredientSuggestions ?? []);
                const textarea = document.getElementById('ingredients-input');
                const list = document.getElementById('ingredient-suggestions');
                let activeIdx = -1;

                function currentLine() {
                    const before = textarea.value.slice(0, textarea.selectionStart);
                    const lines = before.split('\n');
                    return lines[lines.length - 1].trim().toLowerCase();
                }

                function replaceCurrentLine(value) {
                    const start = textarea.value.slice(0, textarea.selectionStart);
                    const after = textarea.value.slice(textarea.selectionStart);
                    const lineStart = start.lastIndexOf('\n') + 1;
                    textarea.value = textarea.value.slice(0, lineStart) + value + after.replace(/^[^\n]*/, '');
                    // Move cursor to end of inserted line
                    const newPos = lineStart + value.length;
                    textarea.setSelectionRange(newPos, newPos);
                }

                function buildList(matches) {
                    list.innerHTML = '';
                    activeIdx = -1;
                    if (!matches.length) { list.classList.add('hidden'); return; }
                    matches.slice(0, 10).forEach((m, i) => {
                        const li = document.createElement('li');
                        li.textContent = m;
                        li.dataset.idx = i;
                        li.className = 'px-4 py-2 cursor-pointer hover:bg-orange-50 hover:text-orange-700';
                        li.addEventListener('mousedown', e => {
                            e.preventDefault(); // keep focus on textarea
                            replaceCurrentLine(m);
                            list.classList.add('hidden');
                            textarea.focus();
                        });
                        list.appendChild(li);
                    });
                    list.classList.remove('hidden');
                }

                function setActive(idx) {
                    const items = list.querySelectorAll('li');
                    items.forEach(li => li.classList.remove('bg-orange-100', 'text-orange-700'));
                    activeIdx = (idx + items.length) % items.length;
                    items[activeIdx].classList.add('bg-orange-100', 'text-orange-700');
                    items[activeIdx].scrollIntoView({ block: 'nearest' });
                }

                textarea.addEventListener('input', () => {
                    const q = currentLine();
                    if (q.length < 2) { list.classList.add('hidden'); return; }
                    const matches = SUGGESTIONS.filter(s => s.includes(q));
                    buildList(matches);
                });

                textarea.addEventListener('keydown', e => {
                    const visible = !list.classList.contains('hidden');
                    if (!visible) return;
                    if (e.key === 'ArrowDown') { e.preventDefault(); setActive(activeIdx + 1); }
                    else if (e.key === 'ArrowUp') { e.preventDefault(); setActive(activeIdx - 1); }
                    else if (e.key === 'Tab' || e.key === 'Enter') {
                        const items = list.querySelectorAll('li');
                        const target = activeIdx >= 0 ? items[activeIdx] : items[0];
                        if (target) {
                            e.preventDefault();
                            replaceCurrentLine(target.textContent);
                            list.classList.add('hidden');
                        }
                    } else if (e.key === 'Escape') {
                        list.classList.add('hidden');
                    }
                });

                // Hide when clicking outside
                document.addEventListener('click', e => {
                    if (!document.getElementById('ingredient-wrap').contains(e.target)) {
                        list.classList.add('hidden');
                    }
                });
            })();
        </script>
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