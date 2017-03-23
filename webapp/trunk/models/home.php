<?php
class HomeModel extends BaseModel {
	// data passed to the home index view
	public function index() {
		$this->viewModel->set ( "MT_Page_Title", "In&iacute;cio | Mega Finan&ccedil;as" );
		return $this->viewModel;
	}
	
	public function login() {
		$this->viewModel->set ( "MT_Page_Title", "Login - Mega Finan&ccedil;as" );
		return $this->viewModel;
	}
	
	public function loginTelaRegister() {
		return $this->viewModel;
	}
	
	public function forgetPassword() {
		return $this->viewModel;
	}
	
	public function confirm(){
		$this->viewModel->set ( "MT_Page_Title", "Confirma&ccedil;&atilde;o de Cadastro - Mega Finan&ccedil;as" );
		return $this->viewModel;
	}
}

?>
