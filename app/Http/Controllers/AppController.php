<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Http\Resources\MealResource;
use App\Models\Meal;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppController extends Controller
{
    public function index(Request $request)
    {

        return Inertia::render('meals/index', [
            'meals' => function () use ($request) {
                $favorites = $request->get('favorites', []);
                $meals = Meal::query()
                    ->when(!empty($favorites), function ($query) use ($favorites) {
                        $query->whereIn('id', $favorites);
                    })
                    ->when($request->search, function ($query, $search) {
                        $query->where('name', 'like', "%{$search}%");
                    })
                    ->orderBy('created_at', 'desc')
                    ->paginate(20)
                    ->withQueryString();
                    
                return MealResource::collection($meals);
            },
        ]);
    }

    public function show(Meal $meal)
    {
        
        return Inertia::render('meals/show', [
            'meal' => function () use ($meal) {
                $meal->load('area', 'category', 'ingredients', 'comments');
                return MealResource::make($meal);
            },
            'comments' => function () use ($meal) {
                $comments = $meal->comments()->with('user')->orderBy('created_at', 'desc')->paginate(20);
                return CommentResource::collection($comments);
            },
        ]);
    }
}