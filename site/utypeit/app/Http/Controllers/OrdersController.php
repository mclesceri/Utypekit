<?php

namespace App\Http\Controllers;

use Config;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Session;

use App\Order;
use App\RecipesImage;
use App\RecipesDraft;
use PDF;
use Auth;
use Carbon;

class OrdersController extends Controller {
	/**
	* Show the order list after login.
	*/
	public function orderslist(Request $request) {
		$orders = DB::table('orders')->where('user_id', Auth::id())->get();
		return view('orders.orderslist',compact(['orders']));
	}
	
	public function order(Request $request, $id = NULL) {
		$organization_types		= 	DB::table('organization_types')->get();
		$printed_liners		= 	DB::table('printed_liners')->where('status',1)->get();
		$paper_stock		= 	DB::table('paper_stock')->where('status',1)->get();
		$recipes_category	= 	DB::table('recipes_category')->where('status',1)->get();
		$pdfName 			= 	$this->generateRandomString(10);
		$method 			= 	$request->method();

		if ($request->isMethod('post')) {
			$requestData 				= 	$request->all();
			
			/* if($requestData['is_preview'] == 1) {
				// $request->session()->flush();
				if($request->hasfile('image_upload_file')) {
					DB::table('recipes_draft')->where('pdfID', $requestData['pdf_name'])->delete();
					foreach($request->file('image_upload_file') as $key => $file) {
						$name				=	$file->getClientOriginalName();
						$imgName 			= 	$this->generateRandomString(10).'.'.$file->getClientOriginalExtension();
						$destinationPath 	= 	public_path('uploads/recipes/draft');
						$imagePath 			= 	$destinationPath. "/".  $imgName;
						$file->move($destinationPath, $imgName); 
						
						$draft_images []  	= 	[
							'imageName'			=>	$imgName,
							'pdfID'				=>	$requestData['pdf_name'],
							'created'=>date("Y-m-d H:i:s")
						];
					}
					RecipesDraft::insert($draft_images); 
				}
				unset($requestData['image_upload_file']);
				$request->session()->put('orderSession', $requestData);
				return Redirect::to('/generate-pdf');	
			} */
			
			
			
			if(isset($requestData['id']) && !empty($requestData['id'])) { //for edit case
				$order = Order::find($requestData['id']);
				//tab1
					$order->user_id 			= 	Auth::id();
					$order->pdf_tag 			= 	$requestData['pdf_tag'];
					$order->status 				= 	$requestData['status'];
					$order->order_title 		= 	$requestData['order_title'];
					$order->book_title1 		= 	$requestData['book_title1'];
					$order->book_title2 		= 	$requestData['book_title2'];
					$order->book_style 			= 	$requestData['book_style'];
					$order->book_count 			= 	$requestData['book_count'];
					$order->organization_type 	= 	$requestData['organization_type'];
					$order->organization_name 	= 	$requestData['organization_name'];
					$order->pdf_printed_liners 	= 	$requestData['pdf_printed_liners'];
					$order->pdf_paper_stock 	= 	$requestData['pdf_paper_stock'];
				
				//tab 2
					$order->nutritional_information 	= 	$requestData['nutritional_information'];
					$order->subcategories_to_recipe 	= 	$requestData['subcategories_to_recipe'];
					$order->contributors 				= 	$requestData['contributors'];
					$order->recipe_index_type 			= 	$requestData['recipe_index_type'];
					$order->order_form_back 			= 	$requestData['order_form_back'];
					$order->wrapper_with_each_page 		= 	$requestData['wrapper_with_each_page'];
					
				//tab 4	
					$order->order_recipes_by 	= 	@$requestData['order_recipes_by'];
					$order->recipes_continued 	= 	@$requestData['recipes_continued'];
					$order->allow_notes 		= 	@$requestData['allow_notes'];
					$order->use_fillers 		= 	@$requestData['use_fillers'];
					$order->filler_type 		= 	@$requestData['filler_type'];
					$order->filler_set 			= 	@$requestData['filler_set'];
					$order->use_icons 			= 	@$requestData['use_icons'];

				$order->divider_tag 		= 	@$requestData['divider_tag'];
				$order->divider_name 		= 	@$requestData['divider_name'];
				$order->divider_paper_stock = 	@$requestData['divider_paper_stock'];
				
				$order->category_title 		= 	serialize($requestData['category_title']);

				$saved							=	$order->save();
				
				if(!$saved){
					return Redirect::to('/order')->with('error', 'Order does not saved, please check it once again.');	
				}
				else {
					return Redirect::to('/order/'.$this->encodeString($requestData['id']))->with('success', 'All Information has been updated successfully.');
				}
			} else { //for first time add
				//tab1
					$order 						= 	new Order;
					$order->user_id 			= 	Auth::id();
					$order->pdf_name 			= 	$requestData['pdf_name'];
					$order->pdf_tag 			= 	$requestData['pdf_tag'];
					$order->status 				= 	$requestData['status'];
					$order->order_title 		= 	$requestData['order_title'];
					$order->order_number		= 	Auth::id().'-'.$this->generateRandomString(4);
					$order->book_title1 		= 	$requestData['book_title1'];
					$order->book_title2 		= 	$requestData['book_title2'];
					$order->book_style 			= 	$requestData['book_style'];
					$order->book_count 			= 	$requestData['book_count'];
					$order->organization_type 	= 	$requestData['organization_type'];
					$order->organization_name 	= 	$requestData['organization_name'];
					$order->pdf_printed_liners 	= 	$requestData['pdf_printed_liners'];
					$order->pdf_paper_stock 	= 	$requestData['pdf_paper_stock'];
					
				//tab 2
					$order->nutritional_information 	= 	$requestData['nutritional_information'];
					$order->subcategories_to_recipe 	= 	$requestData['subcategories_to_recipe'];
					$order->contributors 				= 	$requestData['contributors'];
					$order->recipe_index_type 			= 	$requestData['recipe_index_type'];
					$order->order_form_back 			= 	$requestData['order_form_back'];
					$order->wrapper_with_each_page 		= 	$requestData['wrapper_with_each_page'];	
					
				//tab4
					$order->order_recipes_by 	= 	$requestData['order_recipes_by'];
					$order->recipes_continued 	= 	$requestData['recipes_continued'];
					$order->allow_notes 		= 	$requestData['allow_notes'];
					$order->use_fillers 		= 	$requestData['use_fillers'];
					$order->filler_type 		= 	@$requestData['filler_type'];
					$order->filler_set 			= 	@$requestData['filler_set'];
					$order->use_icons 			= 	$requestData['use_icons'];
					
				//tab2
				$order->divider_tag 		= 	@$requestData['divider_tag'];
				$order->divider_name 		= 	@$requestData['divider_name'];
				$order->divider_paper_stock = 	@$requestData['divider_paper_stock'];
				
				$order->category_title 		= 	serialize($requestData['category_title']);

				$saved							=	$order->save();
				if(!$saved){
					return Redirect::to('/order')->with('error', 'Order does not saved, please check it once again.');	
				}
				else {
					$lastInsertedOrderId 		= 	$order->id;
					if($request->hasfile('image_upload_file')) {
						foreach($request->file('image_upload_file') as $key => $file) {
							$name				=	$file->getClientOriginalName();
							$imgName 			= 	$this->generateRandomString(10).'.'.$file->getClientOriginalExtension();
							$destinationPath 	= 	public_path('uploads/recipes');
							$imagePath 			= 	$destinationPath. "/".  $imgName;
							$file->move($destinationPath, $imgName); 
							
							$recipe_data []  	= 	[
								'recipeTag'			=>	$requestData['recipeTag'][$key],
								'recipeTitle'		=>	$requestData['recipeTitle'][$key],
								'recipeCategory'	=>	$requestData['recipes_category'][$key],
								'imageName'			=>	$imgName,
								'orderID'			=>	$lastInsertedOrderId,
								'isOrderCreated'	=>	1,
								'created'=>date("Y-m-d H:i:s")
							];
						}
						RecipesImage::insert($recipe_data); 
					}
					// $request->session()->flush();	
					$this->createXML($lastInsertedOrderId);
					return Redirect::to('/order/'.$this->encodeString($lastInsertedOrderId))->with('success', 'All Information has been saved successfully.');
				}
			}	
		} else { //edit case
			if(isset($id) && !empty($id)) {
				$id = $this->decodeString($id);
				$fetchedData = Order::with('user')->find($id);
				
				if(!empty($fetchedData)) {
					return view('orders.order',compact(['organization_types', 'printed_liners', 'paper_stock', 'recipes_category', 'pdfName', 'fetchedData']));
					
				} else {
					return view('orders.order',compact(['organization_types', 'printed_liners', 'paper_stock', 'recipes_category', 'pdfName']));
				}
			} else {
				return view('orders.order',compact(['organization_types', 'printed_liners', 'paper_stock', 'recipes_category', 'pdfName']));
			}	
		}
	}
	
