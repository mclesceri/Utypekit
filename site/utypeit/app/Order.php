<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

//Order Model
class Order extends Model
{   
    protected $fillable = [
		//tab 1
			'id',
			'user_id',
			'pdf_name',
			'pdf_tag',
			'status',
			'order_title',
			'order_number',
			'book_title1',
			'book_title2',
			'book_style',
			'book_count',
			'organization_types',
			'organization_name',
			'pdf_printed_liners',
			'pdf_paper_stock',
		
		//tab 2
			'nutritional_information',
			'subcategories_to_recipe',
			'contributors',
			'recipe_index_type',
			'order_form_back',
			'wrapper_with_each_page',
			
		//tab 4
			'order_recipes_by',
			'recipes_continued',
			'allow_notes',
			'use_fillers',
			'filler_type',
			'filler_set',
			'use_icons',
			
			'divider_tag',
			'divider_name',
			'divider_paper_stock',
			'personal_wrapper',

		//common
			'xml_file',
			'zip_file',
			'created_at',
			'updated_at'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $table = 'orders';
	
	public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }
	
	public function images()
    {
        return $this->hasMany('App\RecipesImage','orderID');
    }
}
