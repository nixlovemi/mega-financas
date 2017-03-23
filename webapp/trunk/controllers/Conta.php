<?php
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/entities/Conta.entity.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/Conta.service.php';
require_once $_SERVER ['BIRDS_HOME'] . "classes-general/Session.class.php";

class ContaController extends BaseController{
	private $Session;
	private $loggedUserId;
	
	// add to the parent constructor
	public function __construct($action, $urlValues){

		parent::__construct($action, $urlValues);
		$this->Session = new Session();
		$this->loggedUserId = $this->Session->getLoggedUserId();
		
		// create the model object
		require ("models/Conta.php");
		$this->model = new ContaModel();
	
	}
	
	// default method
	protected function index(){

		$ContaServ = new ContaService();
		$objResp = $ContaServ->pegaTodos($this->loggedUserId);
		
		if($objResp->isErro()){
			$erro_msg = $objResp->getRetByKey("msg");
			$arr_contas = array();
		}
		else{
			$erro_msg = "";
			$arr_contas = $objResp->getRetByKey("arr_contas");
		}
		
		$viewModel = new ViewModel();
		$viewModel = $this->model->index();
		$viewModel->set("erro_msg", $erro_msg);
		$viewModel->set("arr_contas", $arr_contas);
		
		$this->view->output($this->model->index(), "");
	
	}

	protected function incluir(){
		$id = "";
		$tituloJanela = "<i class='fa fa-plus'></i>&nbsp;Nova Conta";
		$descricao = "";
		$saldoInicial = "";
		$cor = "#00FF00";
		$id_button = "btn-grava-nova-conta";
		
		$viewModel = new ViewModel();
		$viewModel = $this->model->incluir();
		
		$viewModel->set("id_conta", $id);
		$viewModel->set("titulo_janela", $tituloJanela);
		$viewModel->set("descricao", $descricao);
		$viewModel->set("saldo_inicial", $saldoInicial);
		$viewModel->set("cor", $cor);
		$viewModel->set("id_button", $id_button);
		
		$this->view->output($this->model->incluir(), "");
	}
	
	protected function postIncluir(){
		require_once $_SERVER ['BIRDS_HOME'] . "classes-general/StringUtils.class.php";
		$StringUtils = new StringUtils();
		
		// array resposta JSON
		$arrJSON = array();
		$arrJSON["retorno"] = ""; // OK, ERRO
		$arrJSON["msg"] = "";
		// ===================
		
		// variaveis
		$userId = $this->loggedUserId;
		$vConNome = (isset($_POST["inpt-con-nome"])) ? $_POST["inpt-con-nome"]: "";
		$vConSaldoInicial = (isset($_POST["inpt-con-saldo-inicial"])) ? $StringUtils->strVlrBrToBd($_POST["inpt-con-saldo-inicial"]): "";
		$vConCor = (isset($_POST["inpt-con-cor"])) ? $_POST["inpt-con-cor"]: "";
		// =========
		
		// cria Conta para inserir
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/Usuario.service.php";
		$UsuarioServ = new UsuarioService();
		$objResp = $UsuarioServ->buscaPorId($userId);
		
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao pesquisar usu&aacute;rio ativo. Tente novamente em breve.";

			echo json_encode($arrJSON);
			return;
		}
		$Usuario = $objResp->getRetByKey("ent");
		
		$Conta = new Conta();
		$Conta->setAtivo(true);
		$Conta->setCor($vConCor);
		$Conta->setNome($vConNome);
		$Conta->setSaldoInicial($vConSaldoInicial);
		$Conta->setUsuario($Usuario);
		
		$ContaServ = new ContaService();
		$objResp = $ContaServ->insere($Conta);
		// =======================
		
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = $objResp->getRetByKey("msg");
			
