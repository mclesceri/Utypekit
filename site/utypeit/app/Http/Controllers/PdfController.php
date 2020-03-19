<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Session;
use PDF;
use App\Order;

class PdfController extends Controller {
	/**
	* function to generate the PDF file.
	*/
    public function generatePDF(Request $request) {
		$data 		= 	$request->session()->all();
		if(isset($data['orderSession'])) {
			$data 	= 	$data['orderSession'];
			unset($data['_previous']);
			unset($data['_token']);
			unset($data['_flash']);
		}		
		$recipes_images 	= 	DB::table('recipes_draft')->where('pdfID', $data['pdf_name'])->get();
        $pdf 				= 	PDF::loadView('pdf.myPDF', compact('data','recipes_images'));
		return $pdf->stream('hdtuto.pdf');
    }
}