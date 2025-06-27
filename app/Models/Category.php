<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'external_id',
        'thumbnail_url',
        'description',
    ];
}
