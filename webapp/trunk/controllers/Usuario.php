<?php
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/entities/Usuario.entity.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/Usuario.service.php';
require_once $_SERVER ['BIRDS_HOME'] . "classes-general/Session.class.php";

class UsuarioController extends BaseController{
	private $Session;
	private $loggedUserId;
	
	// add to the parent constructor
	public function __construct($action, $urlValues){

		parent::__construct($action, $urlValues);
		$this->Session = new Session();
		$this->loggedUserId = $this->Session->getLoggedUserId();
		
		// create the model object
		require ("models/Usuario.php");
		$this->model = new UsuarioModel();
	
	}
	
	// default method
	protected function index(){

		// $this->view->output($this->model->index());
	
	}

	protected function validaRegister(){
		// as variaveis vem via POST e codificadas
		$email = ( isset($_POST['e']) ) ? base64_decode($_POST['e']): "";
		$senha = ( isset($_POST['s']) ) ? base64_decode($_POST['s']): "";
		
		// instancia o servico do Usuario
		$objUsuarioServ = new UsuarioService();
		
		// valida as informacoes do POST
		$arr_resp = $objUsuarioServ->validaEmailSenhaRegister($email, $senha);
		
		// retorna o array como JSON
		echo json_encode($arr_resp, JSON_FORCE_OBJECT);
	}
	
	protected function prossegueRegister(){
		// as variaveis vem via POST e codificadas
		$email = ( isset($_POST['e']) ) ? base64_decode($_POST['e']): "";
		$senha = ( isset($_POST['s']) ) ? base64_decode($_POST['s']): "";
		
		// entidade Usuario
		$Usuario = new Usuario();
		$Usuario->setEmail($email);
		$Usuario->setSenha($senha);
		$Usuario->setCadLiberado("f");
		
		// instancia o servico do Usuario
		$objUsuarioServ = new UsuarioService();
		$objStatus = $objUsuarioServ->insere($Usuario);
		
		$arr_resp = array();
		if($objStatus->isErro()){
			$arr_resp["status"] = 0;
			$arr_resp["msg"] = "Ocorreu um erro ao efetuar o cadastro. Tente novamente em breve.";
		}
		else{
			$arr_resp["status"] = 1;
			$arr_resp["msg"] = "Cadastro efetuado! Voc&ecirc; receber&aacute; em breve um email para confirmar o cadastro.";
		}
		
		// retorna o array como JSON
		echo json_encode($arr_resp, JSON_FORCE_OBJECT);
	}

	protected function enviaEmailRegister(){
		// as variaveis vem via POST e codificadas
		$email = ( isset($_POST['e']) ) ? base64_decode($_POST['e']): "";
		
		// instancia o servico do Usuario
		$objUsuarioServ = new UsuarioService();
		
		// valida as informacoes do POST
		$send = $objUsuarioServ->enviaEmailRegister($email);
		
		// retorna o array como JSON
		echo json_encode($send, JSON_FORCE_OBJECT);
	}

	protected function configs(){
		$userId = $this->loggedUserId;
		
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/Usuario.service.php";
		$UsuarioServ = new UsuarioService();
		$objResp = $UsuarioServ->buscaPorId($userId);
		
		$vStrNome = "";
		$vStrSobrenome = "";
		$vStrEmail = "";
		if( $objResp->isOk() ){
			$Usuario = $objResp->getRetByKey("ent");
			$vStrNome = ($Usuario->getNome() != "Nome") ? $Usuario->getNome(): "";
			$vStrSobrenome = ($Usuario->getSobrenome() != "Nome") ? $Usuario->getSobrenome(): "";
			$vStrEmail = $Usuario->getEmail();
		}
		
		$viewModel = new ViewModel();
		$viewModel = $this->model->configs();
		$viewModel->set ("str_nome", $vStrNome);
		$viewModel->set ("str_sobrenome", $vStrSobrenome);
		$viewModel->set ("str_email", $vStrEmail);
		
		$this->view->output($this->model->configs(), "");
	}
	
	protected function postConfigs(){
		// variaveis
		$userId = $this->loggedUserId;
		$vNome = (isset($_POST["inpt-nome"])) ? $_POST["inpt-nome"]: "";
		$vSobrenome = (isset($_POST["inpt-sobrenome"])) ? $_POST["inpt-sobrenome"]: "";
		// =========
		
		$UsuarioServ = new UsuarioService();
		$objResp = $UsuarioServ->buscaPorId($userId);
		if($objResp->isErro()){
			echo "ERRO#@#Erro ao localizar Usu&aacute;rio.";
			return;
		}
		
		$Usuario = $objResp->getRetByKey("ent");
		$Usuario->setNome($vNome);
		$Usuario->setSobrenome($vSobrenome);
		
		$objResp = $UsuarioServ->edita($Usuario);
		if($objResp->isErro()){
			echo "ERRO#@#Erro ao gravar configura&ccedil;&otilde;es.";
			return;
		}
		else{
			echo "OK#@#Configura&ccedil;&otilde;es salvas com sucesso!";
			return;
		}
	}
	
	protected function postConfirmarSenha(){
		// variaveis
		$userId = $this->loggedUserId;
		$vNovaSenha = (isset($_POST["inpt-nova-senha"])) ? $_POST["inpt-nova-senha"]: "";
		$vNovaSenhaRep = (isset($_POST["inpt-repetir-nova-senha"])) ? $_POST["inpt-repetir-nova-senha"]: "";
		// =========
		
		$UsuarioServ = new UsuarioService();
		$objResp = $UsuarioServ->buscaPorId($userId);
		if($objResp->isErro()){
			echo "ERRO#@#Erro ao localizar Usu&aacute;rio.";
			return;
		}
		
		$Usuario = $objResp->getRetByKey("ent");
		$objResp = $UsuarioServ->alteraSenha($Usuario, $vNovaSenha, $vNovaSenhaRep);
		
		if($objResp->isErro()){
			echo "ERRO#@#" . $objResp->getRetByKey("msg");
			return;
		}
		else{
			echo "OK#@#" . $objResp->getRetByKey("msg");
			return;
		}
	}
}

?>