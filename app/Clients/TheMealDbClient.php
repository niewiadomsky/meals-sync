<?php

namespace App\Clients;

use Http;
use Illuminate\Http\Client\PendingRequest;

class TheMealDbClient
{
    protected PendingRequest $client;

    public function __construct(
        private string $baseUrl,
    ) {
        $this->client = Http::withOptions([
            'base_uri' => $this->baseUrl,
        ]);
    }

    public function searchMeals(string $query)
    {
        return $this->client->get("search.php", [
            's' => $query,
        ])->json();
    }

    public function searchMealsByFirstLetter(string $letter)
    {
        return $this->client->get("search.php", [
            'f' => $letter,
        ])->json();
    }

    public function getMealById(string $id)
    {
        return $this->client->get("lookup.php", [
            'i' => $id,
        ])->json();
    }

    public function getCategories()
    {
        return $this->client->get("categories.php")->json();
    }

    public function getAreas()
    {
        return $this->client->get("list.php", [
            'a' => 'list',
        ])->json();
    }

    public function getIngredients()
    {
        return $this->client->get("list.php", [
            'i' => 'list',
        ])->json();
    }

    public function getMeals()
    {
        return $this->client->get("search.php", [
            's' => 'list',
        ])->json();
    }
}