	private function createXML($orderId = NULL) {
		if(isset($orderId) && !empty($orderId)) {	
			$order = new Order; //creates object of class
			$fetchedData = $order->find($orderId);
			
			if(!empty($fetchedData)) {
				$dom = new \DOMDocument('1.0','UTF-8');
				$dom->formatOutput = true;
				
				//Create Root Element Start	
					$root = $dom->createElement('cookbook');
					$root->setAttribute('id', $fetchedData->id);
					$root->setAttribute('format', 'traditional');
					$root->setAttribute('continued', 'no');
					$root->setAttribute('fillers', 'yes');
					$root->setAttribute('filler_type', 'text');
					$root->setAttribute('filler_set', 'Custom');
					$root->setAttribute('index_order', 'alphabetical');
					$root->setAttribute('contributor_index', 'yes');
					$root->setAttribute('icons', 'no');
					$root->setAttribute('nutritionals', 'yes');
					$root->setAttribute('uti', 'yes');
					$root->setAttribute('order_form', 'no');
					$root->setAttribute('proof', 'n');
				//Create Root Element End			
				
				$dom->appendChild($root);
				
				//Create Cover Form Start
					$cover = $dom->createElement('cover');
					$root->appendChild($cover);
					
					$cover->appendChild( $dom->createElement('pdf_name', $fetchedData->pdf_name) );
					$cover->appendChild( $dom->createElement('pdf_tag', $fetchedData->pdf_tag) );
					$cover->appendChild( $dom->createElement('pdf_printed_liners', $fetchedData->pdf_printed_liners) );
					$cover->appendChild( $dom->createElement('pdf_paper_stock', $fetchedData->pdf_paper_stock) );
				//Create Cover Form End
				
				//Create Dividers Form Start
					$dividers = $dom->createElement('dividers');
					$root->appendChild($dividers);
					
					$dividers->appendChild( $dom->createElement('divider_tag', $fetchedData->divider_tag) );
					$dividers->appendChild( $dom->createElement('divider_paper_stock', $fetchedData->divider_paper_stock) );
				//Create Dividers Form End
				
				//Create Recipes Form Start
					$recipeImages = new RecipesImage; //creates object of class
					$fetchedRecipeData = $recipeImages->where('orderID', $orderId)->get();
					
					if(!empty($fetchedRecipeData)) {	
						$recipes = $dom->createElement('recipes');
						$root->appendChild($recipes);
						
						foreach($fetchedRecipeData as $image) {
							$recipe = $dom->createElement('recipe');
							$recipe->setAttribute('icon', "");
							$recipe->appendChild( $dom->createElement('title', $image->recipeTag) );
							$recipe->appendChild( $dom->createElement('image_name', $image->imageName) );
							$recipes->appendChild($recipe);	
						}
					}	
				//Create Recipes Form End
				
				//Create Order Form (Personal Page) Start
					$order_form = $dom->createElement('order_form');
					$root->appendChild($order_form);
					
					if($fetchedData->personal_wrapper == 0) {
						$personal_wrapper = 'No';
					} else {
						$personal_wrapper = 'Yes';
					}	
					$order_form->appendChild( $dom->createElement('personal_wrapper', $personal_wrapper) );
					
					if($fetchedData->personal_nutritional_information == 0) {
						$personal_nutritional_information = 'No';
					} else {
						$personal_nutritional_information = 'Yes';
					}
					$order_form->appendChild( $dom->createElement('personal_nutritional_information', $personal_nutritional_information) );
				//Create Order Form (Personal Page) End
				
				//Create Dyanmic XML File Name Start
					$xmlFileName = time().'_'.$fetchedData->pdf_name.'.xml';
				//Create Dyanmic XML File Name End

				//echo '<xmp>'. $dom->saveXML() .'</xmp>';die;
				if($dom->save(Config::get('constants.xml_file_path').$xmlFileName)){
					$zip_file =  $this->createZip($orderId, $xmlFileName);
					if($zip_file) {
						$fetchedData->xml_file = $xmlFileName;
						$fetchedData->zip_file = $zip_file;
						$fetchedData->save();	
					}
				} 	
			}
		} 		
	}
	
