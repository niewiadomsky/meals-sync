<?php

namespace App\Services;

use App\Clients\TheMealDbClient;
use App\Models\Area;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Meal;
use Illuminate\Support\Str;

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
        $areaModels = Area::all();
        $categoryModels = Category::all();
        $ingredientModels = Ingredient::all();
        $letters = range('a', 'z');
        $meals = collect([]);

        foreach ($letters as $letter) {
            $fetchedMeals = $this->client->searchMealsByFirstLetter($letter)['meals'] ?? [];
            $meals = $meals->concat($fetchedMeals);
        }

        $existedMealModels = Meal::query()
            ->whereIn('external_id', $meals->pluck('idMeal'))
            ->pluck('external_id')
            ->toArray();

        $meals->filter(function ($meal) use ($existedMealModels) {
            return !in_array($meal['idMeal'], $existedMealModels);
        })->each(function ($meal) use ($areaModels, $categoryModels, $ingredientModels) {
            $mealModel = Meal::create([
                'name' => $meal['strMeal'],
                'external_id' => $meal['idMeal'],
                'instructions' => $meal['strInstructions'],
                'thumbnail_url' => $meal['strMealThumb'],
                'video_url' => $meal['strYoutube'],
                'area_id' => $areaModels->firstWhere('name', $meal['strArea'])->id,
                'category_id' => $categoryModels->firstWhere('name', $meal['strCategory'])->id,
                'tags' => Str::explode(',', $meal['strTags']),
            ]);

            for ($i = 1; $i <= 20; $i++) {
                $ingredient = $meal["strIngredient{$i}"];
                $measure = $meal["strMeasure{$i}"];
                if ($ingredient) {
                    $ingredient = Str::title($ingredient);
                    $ingredientModel = $ingredientModels->firstWhere('name', $ingredient);
                    
                    if (!$ingredientModel) {
                        $ingredientModel = Ingredient::create([
                            'name' => $ingredient,
                        ]);
                        $ingredientModels->push($ingredientModel);
                    }

                    $mealModel->ingredients()->attach($ingredientModel->id, ['measure' => $measure]);
                }
            }
        });
    }
}