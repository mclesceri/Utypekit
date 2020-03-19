<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecipesImage extends Model
{   
    protected $fillable = [
        'id',
		'recipeTag',
		'recipeTitle',
        'recipeCategory',
        'imageName',
        'orderID',
        'isOrderCreated',
        'created'
    ];

	
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $table = 'recipes_images';
}
