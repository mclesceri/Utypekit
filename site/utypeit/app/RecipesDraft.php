<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecipesDraft extends Model
{   
    protected $fillable = [
        'id',
		'imageName',
		'pdfID',
        'created'
    ];

	
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $table = 'recipes_draft';
}
