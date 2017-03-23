<?php
require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/entities/MovimentacaoCat.entity.php";
require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/MovimentacaoCat.service.php";
require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/MovimentacaoTipo.service.php";
require_once $_SERVER ['BIRDS_HOME'] . "classes-general/Session.class.php";

class MovimentacaoCatController extends BaseController{
	private $Session;
	private $loggedUserId;
	
	// add to the parent constructor
	public function __construct($action, $urlValues){

		parent::__construct($action, $urlValues);
		$this->Session = new Session();
		$this->loggedUserId = $this->Session->getLoggedUserId();
		
		// create the model object
		require ("models/MovimentacaoCat.php");
		$this->model = new MovimentacaoCatModel();
	
	}
	
	protected function index(){

		$viewModel = new ViewModel();
		$MovimentacaoCatServ = new MovimentacaoCatService();
		$objResp = $MovimentacaoCatServ->pegaTodosPais($this->loggedUserId);
		
		if($objResp->isErro()){
			$erro_msg = $objResp->getRetByKey("msg");
			$arr_categorias = array();
		}
		else{
			$erro_msg = "";
			$arr_categorias = $objResp->getRetByKey("arr_categorias");
		}
		
		// divide o array em dois arrays
		// um de entrada e outro de saidas
		if( count($arr_categorias) > 0 ){
			list($arrEntradas, $arrSaidas) = $this->model->separaArrayCat($arr_categorias);
		}
		else{
			$arrEntradas = array();
			$arrSaidas = array();
		}
		// ===============================
		
		
		$viewModel = $this->model->index();
		$viewModel->set("erro_msg", $erro_msg);
		$viewModel->set("arrEntradas", $arrEntradas);
		$viewModel->set("arrSaidas", $arrSaidas);
		
		$this->view->output($this->model->index(), "");
	
	}
	
	protected function incluir(){
		$MovimentacaoTipoServ = new MovimentacaoTipoService();
		
		$id = "";
		$tituloJanela = "<i class='fa fa-plus'></i>&nbsp;Nova Categoria";
		$descricao = "";
		$cat_tipo = "";
		$htmlSubcat = "";
		$id_button = "btn-grava-nova-categoria";
		
		// busca os tipos
		$arrRet = $MovimentacaoTipoServ->pegaTodos();
		$arr_cat_tipo = array();
		if($arrRet->isOk()){
			$arr_cat_tipo = $arrRet->getRetByKey("arr_mov_tipo");
		}
		// ==============
		
		$viewModel = new ViewModel();
		$viewModel = $this->model->incluir();
		
		$viewModel->set("id_movimentacao", $id);
		$viewModel->set("titulo_janela", $tituloJanela);
		$viewModel->set("descricao", $descricao);
		$viewModel->set("cat_tipo", $cat_tipo);
		$viewModel->set("arr_cat_tipo", $arr_cat_tipo);
		$viewModel->set("html_subcat", $htmlSubcat);
		$viewModel->set("id_button", $id_button);
		
		$this->view->output($this->model->incluir(), "");
	}
	
	protected function postIncluir(){
		// array resposta JSON
		$arrJSON = array();
		$arrJSON["retorno"] = ""; // OK, ERRO
		$arrJSON["msg"] = "";
		// ===================
		
		// variaveis
		$userId = $this->loggedUserId;
		$vMcDescricao = (isset($_POST["inpt-mc-descricao"])) ? $_POST["inpt-mc-descricao"]: "";
		$vMcMtId = (isset($_POST["slct-mc-mt-id"])) ? $_POST["slct-mc-mt-id"]: "";
		// =========
		
		// cria Movimentacao para inserir
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
		
		// busca a entidade MovimentacaoTipo
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/MovimentacaoTipo.service.php";
		$MovimentacaoTipoServ = new MovimentacaoTipoService();
		$objResp = $MovimentacaoTipoServ->buscaPorId($vMcMtId);
		
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao pesquisar tipo da movimenta&ccedil;&atilde;o. Tente novamente em breve.";
		
			echo json_encode($arrJSON);
			return;
		}
		$MovimentacaoTipo = $objResp->getRetByKey("ent");
		// =================================
		
