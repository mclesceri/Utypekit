<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Session;
use PDF;
use App\Order;
use App\Recipe;
use App\RecipeContributor;
use App\RecipePart;
use App\RecipeIngredient;
use Auth;


class RecipesController extends Controller {
	public function __construct(){
		// exec('php /srv/utypeit/site/utypeit/artisan view:clear');
		exec('php /var/www/html/prj10360/utypeit/artisan view:clear');
	}
	
	/**
	* function to generate the PDF file.
	*/
    public function index(Request $request, $order_id = NULL)   {
		
		$order_id 		= 	$this->decodeString($order_id);
		// get order info
		$conditions 	= 	[['user_id', '=', Auth::id()], ['id', '=', $order_id]];
		$getAll 		=	0;		 
		$orderInfo 		= 	$this->getOrderInfo($conditions,$getAll);
		
		// get recipe info with filter
		$query 			= 	Recipe::where('user_id', Auth::id());
		$query->where('order_id', '=', $order_id);
		if ($request->has('recipe_search_for')) {
			$recipe_search_for 		= 	$request->input('recipe_search_for');
			$recipe_search_by 		= 	$request->input('recipe_search_by');
			$recipe_search_term 	= 	$request->input('recipe_search_term');
			
			if($recipe_search_by == 'is') {
				$query->where($recipe_search_for, '=', $recipe_search_term);
			}
			if($recipe_search_by == 'like') {
				$query->where($recipe_search_for, 'LIKE', '%' . $recipe_search_term . '%');
			}
			if($recipe_search_by == 'not') {
				$query->where($recipe_search_for, '!=', $recipe_search_term);
			}
        }
		$recipes 		= 	$query->sortable()->paginate(config('constants.limit'));
		return view('recipes.index', compact('recipes', 'order_id', 'orderInfo'));
    }
	
