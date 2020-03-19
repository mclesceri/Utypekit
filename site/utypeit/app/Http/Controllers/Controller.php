<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use PDF;
use App\Order;
use App\Recipe;
use Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	
	public function __construct(){
		// exec('php /srv/utypeit/site/utypeit/artisan view:clear');
		exec('php /var/www/html/prj10360/utypeit/artisan view:clear');
	}
	
	function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	
	function encodeString($string = NULL) {
		return base64_encode(convert_uuencode($string));	
	}
	
	function decodeString($string = NULL) {
		return convert_uudecode(base64_decode($string));	
	}
	
	function getOrderInfo($conditions, $getAll) {
		if($getAll == 0)
			$orderInfo 	= 	Order::where($conditions)->first();
		else
			$orderInfo 	= 	Order::where($conditions)->get();
		return $orderInfo;
	}
	
	
}
