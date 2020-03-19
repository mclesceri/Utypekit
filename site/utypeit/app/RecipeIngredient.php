<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

//Order Model
class RecipeIngredient extends Model
{
    protected $fillable = [
        'id',
        'part',
        'ingredient',
        'created_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $table = 'recipes_ingredients';
}
