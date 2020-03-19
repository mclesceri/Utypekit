<?php


namespace App;


use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;


class Recipe extends Model
{
	use Sortable;


    protected $fillable = [ 'title', 'status' ];
	public $sortable = ['id', 'title', 'status', 'created_at', 'updated_at'];
	
	public function order() {
        return $this->belongsTo('App\Order','order_id');
    }
	
	public function contributors() {
        return $this->hasMany('App\RecipeContributor','recipe_id');
    }
	
	public function recipesParts() {
        return $this->hasMany('App\RecipePart','recipe_id');
    }
}
