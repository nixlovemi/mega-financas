<?php

class ErrorController extends BaseController{
	// add to the parent constructor
	public function __construct($action, $urlValues){

		parent::__construct($action, $urlValues);
		
		// create the model object
		require ("models/error.php");
		$this->model = new ErrorModel();
	
	}
	
	// bad URL request error
	protected function badURL(){

		$this->view->output($this->model->badURL());
	
	}

	protected function acessoNegado(){

		$this->view->output($this->model->acessoNegado());
	
	}

}

?>
