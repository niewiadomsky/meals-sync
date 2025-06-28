<?php

namespace App\Services;

use App\Clients\TheMealDbClient;
use App\Contracts\ProgressReporter;
use App\Models\Area;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Meal;
use DB;
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

        $categoriesToInsert = $categories->filter(function ($category) use ($existedCategories) {
            return !in_array($category['idCategory'], $existedCategories);
        });

        if ($categoriesToInsert->isEmpty()) {
            $this->progressReporter?->log('No new categories to create');
            return;
        }

        $this->progressReporter?->start("Creating {$categoriesToInsert->count()} new categories", $categoriesToInsert->count());

        $preparedCategories = $categoriesToInsert->map(function ($category) {
            $this->progressReporter?->advance();
            return [
                'name' => $category['strCategory'],
                'external_id' => $category['idCategory'],
                'thumbnail_url' => $category['strCategoryThumb'],
                'description' => $category['strCategoryDescription'],
            ];
        });

        $affectedRows = Category::insert($preparedCategories->toArray());
        $this->progressReporter?->finish();
        $this->progressReporter?->log("Inserted {$affectedRows} new categories");

    }

    public function importAreas()
    {
        $this->progressReporter?->log('Fetching areas');

        $areas = collect($this->client->getAreas()['meals']);
        $existedAreas = Area::query()
            ->whereIn('name', $areas->pluck('strArea'))
            ->pluck('name')
            ->toArray();

        $areasToInsert = $areas->filter(function ($area) use ($existedAreas) {
            return !in_array($area['strArea'], $existedAreas);
        });

        if ($areasToInsert->isEmpty()) {
            $this->progressReporter?->log('No new areas to create');
            return;
        }

        $this->progressReporter?->start("Creating {$areasToInsert->count()} new areas", $areasToInsert->count());

        $preparedAreas = $areasToInsert->map(function ($area) {
            $this->progressReporter?->advance();

            return [
                'name' => $area['strArea'],
            ];
        });

        $affectedRows = Area::insert($preparedAreas->toArray());
        $this->progressReporter?->finish();
        $this->progressReporter?->log("Inserted {$affectedRows} new areas");

    }

    public function importIngredients()
    {
        $this->progressReporter?->log('Fetching ingredients');
        $ingredients = collect($this->client->getIngredients()['meals']);
        $existedIngredients = Ingredient::query()
            ->whereIn('external_id', $ingredients->pluck('idIngredient'))
            ->pluck('external_id')
            ->toArray();

        $ingredientsToInsert = $ingredients->filter(function ($ingredient) use ($existedIngredients) {
            return !in_array($ingredient['idIngredient'], $existedIngredients);
        });

        if ($ingredientsToInsert->isEmpty()) {
            $this->progressReporter?->log('No new ingredients to create');
            return;
        }

        $this->progressReporter?->start("Creating {$ingredientsToInsert->count()} new ingredients", $ingredientsToInsert->count());

        $preparedIngredients = $ingredientsToInsert->map(function ($ingredient) {
            $this->progressReporter?->advance();

            return [
                'name' => $ingredient['strIngredient'],
                'external_id' => $ingredient['idIngredient'],
                'description' => $ingredient['strDescription'],
            ];
        });
        
        $affectedRows = Ingredient::insert($preparedIngredients->toArray());
        
        $this->progressReporter?->finish();
        $this->progressReporter?->log("Inserted {$affectedRows} new ingredients");
    }

    public function importMeals()
    {
        $areaModels = Area::all();
        $categoryModels = Category::all();
        $ingredientModels = Ingredient::all();
        $fetchedMeals = $this->fetchAllMeals();

        $existedMealModels = Meal::query()
            ->whereIn('external_id', $fetchedMeals->pluck('idMeal'))
            ->pluck('external_id')
            ->toArray();

        $mealsToInsert = $fetchedMeals->filter(function ($meal) use ($existedMealModels) {
            return !in_array($meal['idMeal'], $existedMealModels);
        });

        if ($mealsToInsert->isEmpty()) {
            $this->progressReporter?->log('No new meals to create');
            return;
        }

        $ingredientsForMeals = [];

        $this->progressReporter?->start("Creating {$mealsToInsert->count()} new meals", $mealsToInsert->count());

        $preparedMeals = $mealsToInsert->map(function ($meal) use ($areaModels, $categoryModels, $ingredientModels, &$ingredientsForMeals) {

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

                    $ingredientsForMeals[$meal['idMeal']][] = [
                        'ingredient_id' => $ingredientModel->id,
                        'measure' => $measure,
                    ];
                }
            }

            $tags = Str::of($meal['strTags'])->explode(',');
            $this->progressReporter?->advance();

            return [
                'name' => $meal['strMeal'],
                'external_id' => $meal['idMeal'],
                'instructions' => $meal['strInstructions'],
                'thumbnail_url' => $meal['strMealThumb'],
                'video_url' => $meal['strYoutube'],
                'area_id' => $areaModels->firstWhere('name', $meal['strArea'])->id,
                'category_id' => $categoryModels->firstWhere('name', $meal['strCategory'])->id,
                'tags' => $tags->isEmpty() || empty($tags->first()) ? null : $tags->toJson(),
            ];
        });
        
        Meal::insert($preparedMeals->toArray());
        $this->progressReporter?->finish();

        $insertedMeals = Meal::whereIn('external_id', $mealsToInsert->pluck('idMeal'))->get();
        
        $this->progressReporter?->start("Attaching ingredients to {$insertedMeals->count()} meals", $insertedMeals->count());
        
        $preparedIngredients = $insertedMeals->map(function ($meal) use ($ingredientsForMeals) {
            $this->progressReporter?->advance();
            return array_map(function ($ingredient) use ($meal) {
                return [
                    'meal_id' => $meal->id,
                    ...$ingredient,
                ];
            }, $ingredientsForMeals[$meal->external_id]);
        })->flatten(1);

        DB::table('ingredient_meal')->insert($preparedIngredients->toArray());
        $this->progressReporter?->finish();
    }

    protected function fetchAllMeals()
    {
        $letters = range('a',  'z');
        $this->progressReporter?->start('Fetching Meals by Letter', count($letters));
        $meals = collect([]);

        foreach ($letters as $letter) {
            $this->progressReporter?->setMessage("Fetching meals starting with '{$letter}'");
            $fetchedMeals = $this->client->searchMealsByFirstLetter($letter)['meals'] ?? [];
            $meals = $meals->concat($fetchedMeals);
            $this->progressReporter?->advance();
        }

        $this->progressReporter?->finish();

        return $meals;
    }
}