			echo json_encode($arrJSON);
			return;
		}
		else{
			$arrJSON["retorno"] = "OK"; // OK, ERRO
			$arrJSON["msg"] = $objResp->getRetByKey("msg");
			
			echo json_encode($arrJSON);
			return;
		}
	}
	
	protected function editar(){
		// variaveis
		$id = ( isset($_POST["id"]) ) ? $_POST["id"]: "";
		$tituloJanela = "<i class='fa fa-pencil-square-o'></i>&nbsp;Editar Conta";
		$descricao = "";
		$saldoInicial = "";
		$cor = "";
		$id_button = "btn-edita-nova-conta";
		// =========
		
		// busca Conta e pega valores
		$ContaServ = new ContaService();
		$objResp = $ContaServ->buscaPorId($id);
		if($objResp->isOk()){
			$Conta = $objResp->getRetByKey("ent");
			
			$tituloJanela .= ": " . $Conta->getNome();
			$descricao = $Conta->getNome();
			$saldoInicial = $Conta->getSaldoInicial();
			$cor = $Conta->getCor();
		}
		//===========================
		
		// variaveis da view
		$viewModel = new ViewModel();
		$viewModel = $this->model->editar();
		
		$viewModel->set("id_conta", $id);
		$viewModel->set("titulo_janela", $tituloJanela);
		$viewModel->set("descricao", $descricao);
		$viewModel->set("saldo_inicial", $saldoInicial);
		$viewModel->set("cor", $cor);
		$viewModel->set("id_button", $id_button);
		// =================
	
		$this->view->output($this->model->editar(), "");
	}
	
	protected function postEditar(){
		require_once $_SERVER ['BIRDS_HOME'] . "classes-general/StringUtils.class.php";
		$StringUtils = new StringUtils();
		
		// array resposta JSON
		$arrJSON = array();
		$arrJSON["retorno"] = ""; // OK, ERRO
		$arrJSON["msg"] = "";
		// ===================
		
		// variaveis
		$userId = $this->loggedUserId;
		$vConId = (isset($_POST["hddn-id-conta"])) ? $_POST["hddn-id-conta"]: "";
		$vConNome = (isset($_POST["inpt-con-nome"])) ? $_POST["inpt-con-nome"]: "";
		$vConSaldoInicial = (isset($_POST["inpt-con-saldo-inicial"])) ? $StringUtils->strVlrBrToBd($_POST["inpt-con-saldo-inicial"]): "";
		$vConCor = (isset($_POST["inpt-con-cor"])) ? $_POST["inpt-con-cor"]: "";
		// =========
		
		// busca Conta para editar
		$Conta = new Conta();
		$ContaServ = new ContaService();
		
		$objResp = $ContaServ->buscaPorId($vConId);
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao buscar conta. Motivo: " . $objResp->getRetByKey("msg");

			echo json_encode($arrJSON);
			return;
		}
		
		$Conta = $objResp->getRetByKey("ent");
		$Conta->setNome($vConNome);
		$Conta->setSaldoInicial($vConSaldoInicial);
		$Conta->setCor($vConCor);
		
		if( $Conta->getUsuario()->getId() != $this->loggedUserId ){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao alterar conta. Motivo: usu&aacute;rio diferente.";
			
			echo json_encode($arrJSON);
			return;
		}
		
		$objResp = $ContaServ->edita($Conta);
		// =======================
		
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = $objResp->getRetByKey("msg");
			
			echo json_encode($arrJSON);
			return;
		}
		else{
			$arrJSON["retorno"] = "OK"; // OK, ERRO
			$arrJSON["msg"] = $objResp->getRetByKey("msg");
			
			echo json_encode($arrJSON);
			return;
		}
	}
	
	protected function postDeletar(){
		
		// array resposta JSON
		$arrJSON = array();
		$arrJSON["retorno"] = ""; // OK, ERRO
		$arrJSON["msg"] = "";
		// ===================
		
		// variaveis
		$userId = $this->loggedUserId;
		$vConId = (isset($_POST["id"])) ? $_POST["id"]: "";
		// =========
		
		// busca Conta para editar
		$Conta = new Conta();
		$ContaServ = new ContaService();
		
		$objResp = $ContaServ->buscaPorId($vConId);
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao buscar conta. Motivo: " . $objResp->getRetByKey("msg");

			echo json_encode($arrJSON);
			return;
		}
		
		$Conta = $objResp->getRetByKey("ent");
		$Conta->setAtivo("f");
		
		if( $Conta->getUsuario()->getId() != $this->loggedUserId ){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao alterar conta. Motivo: usu&aacute;rio diferente.";
			
			echo json_encode($arrJSON);
			return;
		}
		
		$objResp = $ContaServ->edita($Conta);
		// =======================
		
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = $objResp->getRetByKey("msg");
			
			echo json_encode($arrJSON);
			return;
		}
		else{
			$arrJSON["retorno"] = "OK"; // OK, ERRO
			$arrJSON["msg"] = "Conta deletada com sucesso!";
			
			echo json_encode($arrJSON);
			return;
		}
	}
}

?>