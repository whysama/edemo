<?php

class Geocoder {
	
	private static $url = "http://maps.google.com/maps/api/geocode/json?address=%s&sensor=false";
	
	public function getLocation($search){
		
		$url_address = utf8_encode($search);
		
		// Penser a encoder votre adresse
		$url_address = urlencode($url_address);
		
		// On prépare notre requête
		$query = sprintf(self::$url,$url_address);
		
		// On interroge le serveur
		$json = file_get_contents($query);

		$response = json_decode($json, true);
		
		if($response['status']='OK'){
			return $response['results'][0]['geometry']['location'];
		}else{
			return false;
		}
		
		return $json;
	}
	
	static private function curl_file_get_contents($URL){
		$c = curl_init();
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_URL, $URL);
		$contents = curl_exec($c);
		curl_close($c);
	
		if ($contents) return $contents;
		else return FALSE;
	}
}

?>