	public function manage_recipe(Request $request, $order_id = NULL, $recipe_id = NULL) {
		$decoded_order_id 	= 	$order_id;
		$order_id 	= 	$this->decodeString($order_id);
		$conditions 	= 	[['user_id', '=', Auth::id()], ['id', '=', $order_id]];
		// dd($conditions);die;
		$getAll 		=	0;		 
		$orderInfo 		= 	$this->getOrderInfo($conditions,$getAll);
		
		// dd($orderInfo);die;
		
		if ($request->isMethod('post')) {
			$requestData 			= 	$request->all();
			// echo "<pre>";
			// print_r($requestData);die;
			
			if(isset($recipe_id) && $recipe_id != '') { //for edit case
			// echo "<pre>";
			// print_r($request);die;
				$recipe_id 				= 	$this->decodeString($recipe_id);
				$recipeObj 				= 	Recipe::find($recipe_id);
				$recipeObj->user_id 	= 	Auth::id();
				$recipeObj->order_id	= 	$order_id;
				$recipeObj->title		= 	$requestData['title'];
				$recipeObj->subtitle	= 	$requestData['subtitle'];
				$recipeObj->status		= 	$requestData['status'];
				$recipeObj->category	= 	$requestData['category'];
				$recipeObj->subcategory	= 	@$requestData['subcategory'];
				$recipeObj->note		= 	$requestData['note_desp'];
				$saved					=	$recipeObj->save();
				if(!$saved) {
					return Redirect::to('/manage-recipe/'.$decoded_order_id)->with('error', 'Recipe does not saved, please check it once again.');	
				} else {
					$lastInsertedRecipeId 		= 	$recipe_id;
					
					DB::table('recipe_contributors')->where('recipe_id', $recipe_id)->delete();
					DB::table('recipes_part')->where('recipe_id', $recipe_id)->delete();
					DB::table('recipes_ingredients')->where('recipe_id', $recipe_id)->delete();
					
					foreach($request['fname'] as $contributor_key => $contributor) {				
						$contributorsData []  	= 	[
							'fname'				=>	$request['fname'][$contributor_key],
							'lname'				=>	$request['lname'][$contributor_key],
							'information'		=>	$request['information'][$contributor_key],
							'contributor_order'	=>	($contributor_key)+1,
							'recipe_id'			=>	$lastInsertedRecipeId,
							'created_at'		=>	date("Y-m-d H:i:s")
						];
					}
					RecipeContributor::insert($contributorsData);
					
					foreach($request['part_title'] as $part_key => $part) {
						$recipePartObj				= 	new RecipePart;
						$recipePartObj->part_title 	= 	$part;
						$recipePartObj->part_method	= 	$request['recipe_method'][$part_key];
						$recipePartObj->recipe_id 	= 	$lastInsertedRecipeId;
						$recipePartObj->created_at 	= 	date("Y-m-d H:i:s");
						$recipePartSaved			=	$recipePartObj->save();
						
						if($recipePartSaved) {
							$lastInsertedRecipePartId 		= 	$recipePartObj->id;
							$recipeIngredientData  		=	 array();
							foreach($request['recipe_ingredients'][$part_key] as $ingredients_key => $ingredients) {
								$recipeIngredientData []  	= 	[
									'part'				=>	$lastInsertedRecipePartId,
									'recipe_id'			=>	$lastInsertedRecipeId,
									'ingredient'		=>	$ingredients,
									'created_at'		=>	date("Y-m-d H:i:s")
								];
							}
							RecipeIngredient::insert($recipeIngredientData);
						}
					}
				}
				return Redirect::to('/recipe-list/'.$decoded_order_id)->with('success', 'All Information has been updated successfully.');
			} else {
				$recipeObj				= 	new Recipe;
				$recipeObj->user_id 	= 	Auth::id();
				$recipeObj->order_id	= 	$order_id;
				$recipeObj->title		= 	$requestData['title'];
				$recipeObj->subtitle	= 	$requestData['subtitle'];
				$recipeObj->status		= 	$requestData['status'];
				$recipeObj->category	= 	$requestData['category'];
				$recipeObj->subcategory	= 	@$requestData['subcategory'];
				$recipeObj->note		= 	$requestData['note_desp'];
				$saved					=	$recipeObj->save();
				if(!$saved){
					return Redirect::to('/manage-recipe/'.$decoded_order_id)->with('error', 'Recipe does not saved, please check it once again.');	
				} else {
					$lastInsertedRecipeId 		= 	$recipeObj->id;
					foreach($request['fname'] as $contributor_key => $contributor) {				
						$contributorsData []  	= 	[
							'fname'				=>	$request['fname'][$contributor_key],
							'lname'				=>	$request['lname'][$contributor_key],
							'information'		=>	$request['information'][$contributor_key],
							'contributor_order'	=>	($contributor_key)+1,
							'recipe_id'			=>	$lastInsertedRecipeId,
							'created_at'		=>	date("Y-m-d H:i:s")
						];
					}
					RecipeContributor::insert($contributorsData);
					
					foreach($request['part_title'] as $part_key => $part) {
						$recipePartObj				= 	new RecipePart;
						$recipePartObj->part_title 	= 	$part;
						$recipePartObj->part_method	= 	$request['recipe_method'][$part_key];
						$recipePartObj->recipe_id 	= 	$lastInsertedRecipeId;
						$recipePartObj->created_at 	= 	date("Y-m-d H:i:s");
						$recipePartSaved			=	$recipePartObj->save();
						
						if($recipePartSaved) {
							$lastInsertedRecipePartId 		= 	$recipePartObj->id;
							$recipeIngredientData  		=	 array();
							foreach($request['recipe_ingredients'][$part_key] as $ingredients_key => $ingredients) {
								$recipeIngredientData []  	= 	[
									'part'				=>	$lastInsertedRecipePartId,
									'recipe_id'			=>	$lastInsertedRecipeId,
									'ingredient'		=>	$ingredients,
									'created_at'		=>	date("Y-m-d H:i:s")
								];
							}
							RecipeIngredient::insert($recipeIngredientData);
						}
					}
				}
				return Redirect::to('/recipe-list/'.$decoded_order_id)->with('success', 'All Information has been updated successfully.');
			}
		} else { //edit case
			if(isset($recipe_id) && !empty($recipe_id)) {
				$recipe_id 				= 	$this->decodeString($recipe_id);
				$fetchedData = Recipe::with('order','contributors', 'recipesParts','recipesParts.recipeIngredients')->find($recipe_id);
				if(!empty($fetchedData)) {
					return view('recipes.manage_recipe', compact('order_id','orderInfo','fetchedData'));
				} else {
					return view('recipes.manage_recipe', compact('order_id','orderInfo'));
				}
			} else {
				return view('recipes.manage_recipe', compact('order_id','orderInfo'));
			}	
		}
		return view('recipes.manage_recipe', compact('order_id','orderInfo'));
	}
	
	public function getSubcategory(Request $request){
		$status 	= 	0;
		$data 		= 	array();
		$method 	= 	$request->method();	
		if ($request->isMethod('post')) {
			$requestData 				= 	$request->all();
			if(isset($requestData['type']) && !empty($requestData['type'])) {
				$conditions 	= 	[['user_id', '=', Auth::id()], ['id', '=', $requestData['order_id']]];
				$getAll 		=	0;		 
				$subcategory	= 	Order::where($conditions)->select('subcategory_title')->first();
				if(!empty($subcategory)) {
					$status = 1;
					$data = unserialize($subcategory->subcategory_title);
				}	
			}
		}
		// echo "<pre>";
		// print_r($data);die;
		echo json_encode(array('status'=>$status, 'data'=>$data));
		die;
	}
	
	
}