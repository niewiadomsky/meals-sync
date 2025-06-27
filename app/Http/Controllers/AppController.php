<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppController extends Controller
{
    public function index(Request $request)
    {
        

        return Inertia::render('index', [
            'meals' => function () use ($request) {
                $meals = Meal::query()
                    ->when($request->search, function ($query, $search) {
                        $query->where('name', 'like', "%{$search}%");
                    })
                    ->orderBy('created_at', 'desc')
                    ->paginate(20)
                    ->withQueryString();
                    
                return $meals;
            },
        ]);
    }
}