	private function createZip($orderId = NULL, $xmlFileName = NULL) {
		if(isset($orderId) && !empty($orderId)) {	
			$order = new Order; //creates object of class
			$fetchedData = $order->find($orderId);
			
			if(!empty($fetchedData)) {
				$zipFileName = time().'_'.$fetchedData->pdf_name.'.zip';

				$zip = new \ZipArchive;
				if ($zip->open(Config::get('constants.zip_file_path') . $zipFileName, \ZipArchive::CREATE) === TRUE) {
					//Add Files into Zip File Start
						//for xml
							$file = Config::get('constants.xml_file_path').$xmlFileName;
							if (file_exists($file) && is_file($file)) {
								chmod($file, 0777);
								$zip->addFile($file, $xmlFileName);
							} 
						
						//for recipe images 
							$recipeImages = new RecipesImage; //creates object of class
							$fetchedRecipeData = $recipeImages->where('orderID', $orderId)->get(); 
							
							if(!empty($fetchedRecipeData))
								{	
									foreach($fetchedRecipeData as $image) {
										$recipeImage = $image->imageName;	
										$fileImage = Config::get('constants.recipe_image_path').$recipeImage;
										if (file_exists($fileImage) && is_file($fileImage)) {
											chmod($fileImage, 0777);
											$zip->addFile($fileImage, $recipeImage);
										}
										else {
										}	
									}
								}		
					//Add Files into Zip File End
					$zip->close();
					return $zipFileName;
				}
			}		
		}
	}
	
	public function getFiller(Request $request){
		$status = 0;
		$data = array();
		
		$method 			= 	$request->method();	
		if ($request->isMethod('post')) {
			$requestData 				= 	$request->all();
			if(isset($requestData['type']) && !empty($requestData['type'])) {
				$fillers		= 	DB::table('fillers')->select('id', 'name')->where('type',$requestData['type'])->get()->toArray();

				if(!empty($fillers)) {
					$status = 1;	
					$data = $fillers; 
				}	
			}
		}
		echo json_encode(array('status'=>$status, 'data'=>$data));
		die;
	}
	
	public function getHelp(Request $request){
		$status = 0;
		$data = array();
		
		$method 			= 	$request->method();	
		if ($request->isMethod('post')) {
			$requestData 				= 	$request->all();
			
			if(isset($requestData['alias']) && !empty($requestData['alias'])) {
				$details		= 	DB::table('help_icons')->where('alias',$requestData['alias'])->first();

				if(!empty($details)) {
					$status = 1;	
					$data = $details; 
				}	
			}
		}
		echo json_encode(array('status'=>$status, 'data'=>$data));
		die;
	}
}