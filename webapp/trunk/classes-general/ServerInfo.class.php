<?php

class ServerInfo{

	public function getHomeUrl($directory){

		$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		
		$pos = strpos($actual_link, $directory);
		$home_url = substr($actual_link, 0, $pos + strlen($directory));
		
		return $home_url;
	
	}
	
	public function isGetRequest(){
		
		return $_SERVER['REQUEST_METHOD'] === 'GET';
		
	}
	
	public function isPostRequest(){
	
		return $_SERVER['REQUEST_METHOD'] === 'POST';
	
	}

}
?>