		$MovimentacaoCat = new MovimentacaoCat();
		$MovimentacaoCat->setAtivo(true);
		$MovimentacaoCat->setCategoriaPai(null);
		$MovimentacaoCat->setDescricao($vMcDescricao);
		$MovimentacaoCat->setMovimentacaoTipo($MovimentacaoTipo);
		$MovimentacaoCat->setUsuario($Usuario);
		
		$MovimentacaoCatServ = new MovimentacaoCatService();
		$objResp = $MovimentacaoCatServ->insere($MovimentacaoCat);
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
		$viewModel = new ViewModel();
		$MovimentacaoTipoServ = new MovimentacaoTipoService();
		
		// variaveis
		$id = ( isset($_POST["id"]) ) ? $_POST["id"]: "";
		$tituloJanela = "<i class='fa fa-pencil-square-o'></i>&nbsp;Editar Categoria";
		$descricao = "";
		$cat_tipo = "";
		$htmlSubcat = "";
		$id_button = "btn-edita-nova-categoria";
		// =========
	
		// busca Movimentacao e pega valores
		$MovimentacaoCatServ = new MovimentacaoCatService();
		$objResp = $MovimentacaoCatServ->buscaPorId($id);
		if($objResp->isOk()){
			$MovimentacaoCat = $objResp->getRetByKey("ent");
				
			$tituloJanela .= ": " . $MovimentacaoCat->getDescricao();
			$descricao = $MovimentacaoCat->getDescricao();
			$cat_tipo = $MovimentacaoCat->getMovimentacaoTipo()->getId();
		}
		//===========================
		
		// busca os tipos
		$arrRet = $MovimentacaoTipoServ->pegaTodos();
		$arr_cat_tipo = array();
		if($arrRet->isOk()){
			$arr_cat_tipo = $arrRet->getRetByKey("arr_mov_tipo");
		}
		// ==============
		
		// gera o html da subcat
		$objResp = $MovimentacaoCatServ->pegaTodosFilhos($MovimentacaoCat);
		if($objResp->isOk()){
			$arr_subcat = $objResp->getRetByKey("arr_subcategorias");
			$htmlSubcat = $this->model->getHtmlSubcat($arr_subcat);
		}
		// =====================
	
		// variaveis da view
		$viewModel = $this->model->editar();
	
		$viewModel->set("id_movimentacao", $id);
		$viewModel->set("titulo_janela", $tituloJanela);
		$viewModel->set("descricao", $descricao);
		$viewModel->set("cat_tipo", $cat_tipo);
		$viewModel->set("arr_cat_tipo", $arr_cat_tipo);
		$viewModel->set("html_subcat", $htmlSubcat);
		$viewModel->set("id_button", $id_button);
		// =================
	
