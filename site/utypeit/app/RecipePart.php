<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

//Order Model
class RecipePart extends Model
{   
    protected $fillable = [
        'id',
        'part_title',
        'created_at'
    ];

	public function recipeIngredients() {
        return $this->hasMany('App\RecipeIngredient','part');
    }
	
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $table = 'recipes_part';
}
