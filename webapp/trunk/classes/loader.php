<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

class Loader{

	private $controllerName;

	private $controllerClass;

	private $action;

	private $urlValues;
	
	// store the URL request values on object creation
	public function __construct(){

		$this->urlValues = $_GET;
		
		if ($this->urlValues ['controller'] == "") {
			$this->controllerName = "home";
			$this->controllerClass = "HomeController";
		}
		else {
			// $this->controllerName = strtolower($this->urlValues['controller']);
			// $this->controllerClass = ucfirst(strtolower($this->urlValues['controller'])) . "Controller";
			$this->controllerName = $this->urlValues ['controller'];
			$this->controllerClass = $this->urlValues ['controller'] . "Controller";
		}
		
		if ($this->urlValues ['action'] == "") {
			// o controller home é a exceção
			if($this->controllerName == "home"){
				$this->action = "login";
			}
			else{
				$this->action = "index";
			}
		}
		else {
			$this->action = $this->urlValues ['action'];
		}
		
		$_REQUEST['controller'] = $this->controllerName;
		$_REQUEST['action'] = $this->action;
		
		// verifica se havera validacao da sessao
		if (!function_exists('getallheaders'))  {
			function getallheaders()
			{
				if (!is_array($_SERVER)) {
					return array();
				}
		
				$headers = array();
				foreach ($_SERVER as $name => $value) {
					if (substr($name, 0, 5) == 'HTTP_') {
						$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
					}
				}
				return $headers;
			}
		}
		$requestHeaders = getallheaders();
		
		$eh_home_login = ($this->controllerName == "home" && $this->action == 'login');
		$eh_home_confirm = ($this->controllerName == "home" && $this->action == 'confirm');
		$eh_home_login_fcbk = ($this->controllerName == "home" && $this->action == 'loginFcbk');
		$eh_home_login_twitter = ($this->controllerName == "home" && $this->action == 'loginTwttr');
		$eh_skip_validation = isset($requestHeaders["Mf-Skip-Session-Validation"]) && $requestHeaders["Mf-Skip-Session-Validation"] == "true";
		
		if(!($eh_home_login || $eh_skip_validation || $eh_home_confirm || $eh_home_login_fcbk || $eh_home_login_twitter)){
			require_once $_SERVER ['BIRDS_HOME'] . "classes-general/Session.class.php";
			$objSession = new Session();
				
			$objSession->checkSession('home');
		}
		// ----------------
	
	}
	
	// factory method which establishes the requested controller as an object
	public function createController(){
		// check our requested controller's class file exists and require it if so
		if (file_exists("controllers/" . $this->controllerName . ".php")) {
			require ("controllers/" . $this->controllerName . ".php");
		}
		else {
			require ("controllers/error.php");
			return new ErrorController("badurl", $this->urlValues);
		}
		
		// does the class exist?
		if (class_exists($this->controllerClass)) {
			$parents = class_parents($this->controllerClass);
			
			// does the class inherit from the BaseController class?
			if (in_array("BaseController", $parents)) {
				// does the requested class contain the requested action as a method?
				if (method_exists($this->controllerClass, $this->action)) {
					return new $this->controllerClass($this->action, $this->urlValues);
				}
				else {
					// bad action/method error
					require ("controllers/error.php");
					return new ErrorController("badurl", $this->urlValues);
				}
			}
			else {
				// bad controller error
				require ("controllers/error.php");
				return new ErrorController("badurl", $this->urlValues);
			}
		}
		else {
			// bad controller error
			require ("controllers/error.php");
			return new ErrorController("badurl", $this->urlValues);
		}
	
	}

}

?>