		$this->view->output($this->model->editar(), "");
	}
	
	protected function postEditar(){
		// array resposta JSON
		$arrJSON = array();
		$arrJSON["retorno"] = ""; // OK, ERRO
		$arrJSON["msg"] = "";
		// ===================
		
		// variaveis
		$userId = $this->loggedUserId;
		$vMcId = (isset($_POST["hddn-id-movimentacao"])) ? $_POST["hddn-id-movimentacao"]: "";
		$vMcDescricao = (isset($_POST["inpt-mc-descricao"])) ? $_POST["inpt-mc-descricao"]: "";
		$vMcMtId = (isset($_POST["slct-mc-mt-id"])) ? $_POST["slct-mc-mt-id"]: "";
		// =========
		
		// busca a entidade MovimentacaoTipo
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/MovimentacaoTipo.service.php";
		$MovimentacaoTipoServ = new MovimentacaoTipoService();
		$objResp = $MovimentacaoTipoServ->buscaPorId($vMcMtId);
		
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao pesquisar tipo da movimenta&ccedil;&atilde;o. Tente novamente em breve.";
		
			echo json_encode($arrJSON);
			return;
		}
		$MovimentacaoTipo = $objResp->getRetByKey("ent");
		// =================================
		
		// busca Movimentacao para editar
		$MovimentacaoCat = new MovimentacaoCat();
		$MovimentacaoCatServ = new MovimentacaoCatService();
		
		$objResp = $MovimentacaoCatServ->buscaPorId($vMcId);
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao buscar categoria. Motivo: " . $objResp->getRetByKey("msg");

			echo json_encode($arrJSON);
			return;
		}
		// ==============================
		
		$MovimentacaoCat = $objResp->getRetByKey("ent");
		$MovimentacaoCat->setDescricao($vMcDescricao);
		$MovimentacaoCat->setMovimentacaoTipo($MovimentacaoTipo);
		
		if( $MovimentacaoCat->getUsuario()->getId() != $this->loggedUserId ){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao alterar movimenta&ccedil;&atilde;o. Motivo: usu&aacute;rio diferente.";
			
			echo json_encode($arrJSON);
			return;
		}
		
		$objResp = $MovimentacaoCatServ->edita($MovimentacaoCat);
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
		$vMcId = (isset($_POST["id"])) ? $_POST["id"]: "";
		// =========
		
		// busca subcat para editar
		$MovimentacaoCat = new MovimentacaoCat();
		$MovimentacaoCatServ = new MovimentacaoCatService();
		
		$objResp = $MovimentacaoCatServ->buscaPorId($vMcId);
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao buscar categoria. Motivo: " . $objResp->getRetByKey("msg");
		
			echo json_encode($arrJSON);
			return;
		}
		
		$MovimentacaoCat = $objResp->getRetByKey("ent");
		$MovimentacaoCat->setAtivo("f");
		
		if( $MovimentacaoCat->getUsuario()->getId() != $this->loggedUserId ){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao deletar categoria. Motivo: usu&aacute;rio diferente.";
				
			echo json_encode($arrJSON);
			return;
		}
		
		$objResp = $MovimentacaoCatServ->edita($MovimentacaoCat);
		// =======================
		
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = $objResp->getRetByKey("msg");
				
			echo json_encode($arrJSON);
			return;
		}
		else{
			$arrJSON["retorno"] = "OK"; // OK, ERRO
			$arrJSON["msg"] = "Categoria deletada com sucesso!";
				
			echo json_encode($arrJSON);
			return;
		}
	}
	
	protected function postDeletarSubCat(){
		// @todo Quando a subcat tiver contas, ver oq fazer!
		
		// array resposta JSON
		$arrJSON = array();
		$arrJSON["retorno"] = ""; // OK, ERRO
		$arrJSON["msg"] = "";
		$arrJSON["html"] = "";
		// ===================
		
		// variaveis
		$userId = $this->loggedUserId;
		$vMcId = (isset($_POST["id"])) ? $_POST["id"]: "";
		// =========
		
		// busca subcat para editar
		$MovimentacaoCat = new MovimentacaoCat();
		$MovimentacaoCatServ = new MovimentacaoCatService();
		
		$objResp = $MovimentacaoCatServ->buscaPorId($vMcId);
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao buscar subcategoria. Motivo: " . $objResp->getRetByKey("msg");
		
			echo json_encode($arrJSON);
			return;
		}
		
		$MovimentacaoCat = $objResp->getRetByKey("ent");
		$MovimentacaoCat->setAtivo("f");
		
		if( $MovimentacaoCat->getUsuario()->getId() != $this->loggedUserId ){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao deletar subcategoria. Motivo: usu&aacute;rio diferente.";
				
			echo json_encode($arrJSON);
			return;
		}
		
		$objResp = $MovimentacaoCatServ->edita($MovimentacaoCat);
		// =======================
		
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = $objResp->getRetByKey("msg");
				
			echo json_encode($arrJSON);
			return;
		}
		else{
			// gera o html da subcat
			$htmlSubcat = "";
			$objResp = $MovimentacaoCatServ->pegaTodosFilhos($MovimentacaoCat->getCategoriaPai());
			if($objResp->isOk()){
				$arr_subcat = $objResp->getRetByKey("arr_subcategorias");				
				$htmlSubcat = $this->model->getHtmlSubcat($arr_subcat);
			}
			// =====================			
			
			$arrJSON["retorno"] = "OK"; // OK, ERRO
			$arrJSON["msg"] = "Subcategoria deletada com sucesso!";
			$arrJSON["html"] = $htmlSubcat;
				
			echo json_encode($arrJSON);
			return;
		}
	}

	protected function insereSubcategorias(){
		// array resposta JSON
		$arrJSON = array();
		$arrJSON["retorno"] = ""; // OK, ERRO
		$arrJSON["msg"] = "";
		$arrJSON["html"] = "";
		// ===================
		
		// variaveis
		$userId = $this->loggedUserId;
		$vMcId = (isset($_POST["mc_id"])) ? $_POST["mc_id"]: "";
		$vSubcatDesc = (isset($_POST["subcat_desc"])) ? $_POST["subcat_desc"]: "";
		// =========
		
		// busca subcat para editar
		$MovimentacaoCat = new MovimentacaoCat();
		$MovimentacaoCatServ = new MovimentacaoCatService();
		
		$objResp = $MovimentacaoCatServ->buscaPorId($vMcId);
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao buscar subcategoria. Motivo: " . $objResp->getRetByKey("msg");
		
			echo json_encode($arrJSON);
			return;
		}
		
		$MovimentacaoCat = $objResp->getRetByKey("ent");
		if( $MovimentacaoCat->getUsuario()->getId() != $this->loggedUserId ){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao deletar subcategoria. Motivo: usu&aacute;rio diferente.";
		
			echo json_encode($arrJSON);
			return;
		}
		// =======================
		
		// insere a subcategoria na categoria
		$MovimentacaoCatFilho = new MovimentacaoCat();
		$MovimentacaoCatFilho->setAtivo(true);
		$MovimentacaoCatFilho->setCategoriaPai($MovimentacaoCat);
		$MovimentacaoCatFilho->setDescricao($vSubcatDesc);
		$MovimentacaoCatFilho->setMovimentacaoTipo($MovimentacaoCat->getMovimentacaoTipo());
		$MovimentacaoCatFilho->setUsuario($MovimentacaoCat->getUsuario());
		
		$objResp = $MovimentacaoCatServ->insere($MovimentacaoCatFilho);
		// ==================================
		
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = $objResp->getRetByKey("msg");
		
			echo json_encode($arrJSON);
			return;
		}
		else{
			// gera o html da subcat
			$htmlSubcat = "";
			$objResp = $MovimentacaoCatServ->pegaTodosFilhos($MovimentacaoCat);
			if($objResp->isOk()){
				$arr_subcat = $objResp->getRetByKey("arr_subcategorias");
				$htmlSubcat = $this->model->getHtmlSubcat($arr_subcat);
			}
			// =====================
				
			$arrJSON["retorno"] = "OK"; // OK, ERRO
			$arrJSON["msg"] = "Subcategoria inserida com sucesso!";
			$arrJSON["html"] = $htmlSubcat;
		
			echo json_encode($arrJSON);
			return;
		}
	}

	protected function pegaHtmlSubCat(){
		$MovimentacaoCatServ = new MovimentacaoCatService();
		
		// variaveis
		$userId = $this->loggedUserId;
		$vMcId = (isset($_POST["id_cat_pai"])) ? $_POST["id_cat_pai"]: "";
		$vValor = (isset($_POST["valor"])) ? $_POST["valor"]: "";
		$vNome = (isset($_POST["nome"])) ? $_POST["nome"]: "";
		// =========
		
		// pega entidade Usuario
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/entities/Usuario.entity.php";
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/Usuario.service.php";
		$Usuario = new Usuario();
		$UsuarioServ = new UsuarioService();
		
		$objRet = $UsuarioServ->buscaPorId($this->loggedUserId);
		if($objRet->isOk()){
			$Usuario = $objRet->getRetByKey("ent");
		}
		// =====================
		
		// pega categoria pai
		$MovimentacaoCatPai = new MovimentacaoCat();
		$objRet = $MovimentacaoCatServ->buscaPorId($vMcId);
		if($objRet->isOk()){
			$MovimentacaoCatPai = $objRet->getRetByKey("ent");
		}
		// ==================
		
		$objRet = $MovimentacaoCatServ->getHtmlCategoriaSub($MovimentacaoCatPai, $Usuario, $vNome, $vValor);
		if($objRet->isErro()){
			$html = $objRet->getRetByKey("html_cb_subcategoria");
			echo $html;
		}
		else{
			$html = $objRet->getRetByKey("html_cb_subcategoria");
			echo $html;
		}
	}
}

?>