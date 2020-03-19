<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }
	
	/**
     * Delete All record throughout the website.
     *
     * @return \Illuminate\Http\Response
     */
	public function deleteRecord(Request $request) {
		$status = 0;
		
		$method 			= 	$request->method();	
		if ($request->isMethod('post')) {
			$requestData 				= 	$request->all();
			if(isset($requestData['id']) && !empty($requestData['id']) && isset($requestData['tableName']) && !empty($requestData['tableName'])) {
				$response = DB::table($requestData['tableName'])->where('id', $requestData['id'])->delete();
				if($requestData['tableName'] == 'recipes') { 
					DB::table('recipe_contributors')->where('recipe_id', $requestData['id'])->delete();
					DB::table('recipes_part')->where('recipe_id', $requestData['id'])->delete();
					DB::table('recipes_ingredients')->where('recipe_id', $requestData['id'])->delete();
				}
				if($response) {
					$status = 1;	
					$message = 'Delete Action has been perform successfully.';
				} else {
					$message = 'There are some problem into server end, so please try again later.';
				}
			} else {
				$message = 'Id and Model does not exist, please check it once again.';		
			}
		} else {
			$message = 'Method should be POST.';
		}
		echo json_encode(array('status'=>$status, 'message'=>$message));
		die;	
	}
	
}
