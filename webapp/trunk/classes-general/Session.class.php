<?php

class Session{

	function __construct(){

	}
	
	public function clearAllCokies(){
		
		
		
	}
	
	public function getLoggedUserId(){
		
		return $_SESSION ['logged_user_id'];
		
	}

	public function initSession($user_id){
		
		session_start();
		
		$_SESSION ['logged_user_id'] = $user_id;
		$_SESSION ['user_agent'] = (isset($_SERVER ['HTTP_USER_AGENT'])) ? $_SERVER ['HTTP_USER_AGENT'] : '';
		$_SESSION ['logged_in'] = true;
	
	}

	public function checkSession($url_redir_login){
		
		session_start();
		
		if( !isset($_SESSION ['logged_in']) || !isset($_SESSION ['logged_user_id']) || !isset($_SESSION ['user_agent']) ){
			$falta_sessions = true;
		}
		else{
			$falta_sessions = false;
		}
		
		// if the user agent doesnt validate, destroy the session and force relogin
		if (($falta_sessions) || (! isset($_SERVER ['HTTP_USER_AGENT']) || $_SESSION ['user_agent'] !== $_SERVER ['HTTP_USER_AGENT']) || ($_SESSION ['logged_in'] == false)) {
			
			// destroy
			session_destroy();
			$_SESSION = array ();
			
			if (! headers_sent()) {
				// set a flash and redirect to the login page
				header('Status: 200');
				header('Location: ' . $_SERVER['BIRDS_HOME_URL'] . urlencode($url_redir_login));
				exit();
			}
			else {
				// throw an error message
				exit();
			}
		}
	
	}

}
?>