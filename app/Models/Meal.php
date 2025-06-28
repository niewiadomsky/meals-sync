<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    protected $fillable = [
        'name',
        'external_id',
        'instructions',
        'thumbnail_url',
        'video_url',
        'area_id',
        'category_id',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class)->withPivot('measure');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
