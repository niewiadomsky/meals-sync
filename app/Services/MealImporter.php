<?php

namespace App\Services;

use App\Clients\TheMealDbClient;
use App\Contracts\ProgressReporter;
use App\Models\Area;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Meal;
use Illuminate\Support\Str;

class MealImporter
{
    protected ?ProgressReporter $progressReporter = null;

    public function __construct(
        protected TheMealDbClient $client,
    ) {}

    public function setProgressReporter(ProgressReporter $progressReporter)
    {
        $this->progressReporter = $progressReporter;
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
        $this->progressReporter?->log('Fetching categories');

        $categories = collect($this->client->getCategories()['categories']);
        $existedCategories = Category::query()
            ->whereIn('external_id', $categories->pluck('idCategory'))
            ->pluck('external_id')
            ->toArray();

        $newCategories = $categories->filter(function ($category) use ($existedCategories) {
            return !in_array($category['idCategory'], $existedCategories);
        });

        if ($newCategories->isEmpty()) {
            $this->progressReporter?->log('No new categories to create');
            return;
        }

        $this->progressReporter?->start("Creating {$newCategories->count()} new categories", $newCategories->count());

        $newCategories->each(function ($category) {
            $this->progressReporter?->advance();
            Category::create([
                'name' => $category['strCategory'],
                'external_id' => $category['idCategory'],
                'thumbnail_url' => $category['strCategoryThumb'],
                'description' => $category['strCategoryDescription'],
            ]);
        });

        $this->progressReporter?->finish();
    }

    public function importAreas()
    {
        $this->progressReporter?->log('Fetching areas');

        $areas = collect($this->client->getAreas()['meals']);
        $existedAreas = Area::query()
            ->whereIn('name', $areas->pluck('strArea'))
            ->pluck('name')
            ->toArray();

        $newAreas = $areas->filter(function ($area) use ($existedAreas) {
            return !in_array($area['strArea'], $existedAreas);
        });

        if ($newAreas->isEmpty()) {
            $this->progressReporter?->log('No new areas to create');
            return;
        }

        $this->progressReporter?->start("Creating {$newAreas->count()} new areas", $newAreas->count());

        $newAreas->each(function ($area) {
            $this->progressReporter?->advance();
            Area::create([
                'name' => $area['strArea'],
            ]);
        });

        $this->progressReporter?->finish();
    }

    public function importIngredients()
    {
        $this->progressReporter?->log('Fetching ingredients');
        $ingredients = collect($this->client->getIngredients()['meals']);
        $existedIngredients = Ingredient::query()
            ->whereIn('external_id', $ingredients->pluck('idIngredient'))
            ->pluck('external_id')
            ->toArray();

        $newIngredients = $ingredients->filter(function ($ingredient) use ($existedIngredients) {
            return !in_array($ingredient['idIngredient'], $existedIngredients);
        });

        if ($newIngredients->isEmpty()) {
            $this->progressReporter?->log('No new ingredients to create');
            return;
        }

        $this->progressReporter?->start("Creating {$newIngredients->count()} new ingredients", $newIngredients->count());

        $newIngredients->each(function ($ingredient) {
            $this->progressReporter?->advance();
            Ingredient::create([
                'name' => $ingredient['strIngredient'],
                'external_id' => $ingredient['idIngredient'],
                'description' => $ingredient['strDescription'],
            ]);
        });

        $this->progressReporter?->finish();
    }

    public function importMeals()
    {
        $areaModels = Area::all();
        $categoryModels = Category::all();
        $ingredientModels = Ingredient::all();
        $letters = range('a', 'z');
        
        // Start progress for fetching meals
        $this->progressReporter?->start('Fetching Meals by Letter', count($letters));
        
        $meals = collect([]);
        foreach ($letters as $letter) {
            $this->progressReporter?->setMessage("Fetching meals starting with '{$letter}'");
            $fetchedMeals = $this->client->searchMealsByFirstLetter($letter)['meals'] ?? [];
            $meals = $meals->concat($fetchedMeals);
            $this->progressReporter?->advance();
        }
        
        $this->progressReporter?->finish();

        $existedMealModels = Meal::query()
            ->whereIn('external_id', $meals->pluck('idMeal'))
            ->pluck('external_id')
            ->toArray();

        $newMeals = $meals->filter(function ($meal) use ($existedMealModels) {
            return !in_array($meal['idMeal'], $existedMealModels);
        });

        if ($newMeals->isEmpty()) {
            $this->progressReporter?->log('No new meals to create');
            return;
        }

        $this->progressReporter?->start("Creating {$newMeals->count()} new meals", $newMeals->count());

        $newMeals->each(function ($meal) use ($areaModels, $categoryModels, $ingredientModels) {
            
            // TODO: can be more optimized by using upsert
            $mealModel = Meal::create([
                'name' => $meal['strMeal'],
                'external_id' => $meal['idMeal'],
                'instructions' => $meal['strInstructions'],
                'thumbnail_url' => $meal['strMealThumb'],
                'video_url' => $meal['strYoutube'],
                'area_id' => $areaModels->firstWhere('name', $meal['strArea'])->id,
                'category_id' => $categoryModels->firstWhere('name', $meal['strCategory'])->id,
                'tags' => Str::of($meal['strTags'])->explode(','),
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
            
            $this->progressReporter?->advance();
        });

        $this->progressReporter?->finish();
    }
}