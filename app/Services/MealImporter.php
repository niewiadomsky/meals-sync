<?php

namespace App\Services;

use App\Clients\TheMealDbClient;
use App\Models\Area;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Meal;

class MealImporter
{
    public function __construct(
        protected TheMealDbClient $client,
    ) {
        
    }

    public function import()
    {
        $this->importCategories();
        $this->importAreas();
        $this->importIngredients();
        $this->importMeals();
    }

    public function importCategories()
    {
        $categories = collect($this->client->getCategories()['categories']);
        $existedCategories = Category::query()
            ->whereIn('external_id', $categories->pluck('idCategory'))
            ->pluck('external_id')
            ->toArray();

        $categories->filter(function ($category) use ($existedCategories) {
            return !in_array($category['idCategory'], $existedCategories);
        })->each(function ($category) {
            Category::create([
                'name' => $category['strCategory'],
                'external_id' => $category['idCategory'],
                'thumbnail_url' => $category['strCategoryThumb'],
                'description' => $category['strCategoryDescription'],
            ]);
        });
    }

    public function importAreas()
    {
        $areas = collect($this->client->getAreas()['meals']);
        $existedAreas = Area::query()
            ->whereIn('name', $areas->pluck('strArea'))
            ->pluck('name')
            ->toArray();

        $areas->filter(function ($area) use ($existedAreas) {
            return !in_array($area['strArea'], $existedAreas);
        })->each(function ($area) {
            Area::create([
                'name' => $area['strArea'],
            ]);
        });
    }

    public function importIngredients()
    {
        $ingredients = collect($this->client->getIngredients()['meals']);
        $existedIngredients = Ingredient::query()
            ->whereIn('external_id', $ingredients->pluck('idIngredient'))
            ->pluck('external_id')
            ->toArray();

        $ingredients->filter(function ($ingredient) use ($existedIngredients) {
            return !in_array($ingredient['idIngredient'], $existedIngredients);
        })->each(function ($ingredient) {
            Ingredient::create([
                'name' => $ingredient['strIngredient'],
                'external_id' => $ingredient['idIngredient'],
                'description' => $ingredient['strDescription'],
            ]);
        });
    }

    public function importMeals()
    {
        $areas = Area::all();
        $categories = Category::all();
        $ingredients = Ingredient::all();
        $letters = range('a', 'z');
        $meals = collect([]);

        foreach ($letters as $letter) {
            $fetchedMeals = $this->client->searchMealsByFirstLetter($letter)['meals'] ?? [];
            $meals = $meals->concat($fetchedMeals);
        }

        $existedMeals = Meal::query()
            ->whereIn('external_id', $meals->pluck('idMeal'))
            ->pluck('external_id')
            ->toArray();

        $meals->filter(function ($meal) use ($existedMeals) {
            return !in_array($meal['idMeal'], $existedMeals);
        })->each(function ($meal) use ($areas, $categories, $ingredients) {
            $meal = Meal::create([
                'name' => $meal['strMeal'],
                'external_id' => $meal['idMeal'],
                'instructions' => $meal['strInstructions'],
                'thumbnail_url' => $meal['strMealThumb'],
                'video_url' => $meal['strYoutube'],
                'area_id' => $areas->firstWhere('name', $meal['strArea'])->id,
                'category_id' => $categories->firstWhere('name', $meal['strCategory'])->id,
            ]);

            for ($i = 1; $i <= 20; $i++) {
                $ingredient = $meal["strIngredient{$i}"];
                $measure = $meal["strMeasure{$i}"];
                if ($ingredient) {
                    $meal->ingredients()->attach($ingredients->firstWhere('name', $ingredient)->id, ['measure' => $measure]);
                }
            }
        });
    }
}