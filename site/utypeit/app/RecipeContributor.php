<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

//Order Model
class RecipeContributor extends Model
{   
    protected $fillable = [
        'id',
        'user_id',
        'order_id',
        'title',
        'subtitle',
        'status',
        'category',
        'subcategory',
        'note',
        'created_at',
		'updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $table = 'recipe_contributors';
}
