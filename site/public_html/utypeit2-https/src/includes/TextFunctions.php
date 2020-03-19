<?

class TextFunctions {
	
	public function scrubText($text) {
		$res = $text;
        $res = urldecode($res);
        
        $res = stripslashes($res);
        //$res = html_entity_decode($res);
        
		$pattern = array('/1\/2/','/1\/4/','/3\/4/','/1\/3/','/2\/3/','/1\/8/','/3\/8/','/5\/8/','/7\/8/','/\&deg;/','/\&Acirc;/','/\&#39;/','/ć/');
		$replace = array('&#189;','&#188;','&#190;','&#8531;','&#8532;','&#8539;','&#8540;','&#8541;','&#8542;','&#176;','','&#8217;','&#263;');
		$res = preg_replace($pattern,$replace,$res);
		$res = str_replace('^','&#176;',$res);
		//$res = preg_replace('/\b(\d+)\s+(?=\&#)/','$1',$res);
		$res = preg_replace('/\s+/', ' ', trim($res));
		$res = htmlentities($res,ENT_QUOTES,'utf-8',false);
		return($res);
	}
	
	public function xml2array($xml) {
		$newx = simplexml_load_string($xml);
		return($newx);
	}

}
?>