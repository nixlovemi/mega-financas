<?php

class ErrorModel extends BaseModel{
	// data passed to the bad URL error view
	public function badURL(){

		$this->viewModel->set("pageTitle", "Error - Bad URL");
		return $this->viewModel;
	
	}
	
	// data passed to the bad URL error view
	public function acessoNegado(){

		return $this->viewModel;
	
	}

}

?>