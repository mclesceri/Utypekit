<?php 
class otw_social_shares {
	private $url;

	function __construct($url) {
		$this->url = rawurlencode($url);
	}

	private function otw_get_facebook() {
		$json_string = $this->otw_get_page_content('http://api.facebook.com/restserver.php?method=links.getStats&format=json&urls='. $this->url);
		$json = json_decode($json_string, true);

		return isset($json[0]['total_count']) ? intval($json[0]['total_count']) : 0;
	}

	private function otw_get_tweets() { 
		$json_string = $this->otw_get_page_content('http://urls.api.twitter.com/1/urls/count.json?url='. $this->url);
		$json = json_decode($json_string, true);

		return isset($json['count']) ? intval($json['count']) : 0;
	}

	private function otw_get_linkedin() { 
		$json_string = $this->otw_get_page_content("http://www.linkedin.com/countserv/count/share?url=$this->url&format=json");
		$json = json_decode($json_string, true);

		return isset($json['count']) ? intval($json['count']) : 0;
	}

	private function otw_get_google_plus()  {
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"'. rawurldecode($this->url) .'","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
		$curl_results = curl_exec ($curl);
		curl_close ($curl);

		$json = json_decode($curl_results, true);

		return isset($json[0]['result']['metadata']['globalCounts']['count']) ? intval( $json[0]['result']['metadata']['globalCounts']['count'] ) : 0;
	}

	private function otw_get_pinterest() {
		$return_data = $this->otw_get_page_content('http://api.pinterest.com/v1/urls/count.json?url='. $this->url);
		$json_string = preg_replace('/^receiveCount\((.*)\)$/', "\\1", $return_data);
		$json = json_decode($json_string, true);

		return isset($json['count']) ? intval($json['count']) : 0;
	}

	private function otw_get_page_content($url){
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		$cont = curl_exec($ch);

		if(curl_error($ch)){
			die(curl_error($ch));
		}
		
		return $cont;
	}

	private function otw_custom_number_format($n, $precision = 1) {
		if ($n < 1000) {
			$n_format = number_format($n);
		} else if ($n < 1000000) {
			$n_format = number_format($n / 1000, $precision) . 'k';
		} else if ($n < 1000000000) {
			$n_format = number_format($n / 1000000, $precision) . 'm';
		} else {
			$n_format = number_format($n / 1000000000, $precision) . 'b';
		}

		return $n_format;
	}

	public function otw_get_shares(){
		$facebook = $this->otw_get_facebook();
		$twitter = $this->otw_get_tweets();
		$google_plus = $this->otw_get_google_plus();
		$linkedin = $this->otw_get_linkedin();
		$pinterest = $this->otw_get_pinterest();

		$all_shares = $facebook + $twitter + $google_plus + $linkedin + $pinterest;

		return json_encode( array( 'info' => 'success', 'all_shares' => $this->otw_custom_number_format($all_shares), 'facebook' => $this->otw_custom_number_format($facebook), 'twitter' => $this->otw_custom_number_format($twitter), 'google_plus' => $this->otw_custom_number_format($google_plus), 'linkedin' => $this->otw_custom_number_format($linkedin), 'pinterest' => $this->otw_custom_number_format($pinterest) ) );
	}
	
}

?>