<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Recipe;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recipes = [
            [
                'title' => 'Spaghetti Carbonara',
                'description' => 'A classic Italian pasta dish made with eggs, cheese, pancetta, and pepper.',
                'ingredients' => "200g spaghetti\n100g pancetta\n2 large eggs\n50g Pecorino Romano cheese\n50g Parmesan cheese\nFreshly ground black pepper\nSalt",
                'instructions' => "1. Cook the spaghetti in a large pot of salted boiling water until al dente.\n2. In a pan, cook the pancetta until crispy.\n3. In a bowl, beat the eggs and mix in the cheeses and pepper.\n4. Drain the pasta and return it to the pot. Off the heat, quickly mix in the pancetta and egg mixture until creamy.",
                'prep_time' => 10,
                'cook_time' => 15,
                'servings' => 2,
                'categories' => ['Dinner'],
            ],
            [
                'title' => 'Classic Pancakes',
                'description' => 'Fluffy pancakes perfect for a weekend breakfast.',
                'ingredients' => "1 1/2 cups all-purpose flour\n3 1/2 tsp baking powder\n1 tsp salt\n1 tbsp white sugar\n1 1/4 cups milk\n1 egg\n3 tbsp butter, melted",
                'instructions' => "1. In a large bowl, sift together the flour, baking powder, salt, and sugar.\n2. Make a well in the center and pour in the milk, egg, and melted butter; mix until smooth.\n3. Heat a lightly oiled griddle over medium-high heat. Pour or scoop the batter onto the griddle, using approximately 1/4 cup for each pancake. Brown on both sides and serve hot.",
                'prep_time' => 5,
                'cook_time' => 20,
                'servings' => 4,
                'categories' => ['Breakfast'],
            ],
            [
                'title' => 'Chicken Stir Fry',
                'description' => 'A quick and healthy stir fry with chicken and vegetables.',
                'ingredients' => "2 chicken breasts, sliced\n1 bell pepper, sliced\n1 cup broccoli florets\n2 cloves garlic, minced\n2 tbsp soy sauce\n1 tbsp oyster sauce\n1 tsp sesame oil\nSalt and pepper to taste",
                'instructions' => "1. Heat the sesame oil in a wok or large skillet over medium-high heat.\n2. Add the chicken and cook until browned and cooked through. Remove from the pan and set aside.\n3. In the same pan, add the garlic and cook for 30 seconds until fragrant.\n4. Add the bell pepper and broccoli, and stir fry for 3-4 minutes until tender-crisp.\n5. Return the chicken to the pan, add the soy sauce and oyster sauce, and toss everything together until well coated and heated through.",
                'prep_time' => 15,
                'cook_time' => 10,
                'servings' => 2,
                'categories' => ['Dinner', 'Quick & Easy'],
            ],
            [
                'title' => 'Chocolate Chip Cookies',
                'description' => 'Classic chewy chocolate chip cookies.',
                'ingredients' => "1 cup unsalted butter, softened\n1 cup white sugar\n1 cup packed brown sugar\n2 eggs\n2 tsp vanilla extract\n3 cups all-purpose flour\n1 tsp baking soda\n2 tsp hot water\n1/2 tsp salt\n2 cups semisweet chocolate chips",
                'instructions' => "1. Preheat oven to 350°F (175°C).\n2. Cream together the butter, white sugar, and brown sugar until smooth. Beat in the eggs one at a time, then stir in the vanilla.\n3. Dissolve baking soda in hot water. Add to batter along with salt.\n4. Stir in flour and chocolate chips.\n5. Drop by large spoonfuls onto ungreased pans.\n6. Bake for about 10 minutes in the preheated oven, or until edges are nicely browned.",
                'prep_time' => 10,
                'cook_time' => 10,
                'servings' => 24,
                'categories' => ['Dessert', 'Baking'],
            ],
            [
                'title' => 'Caprese Salad',
                'description' => 'A simple Italian salad made with fresh tomatoes, mozzarella, basil, and a drizzle of balsamic glaze.',
                'ingredients' => "2 large tomatoes, sliced\n200g fresh mozzarella cheese, sliced\nFresh basil leaves\n2 tbsp olive oil\n1 tbsp balsamic glaze\nSalt and pepper to taste",
                'instructions' => "1. Arrange the tomato and mozzarella slices on a plate, alternating them.\n2. Tuck fresh basil leaves between the slices.\n3. Drizzle with olive oil and balsamic glaze.\n4. Season with salt and pepper to taste.",
                'prep_time' => 10,
                'cook_time' => 0,
                'servings' => 2,
                'categories' => ['Lunch', 'Vegetarian'],
            ]
        ];

        foreach ($recipes as $data) {
            $categoryNames = $data['categories'] ?? [];
            unset($data['categories']);

            $data['slug'] = Str::slug($data['title']) . '-' . Str::random(5);

            $recipe = Recipe::create($data);

            if ($categoryNames) {
                $categoryIds = Category::whereIn('name', $categoryNames)->pluck('id');
                $recipe->categories()->sync($categoryIds);
            }
        }
    }
}
