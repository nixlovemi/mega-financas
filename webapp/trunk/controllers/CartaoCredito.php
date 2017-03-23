<?php
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/entities/CartaoCredito.entity.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/CartaoCredito.service.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/CartaoCreditoFat.service.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/CartaoCreditoMov.service.php';
require_once $_SERVER ['BIRDS_HOME'] . "classes-general/Session.class.php";
require_once $_SERVER ['BIRDS_HOME'] . "classes-general/Validation.class.php";

class CartaoCreditoController extends BaseController{
	private $Session;
	private $loggedUserId;
	
	// add to the parent constructor
	public function __construct($action, $urlValues){

		parent::__construct($action, $urlValues);
		$this->Session = new Session();
		$this->loggedUserId = $this->Session->getLoggedUserId();
		
		// create the model object
		require ("models/CartaoCredito.php");
		$this->model = new CartaoCreditoModel();
	
	}
	
	// default method
	protected function index(){
		$erro_msg = "";
		$userId = $this->loggedUserId;
		$CartaoCreditoServ = new CartaoCreditoService();
		
		// Usuario Logado ====================
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/Usuario.service.php";
		
		$UsuarioServ = new UsuarioService();
		$objResp = $UsuarioServ->buscaPorId( $userId );
		if( $objResp->isErro() ){
			$erro_msg = "Erro ao buscar usu&aacute;rio logado. Tente novamente em breve!";
		}
		$Usuario = $objResp->getRetByKey("ent");
		// ===================================
		
		$objResp = $CartaoCreditoServ->pegaListaCartoes($Usuario);
		if($objResp->isErro()){
			$erro_msg = $objResp->getRetByKey("msg");
		}
		else{
			$arr_CartaoCreditos = $objResp->getRetByKey("arr_lista_cartao");
		}
		
		$viewModel = new ViewModel();
		$viewModel = $this->model->index();
		$viewModel->set("erro_msg", $erro_msg);
		$viewModel->set("arr_CartaoCreditos", $arr_CartaoCreditos);
		
		$this->view->output($this->model->index(), "");
	
	}
	
	protected function incluir(){
		// inicio das variaveis =================================
		$id = "";
		$id_button = "btn-grava-novo-cartao";
		$titulo_janela = "<i class='fa fa-credit-card'></i> &nbsp;Novo Cart&atilde;o";
		
		$descricao = "";
		$id_bandeira = "";
		$arr_bandeira = array();
		$limite = "";
		$dia_fechamento = "";
		$dia_pagamento = "";
		// ======================================================
		
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/BandeiraCartao.service.php";
		$BandeiraCartaoServ = new BandeiraCartaoService();
		$objStatus = $BandeiraCartaoServ->pegaTodos();
		if($objStatus->isOk()){
			$arr_bandeira = $objStatus->getRetByKey("arr_bandeira_cartao");
		}
		
		$viewModel = new ViewModel();
		$viewModel = $this->model->incluir();
		
		$viewModel->set("id", $id);
		$viewModel->set("id_button", $id_button);
		$viewModel->set("titulo_janela", $titulo_janela);
		$viewModel->set("descricao", $descricao);
		$viewModel->set("id_bandeira", $id_bandeira);
		$viewModel->set("arr_bandeira", $arr_bandeira);
		$viewModel->set("limite", $limite);
		$viewModel->set("dia_fechamento", $dia_fechamento);
		$viewModel->set("dia_pagamento", $dia_pagamento);
		
		$this->view->output($this->model->incluir(), "");
	}

	protected function postIncluir(){
		require_once $_SERVER ['BIRDS_HOME'] . "classes-general/StringUtils.class.php";
		$StringUtils = new StringUtils();
		
		// array resposta JSON =========================
		$arrJSON = array();
		$arrJSON["retorno"] = ""; // OK, ERRO
		$arrJSON["msg"] = "";
		// =============================================
		
		// variaveis ===================================
		$userId = $this->loggedUserId;
		$vCcDescriao = (isset($_POST["inpt-cc-descricao"])) ? $_POST["inpt-cc-descricao"]: "";
		$vCcBcId = (isset($_POST["inpt-cc-bc-id"])) ? $_POST["inpt-cc-bc-id"]: "";
		$vCcLimite = (isset($_POST["cc-limite"])) ? $StringUtils->strVlrBrToBd($_POST["cc-limite"]): "";
		$vCcDiaFech = (isset($_POST["cc-dia-fechamento"])) ? $_POST["cc-dia-fechamento"]: "";
		$vCcDiaPgto = (isset($_POST["cc-dia-pagamento"])) ? $_POST["cc-dia-pagamento"]: "";
		// =============================================
		
		// Usuario Logado ====================
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/Usuario.service.php";
		
		$UsuarioServ = new UsuarioService();
		$objResp = $UsuarioServ->buscaPorId( $userId );
		if( $objResp->isErro() ){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao buscar usu&aacute;rio logado. Tente novamente em breve!";
				
			echo json_encode($arrJSON);
			return;
		}
		$Usuario = $objResp->getRetByKey("ent");
		// ===================================
		
		// entidade bandeira cartao ====================
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/BandeiraCartao.service.php";
		$BandeiraCartaoServ = new BandeiraCartaoService();
		$objResp = $BandeiraCartaoServ->buscaPorId($vCcBcId);
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao buscar a bandeira do cart&atilde;o. Tente novamente em breve.";
			
			echo json_encode($arrJSON);
			return;
		}
		
		$BandeiraCartao = $objResp->getRetByKey("ent");
		// =============================================
		
		// cria Cartao pra inserir =====================
		$CartaoCredito = new CartaoCredito();
		
		$CartaoCredito->setBandeiraCartao($BandeiraCartao);
		$CartaoCredito->setDeletado('f');
		$CartaoCredito->setDescricao($vCcDescriao);
		$CartaoCredito->setDiaFechamento($vCcDiaFech);
		$CartaoCredito->setDiaPagamento($vCcDiaPgto);
		$CartaoCredito->setLimite($vCcLimite);
		$CartaoCredito->setUsuario($Usuario);
		// =============================================
		
		$CartaoCreditoServ = new CartaoCreditoService();
		$objResp = $CartaoCreditoServ->insere($CartaoCredito);
		
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
		// inicio das variaveis =================================
		$id = $_POST["id"];
		$id_button = "btn-grava-edita-cartao";
		$titulo_janela = "<i class='fa fa-credit-card'></i> &nbsp;Editar Cart&atilde;o";
		
		$descricao = "";
		$id_bandeira = "";
		$arr_bandeira = array();
		$limite = "";
		$dia_fechamento = "";
		$dia_pagamento = "";
		// ======================================================
		
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/BandeiraCartao.service.php";
		$BandeiraCartaoServ = new BandeiraCartaoService();
		$objStatus = $BandeiraCartaoServ->pegaTodos();
		if($objStatus->isOk()){
			$arr_bandeira = $objStatus->getRetByKey("arr_bandeira_cartao");
		}
		
		// busca cartao pelo id =================================
		$CartaoCreditoServ = new CartaoCreditoService();
		$objRet = $CartaoCreditoServ->buscaPorId($id);
		if($objRet->isErro()){
			echo "Erro ao editar Cart&atilde;o Cr&eacute;dito! Desc.:" . $objResp->getRetByKey("msg"); //@todo forcar erro pra ver se msg aparece
			return;
		}
		
		$CartaoCredito = $objRet->getRetByKey("ent");
		
		$descricao = $CartaoCredito->getDescricao();
		$id_bandeira = $CartaoCredito->getBandeiraCartao()->getId();
		$limite = $CartaoCredito->getLimite();
		$dia_fechamento = $CartaoCredito->getDiaFechamento();
		$dia_pagamento = $CartaoCredito->getDiaPagamento();
		// ======================================================
		
		$viewModel = new ViewModel();
		$viewModel = $this->model->editar();
		
		$viewModel->set("id", $id);
		$viewModel->set("id_button", $id_button);
		$viewModel->set("titulo_janela", $titulo_janela);
		$viewModel->set("descricao", $descricao);
		$viewModel->set("id_bandeira", $id_bandeira);
		$viewModel->set("arr_bandeira", $arr_bandeira);
		$viewModel->set("limite", $limite);
		$viewModel->set("dia_fechamento", $dia_fechamento);
		$viewModel->set("dia_pagamento", $dia_pagamento);
		
		$this->view->output($this->model->editar(), "");
	}
	
	protected function postEditar(){
		require_once $_SERVER ['BIRDS_HOME'] . "classes-general/StringUtils.class.php";
		$StringUtils = new StringUtils();
		$CartaoCreditoServ = new CartaoCreditoService();
		
		// array resposta JSON =========================
		$arrJSON = array();
		$arrJSON["retorno"] = ""; // OK, ERRO
		$arrJSON["msg"] = "";
		// =============================================
		
		// variaveis ===================================
		$userId = $this->loggedUserId;
		
		$vCcId = (isset($_POST["hddn-id-conta"])) ? $_POST["hddn-id-conta"]: "";
		$vCcDescriao = (isset($_POST["inpt-cc-descricao"])) ? $_POST["inpt-cc-descricao"]: "";
		$vCcBcId = (isset($_POST["inpt-cc-bc-id"])) ? $_POST["inpt-cc-bc-id"]: "";
		$vCcLimite = (isset($_POST["cc-limite"])) ? $StringUtils->strVlrBrToBd($_POST["cc-limite"]): "";
		$vCcDiaFech = (isset($_POST["cc-dia-fechamento"])) ? $_POST["cc-dia-fechamento"]: "";
		$vCcDiaPgto = (isset($_POST["cc-dia-pagamento"])) ? $_POST["cc-dia-pagamento"]: "";
		// =============================================
		
		// Usuario Logado ====================
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/Usuario.service.php";
		
		$UsuarioServ = new UsuarioService();
		$objResp = $UsuarioServ->buscaPorId( $userId );
		if( $objResp->isErro() ){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao buscar usu&aacute;rio logado. Tente novamente em breve!";
		}
		$Usuario = $objResp->getRetByKey("ent");
		// ===================================
		
		// entidade bandeira cartao ====================
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/BandeiraCartao.service.php";
		$BandeiraCartaoServ = new BandeiraCartaoService();
		$objResp = $BandeiraCartaoServ->buscaPorId($vCcBcId);
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao buscar a bandeira do cart&atilde;o. Tente novamente em breve.";
				
			echo json_encode($arrJSON);
			return;
		}
		
		$BandeiraCartao = $objResp->getRetByKey("ent");
		// =============================================
		
		// busca entidade pra alterar ==================
		$objResp = $CartaoCreditoServ->buscaPorId($vCcId);
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao buscar o cart&atilde;o. Tente novamente em breve.";
			
			echo json_encode($arrJSON);
			return;
		}
		
		$CartaoCredito = $objResp->getRetByKey("ent");
		// =============================================
		
		// cria Cartao pra editar ======================
		$CartaoCredito->setBandeiraCartao($BandeiraCartao);
		$CartaoCredito->setDescricao($vCcDescriao);
		$CartaoCredito->setDiaFechamento($vCcDiaFech);
		$CartaoCredito->setDiaPagamento($vCcDiaPgto);
		$CartaoCredito->setLimite($vCcLimite);
		$CartaoCredito->setUsuario($Usuario);
		// =============================================
		
		$objResp = $CartaoCreditoServ->edita($CartaoCredito);
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
		require_once $_SERVER ['BIRDS_HOME'] . "classes-general/StringUtils.class.php";
		$StringUtils = new StringUtils();
		$CartaoCreditoServ = new CartaoCreditoService();
		
		// array resposta JSON =========================
		$arrJSON = array();
		$arrJSON["retorno"] = ""; // OK, ERRO
		$arrJSON["msg"] = "";
		// =============================================
		
		// variaveis ===================================
		$userId = $this->loggedUserId;
		$vCcId = (isset($_POST["id"])) ? $_POST["id"]: "";
		// =============================================
		
		// Usuario Logado ====================
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/Usuario.service.php";
		
		$UsuarioServ = new UsuarioService();
		$objResp = $UsuarioServ->buscaPorId( $userId );
		if( $objResp->isErro() ){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao buscar usu&aacute;rio logado. Tente novamente em breve!";
		}
		$Usuario = $objResp->getRetByKey("ent");
		// ===================================
		
		// busca entidade pra alterar ==================
		$objResp = $CartaoCreditoServ->buscaPorId($vCcId);
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao buscar o cart&atilde;o. Tente novamente em breve.";
				
			echo json_encode($arrJSON);
			return;
		}
		
		$CartaoCredito = $objResp->getRetByKey("ent");
		// =============================================
		
		// cria Cartao pra deletar =====================
		$CartaoCredito->setDeletado('t');
		// =============================================
		
		$objResp = $CartaoCreditoServ->edita($CartaoCredito);
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = $objResp->getRetByKey("msg");
		
			echo json_encode($arrJSON);
			return;
		}
		else{
			$arrJSON["retorno"] = "OK"; // OK, ERRO
			$arrJSON["msg"] = "Cart&atilde;o deletado com sucesso!";
		
			echo json_encode($arrJSON);
			return;
		}
	}

	protected function indexLancamentos(){
		require_once $_SERVER ['BIRDS_HOME'] . "classes-general/StringUtils.class.php";
		$StringUtils = new StringUtils();
		
		// variaveis
		$userId = $this->loggedUserId;
		$erro_msg = "";
		$dtFatura = (isset($_POST["dt_fatura"]) && strlen($_POST["dt_fatura"]) == 10) ? $StringUtils->strDateBrToBd($_POST["dt_fatura"]): date('Y-m-01');
		$idCartao = (isset($_POST["idCartao"]) && is_numeric($_POST["idCartao"])) ? $_POST["idCartao"]: "";
		// =========
		
		// Usuario Logado ====================
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/Usuario.service.php";
		
		$UsuarioServ = new UsuarioService();
		$objResp = $UsuarioServ->buscaPorId( $userId );
		if( $objResp->isErro() ){
			$erro_msg = "Erro ao buscar usu&aacute;rio logado. Tente novamente em breve!";
		}
		$Usuario = $objResp->getRetByKey("ent");
		// ===================================
		
		// Cartoes do Usuario ================
		list($CartaoCredito, $arrCartaoCredito) = $this->model->pegaCartoesUsuario($Usuario, $idCartao);
		$idCartao = ($idCartao == "" && is_numeric($CartaoCredito->getId())) ? $CartaoCredito->getId(): $idCartao;
		// ===================================
		
		// meses que tem fatura ==============
		$arrMes = $this->model->pegaMesesFaturaCartao($CartaoCredito);
		// ===================================
		
		// entidade cartao credito ====================
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/CartaoCredito.service.php";
		$CartaoCreditoServ = new CartaoCreditoService();
		$CartaoCredito = new CartaoCredito();
		
		$objResp = $CartaoCreditoServ->buscaPorId($idCartao);
		if($objResp->isErro()){
			$arrMovCartao = array();
		}
		else{
			$CartaoCredito = $objResp->getRetByKey("ent");
		}
		// =============================================
		
		// pega as movimentacoes do cartao
		$CartaoCreditoMovServ = new CartaoCreditoMovService();
		$arrDtFatura = explode("-", $dtFatura);
		$mesFatura = $arrDtFatura[1];
		$anoFatura = $arrDtFatura[0];
		
		$objRet = $CartaoCreditoMovServ->buscaMovimentacoes($CartaoCredito, $mesFatura, $anoFatura);
		if($objRet->isOk()){
			$arrMovCartao = $objRet->getRetByKey("rs");
		}
		// ===============================
		
		$viewModel = new ViewModel();
		$viewModel = $this->model->indexLancamentos();
		$viewModel->set("erro_msg", $erro_msg);
		$viewModel->set("data_fatura", $dtFatura);
		$viewModel->set("arr_mes", $arrMes);
		$viewModel->set("cartao_descricao", $CartaoCredito->getDescricao());
		$viewModel->set("id_cartao", $idCartao);
		$viewModel->set("arr_cartao", $arrCartaoCredito);
		$viewModel->set("arr_movimentacoes", $arrMovCartao);
		$viewModel->set("btn_topo_id", "btn-novo-lancamento-cartao");
		$viewModel->set("btn_topo_text", "NOVA LAN&Ccedil;AMENTO");
		$viewModel->set("icon_topo_name", "fa-money");
		$viewModel->set("icon_topo_text", "Lan&ccedil;amento Cart&atilde;o");
		
		$this->view->output($viewModel, "");
	}

	protected function incluirLctoCartao(){
		// inicio das variaveis =================================
		$id = "";
		$id_button = "btn-grava-novo-lcto-cartao";
		$titulo_janela = "<i class='fa fa-credit-card'></i> &nbsp;Novo Lan&ccedil;amento";
		$userId = $this->loggedUserId;
		
		$descricao = "";
		// ======================================================
		
		// Usuario Logado ====================
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/Usuario.service.php";
		
		$UsuarioServ = new UsuarioService();
		$objResp = $UsuarioServ->buscaPorId( $userId );
		if( $objResp->isErro() ){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao buscar usu&aacute;rio logado. Tente novamente em breve!";
				
			echo json_encode($arrJSON);
			return;
		}
		$Usuario = $objResp->getRetByKey("ent");
		// ===================================
		
		// cartoes do usuario ================
		$CartaoCreditoServ = new CartaoCreditoService();
		$objResp = $CartaoCreditoServ->pegaListaCartoes($Usuario);
		if($objResp->isErro()){
			$arrCartoesUsu = array();
		}
		
		$arrCartoesUsu = $objResp->getRetByKey("arr_lista_cartao");
		// ===================================
		
		// categorias de despesa =============
		require_once $_SERVER ['BIRDS_HOME'] . "models/Movimentacao.php";
		$MovimentacaoModel = new MovimentacaoModel();
		
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/MovimentacaoTipo.service.php";
		$MovimentacaoTipoServ = new MovimentacaoTipoService();
		$objRet = $MovimentacaoTipoServ->pegaDespesa();
		if($objRet->isErro()){
			die("Erro ao buscar despesas. Tente novamente!"); //@todo forcar erro e ver se msg aparece
		}
		$MovimentacaoTipo = $objRet->getRetByKey("ent");
		
		$nome_combo_cat = "mov_cat";
		$valor_combo_cat = "";
		$combo_categoria = $MovimentacaoModel->pegaHtmlCat($Usuario, $nome_combo_cat, $valor_combo_cat, $MovimentacaoTipo);
		// ===================================
		
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/BandeiraCartao.service.php";
		$BandeiraCartaoServ = new BandeiraCartaoService();
		$objStatus = $BandeiraCartaoServ->pegaTodos();
		if($objStatus->isOk()){
			$arr_bandeira = $objStatus->getRetByKey("arr_bandeira_cartao");
		}
		
		$viewModel = new ViewModel();
		$viewModel = $this->model->incluir();
		
		$viewModel->set("id", $id);
		$viewModel->set("id_button", $id_button);
		$viewModel->set("titulo_janela", $titulo_janela);
		$viewModel->set("descricao", $descricao);
		$viewModel->set("arr_lista_cartao", $arrCartoesUsu);
		$viewModel->set("html_combo_categoria", $combo_categoria);
		
		$this->view->output($this->model->incluirLctoCartao(), "");
	}

	protected function postIncluirLctoCartao(){
		require_once $_SERVER ['BIRDS_HOME'] . "classes-general/StringUtils.class.php";
		$StringUtils = new StringUtils();
		$CartaoCreditoMovServ = new CartaoCreditoMovService();
		$Validation = new Validation();
		
		// array resposta JSON =========================
		$arrJSON = array();
		$arrJSON["retorno"] = ""; // OK, ERRO
		$arrJSON["msg"] = "";
		// =============================================
		
		// variaveis ===================================
		$userId = $this->loggedUserId;
		$vCcmDescricao = (isset($_POST["inpt-ccm_descricao"])) ? $_POST["inpt-ccm_descricao"]: "";
		$vCcmData = (isset($_POST["inpt-ccm_data"])) ? $StringUtils->strDateBrToBd($_POST["inpt-ccm_data"]): "";
		$vCcmValor = (isset($_POST["inpt-ccm_valor"])) ? $StringUtils->strVlrBrToBd($_POST["inpt-ccm_valor"]): "";
		$vCcmCartaoCred = (isset($_POST["inpt-ccf_cc_id"])) ? $_POST["inpt-ccf_cc_id"]: "";
		$vCcmCategoria = (isset($_POST["mov_cat"])) ? $_POST["mov_cat"]: "";
		$vCcmSubCategoria = (isset($_POST["mov_subcat"])) ? $_POST["mov_subcat"]: "";
		$vCmmId = (isset($_POST["hddn-id-conta"])) ? $_POST["hddn-id-conta"]: "";
		$vRepetirPor = (isset($_POST["mov_repetir_por"]) && is_numeric($_POST["mov_repetir_por"])) ? $_POST["mov_repetir_por"]: 0;
		$vIdParcelado = (isset($vRepetirPor) && $vRepetirPor > 0) ? $CartaoCreditoMovServ->pegaIdParcelado(): NULL;
		// =============================================
		
		// Usuario Logado ====================
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/Usuario.service.php";
		
		$UsuarioServ = new UsuarioService();
		$objResp = $UsuarioServ->buscaPorId( $userId );
		if( $objResp->isErro() ){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao buscar usu&aacute;rio logado. Tente novamente em breve!";
		
			echo json_encode($arrJSON);
			return;
		}
		$Usuario = $objResp->getRetByKey("ent");
		// ===================================
		
		// entidade movimentacao cat ====================
		$vCategoria = ($vCcmSubCategoria != "") ? $vCcmSubCategoria: $vCcmCategoria;
		
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/MovimentacaoCat.service.php";
		$MovimentacaoCatServ = new MovimentacaoCatService();
		$objResp = $MovimentacaoCatServ->buscaPorId($vCategoria);
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao buscar a categoria. Tente novamente em breve.";
				
			echo json_encode($arrJSON);
			return;
		}
		
		$MovimentacaoCat = $objResp->getRetByKey("ent");
		// =============================================
		
		// entidade cartao credito ====================
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/CartaoCredito.service.php";
		$CartaoCreditoServ = new CartaoCreditoService();
		$objResp = $CartaoCreditoServ->buscaPorId($vCcmCartaoCred);
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao buscar a cart&atilde;o de cr&eacute;dito. Tente novamente em breve.";
		
			echo json_encode($arrJSON);
			return;
		}
		
		$CartaoCredito = $objResp->getRetByKey("ent");
		// =============================================
		
		// cria Cartao pra inserir =====================
		$V_parcela = 0;
		$vDtVcto = $vCcmData;
		$vDia = date("d", strtotime($vDtVcto));
		
		for($i=0; $i<=$vRepetirPor; $i++){
			// calcula nr parcelas =====================
			if($vRepetirPor > 0){
				$V_parcela++;
			}
			else{
				$V_parcela = NULL;
			}
			// =========================================
				
			// calcula dt vcto =========================
			if($i>0){
				$vDtVcto = date("Y-m-", strtotime($vDtVcto)) . "01";
				$vDtVcto = date("Y-m-", strtotime("+1 month", strtotime($vDtVcto))) . $vDia;
		
				if(!$Validation->isDate($vDtVcto)){
					$vDtVcto = explode("-", $vDtVcto);
					$vDtVcto = $vDtVcto[0] . "-" . $vDtVcto[1] . '-01';
					$vDtVcto = date("Y-m-t", strtotime($vDtVcto));
				}
			}
			// =========================================
			
			$CartaoCreditoFatServ = new CartaoCreditoFatService();
			$CartaoCreditoFat = $CartaoCreditoFatServ->buscaFaturaPorDespesa($CartaoCredito, $vDtVcto);
				
			$CartaoCreditoMov = new CartaoCreditoMov();
		
			$CartaoCreditoMov->setCartaoCreditoFat($CartaoCreditoFat);
			$CartaoCreditoMov->setData($vDtVcto);
			$CartaoCreditoMov->setDescricao($vCcmDescricao);
			$CartaoCreditoMov->setMovimentacaoCat($MovimentacaoCat);
			$CartaoCreditoMov->setValor($vCcmValor);
			$CartaoCreditoMov->setIdParcelado($vIdParcelado);
			$CartaoCreditoMov->setParcela($V_parcela);
			
			$objResp = $CartaoCreditoMovServ->insere($CartaoCreditoMov);
		}
		// =============================================
		
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

	protected function postDeletarMov(){
		require_once $_SERVER ['BIRDS_HOME'] . "classes-general/StringUtils.class.php";
		$StringUtils = new StringUtils();
		$CartaoCreditoMovServ = new CartaoCreditoMovService();
		
		// array resposta JSON =========================
		$arrJSON = array();
		$arrJSON["retorno"] = ""; // OK, ERRO
		$arrJSON["msg"] = "";
		// =============================================
		
		// variaveis ===================================
		$userId = $this->loggedUserId;
		$vCcmId = (isset($_POST["id"])) ? $_POST["id"]: "";
		// =============================================
		
		// Usuario Logado ====================
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/Usuario.service.php";
		
		$UsuarioServ = new UsuarioService();
		$objResp = $UsuarioServ->buscaPorId( $userId );
		if( $objResp->isErro() ){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao buscar usu&aacute;rio logado. Tente novamente em breve!";
		}
		$Usuario = $objResp->getRetByKey("ent");
		// ===================================
		
		// busca entidade pra deletar ==================
		$objResp = $CartaoCreditoMovServ->buscaPorId($vCcmId);
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao buscar a movimenta&ccedil;&atilde;o. Tente novamente em breve.";
		
			echo json_encode($arrJSON);
			return;
		}
		
		$CartaoCreditoMov = $objResp->getRetByKey("ent");
		// =============================================
		
		// cria Cartao pra deletar =====================
		$CartaoCreditoMov->setDeletado('t');
		// =============================================
		
		$objResp = $CartaoCreditoMovServ->edita($CartaoCreditoMov);
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = $objResp->getRetByKey("msg");
		
			echo json_encode($arrJSON);
			return;
		}
		else{
			$arrJSON["retorno"] = "OK"; // OK, ERRO
			$arrJSON["msg"] = "Lan&ccedil;amento deletado com sucesso!";
		
			echo json_encode($arrJSON);
			return;
		}
	}

	protected function editarLctoCartao(){
		// inicio das variaveis =================================
		$id = (isset($_POST["id"])) ? $_POST["id"]: "";
		$id_button = "btn-grava-edita-lcto-cartao";
		$titulo_janela = "<i class='fa fa-credit-card'></i> &nbsp;Editar Lan&ccedil;amento";
		$userId = $this->loggedUserId;
		// ======================================================
		
		// busca movimentacao ================
		$CartaoCreditoMovServ = new CartaoCreditoMovService();
		$objRet = $CartaoCreditoMovServ->buscaPorId($id);
		if( $objRet->isErro() ){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao buscar lan&ccedil;amento logado. Tente novamente em breve!";
		
			echo json_encode($arrJSON);
			return;
		}
		$CartaoCreditoMov = $objRet->getRetByKey("ent");
		
		$Usuario = $CartaoCreditoMov->getCartaoCreditoFat()->getCartaoCredito()->getUsuario();
		$descricao = $CartaoCreditoMov->getDescricao();
		$dtDespesa = ($CartaoCreditoMov->getData() != "") ? date("d/m/Y", strtotime($CartaoCreditoMov->getData())): "";
		$vlrDespesa = ($CartaoCreditoMov->getValor() != "") ? number_format($CartaoCreditoMov->getValor(), 2, ",", "."): "";
		$idCartao = (is_numeric($CartaoCreditoMov->getCartaoCreditoFat()->getCartaoCredito()->getId())) ? $CartaoCreditoMov->getCartaoCreditoFat()->getCartaoCredito()->getId(): "";
		$parcela = (is_numeric($CartaoCreditoMov->getParcela())) ? $CartaoCreditoMov->getParcela(): "";
		$tot_parcelas = (is_numeric($CartaoCreditoMov->getQtdeParcelas())) ? $CartaoCreditoMov->getQtdeParcelas(): "";
		// ===================================
		
		// cartoes do usuario ================
		$CartaoCreditoServ = new CartaoCreditoService();
		$objResp = $CartaoCreditoServ->pegaListaCartoes($Usuario);
		if($objResp->isErro()){
			$arrCartoesUsu = array();
		}
		
		$arrCartoesUsu = $objResp->getRetByKey("arr_lista_cartao");
		// ===================================
		
		// categorias de despesa =============
		require_once $_SERVER ['BIRDS_HOME'] . "models/Movimentacao.php";
		$MovimentacaoModel = new MovimentacaoModel();
		
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/MovimentacaoTipo.service.php";
		$MovimentacaoTipoServ = new MovimentacaoTipoService();
		$objRet = $MovimentacaoTipoServ->pegaDespesa();
		if($objRet->isErro()){
			die("Erro ao buscar despesas. Tente novamente!"); //@todo forcar erro e ver se msg aparece
		}
		$MovimentacaoTipo = $objRet->getRetByKey("ent");
		
		list($V_MC_ID_PAI, $V_MC_ID_FILHO) = $this->model->pegaCatSubcat($CartaoCreditoMov->getMovimentacaoCat());
		$nome_combo_cat = "mov_cat";
		$valor_combo_cat = $V_MC_ID_PAI;
		$combo_categoria = $MovimentacaoModel->pegaHtmlCat($Usuario, $nome_combo_cat, $valor_combo_cat, $MovimentacaoTipo);
		// ===================================
		
		// combo subcategoria
		require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/MovimentacaoCat.service.php';
		$MovimentacaoCatServ = new MovimentacaoCatService();
		$objResp = $MovimentacaoCatServ->buscaPorId($V_MC_ID_PAI);
		if($objResp->isErro()){
			echo "Erro ao buscar categorias! Desc.:" . $objResp->getRetByKey("msg"); //@todo forcar erro pra ver se msg aparece
			return;
		}
		$MovCatPai = $objResp->getRetByKey("ent");
		
		$nome_combo_subcat = "mov_subcat";
		$valor_combo_cat = $V_MC_ID_FILHO;
		$combo_subcategoria = $this->model->pegaHtmlSubCat($Usuario, $MovCatPai, $nome_combo_subcat, $valor_combo_cat);
		// ==================
		
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/BandeiraCartao.service.php";
		$BandeiraCartaoServ = new BandeiraCartaoService();
		$objStatus = $BandeiraCartaoServ->pegaTodos();
		if($objStatus->isOk()){
			$arr_bandeira = $objStatus->getRetByKey("arr_bandeira_cartao");
		}
		
		$viewModel = new ViewModel();
		$viewModel = $this->model->incluir();
		
		$viewModel->set("id", $id);
		$viewModel->set("id_button", $id_button);
		$viewModel->set("titulo_janela", $titulo_janela);
		$viewModel->set("descricao", $descricao);
		$viewModel->set("dataDespesa", $dtDespesa);
		$viewModel->set("vlrDespesa", $vlrDespesa);
		$viewModel->set("idCartao", $idCartao);
		$viewModel->set("parcela", $parcela);
		$viewModel->set("tot_parcela", $tot_parcelas);
		$viewModel->set("arr_lista_cartao", $arrCartoesUsu);
		$viewModel->set("html_combo_categoria", $combo_categoria);
		$viewModel->set("html_combo_subcategoria", $combo_subcategoria);
		
		$this->view->output($this->model->incluirLctoCartao(), "");
	}

	protected function postEditarLctoCartao(){
		require_once $_SERVER ['BIRDS_HOME'] . "classes-general/StringUtils.class.php";
		$StringUtils = new StringUtils();
		
		// array resposta JSON =========================
		$arrJSON = array();
		$arrJSON["retorno"] = ""; // OK, ERRO
		$arrJSON["msg"] = "";
		// =============================================
		
		// variaveis ===================================
		$userId = $this->loggedUserId;
		$id = (isset($_POST["hddn-id-conta"])) ? $_POST["hddn-id-conta"]: "";
		$vCcmDescricao = (isset($_POST["inpt-ccm_descricao"])) ? $_POST["inpt-ccm_descricao"]: "";
		$vCcmData = (isset($_POST["inpt-ccm_data"])) ? $StringUtils->strDateBrToBd($_POST["inpt-ccm_data"]): "";
		$vCcmValor = (isset($_POST["inpt-ccm_valor"])) ? $StringUtils->strVlrBrToBd($_POST["inpt-ccm_valor"]): "";
		$vCcmCartaoCred = (isset($_POST["inpt-ccf_cc_id"])) ? $_POST["inpt-ccf_cc_id"]: "";
		$vCcmCategoria = (isset($_POST["mov_cat"])) ? $_POST["mov_cat"]: "";
		$vCcmSubCategoria = (isset($_POST["mov_subcat"])) ? $_POST["mov_subcat"]: "";
		$vCmmId = (isset($_POST["hddn-id-conta"])) ? $_POST["hddn-id-conta"]: "";
		// =============================================
		
		// entidade movimentacao cat ====================
		$vCategoria = ($vCcmSubCategoria != "") ? $vCcmSubCategoria: $vCcmCategoria;
		
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/MovimentacaoCat.service.php";
		$MovimentacaoCatServ = new MovimentacaoCatService();
		$objResp = $MovimentacaoCatServ->buscaPorId($vCategoria);
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao buscar a categoria. Tente novamente em breve.";
		
			echo json_encode($arrJSON);
			return;
		}
		
		$MovimentacaoCat = $objResp->getRetByKey("ent");
		// =============================================
		
		// entidade cartao credito ====================
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/CartaoCredito.service.php";
		$CartaoCreditoServ = new CartaoCreditoService();
		$objResp = $CartaoCreditoServ->buscaPorId($vCcmCartaoCred);
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao buscar a cart&atilde;o de cr&eacute;dito. Tente novamente em breve.";
		
			echo json_encode($arrJSON);
			return;
		}
		
		$CartaoCredito = $objResp->getRetByKey("ent");
		// =============================================
		
		$CartaoCreditoFatServ = new CartaoCreditoFatService();
		$CartaoCreditoFat = $CartaoCreditoFatServ->buscaFaturaPorDespesa($CartaoCredito, $vCcmData);
		
		// cria Cartao pra inserir =====================
		$CartaoCreditoMovServ = new CartaoCreditoMovService();
		$objResp = $CartaoCreditoMovServ->buscaPorId($id);
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao buscar o lan&ccedil;amento. Tente novamente em breve.";
		
			echo json_encode($arrJSON);
			return;
		}
		$CartaoCreditoMov = $objResp->getRetByKey("ent");		
		
		$CartaoCreditoMov->setCartaoCreditoFat($CartaoCreditoFat);
		
		if(!$CartaoCreditoMov->getParcela() > 0){
			$CartaoCreditoMov->setData($vCcmData);
		}
		
		$CartaoCreditoMov->setDescricao($vCcmDescricao);
		$CartaoCreditoMov->setMovimentacaoCat($MovimentacaoCat);
		$CartaoCreditoMov->setValor($vCcmValor);
		// =============================================
		
		$objResp = $CartaoCreditoMovServ->edita($CartaoCreditoMov);
		
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

	protected function mostraParcelas(){
		$viewModel = new ViewModel();
		$viewModel = $this->model->mostraParcelas();
		$erro_msg = "";
		
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
		
		$ccm_id = $_POST["ccm_id"];
		$titulo_janela = "Exibi&ccedil;&atilde;o do Parcelamento";
		
		// busca a entidade CartaoCreditoMov ============
		$CartaoCreditoMov = new CartaoCreditoMov();
		$CartaoCreditoMovServ = new CartaoCreditoMovService();
		$objRet = $CartaoCreditoMovServ->buscaPorId($ccm_id);
		if($objRet->isErro()){
			$erro_msg = "Erro ao buscar parcelamento. Desc.:" . $objRet->getRetByKey("msg");
		}
		else{
			$CartaoCreditoMov = $objRet->getRetByKey("ent");
		}
		// ==============================================
		
		// pega as movimentacoes baseadas no parcelamento
		$V_id_parcelado = $CartaoCreditoMov->getIdParcelado();
		$V_id_parcelado = (is_numeric($V_id_parcelado)) ? $V_id_parcelado: -1;
		$V_arr_parcelamento = array();
		
		$objRet = $CartaoCreditoMovServ->pegaMovimentacoesParcelamento($Usuario, $V_id_parcelado);
		if($objRet->isErro()){
			$erro_msg = "Erro ao buscar movimenta&ccedil;&otilde;es do parcelamento.";
		}
		else{
			$V_arr_parcelamento = $objRet->getRetByKey("arr_movParcelado");
		}
		// ==============================================
		
		$viewModel->set("erro_msg", $erro_msg);
		$viewModel->set("titulo_janela", $titulo_janela);
		$viewModel->set("arr_movimentacoes", $V_arr_parcelamento);
		
		$this->view->output($this->model->mostraParcelas(), "");
	}
}
?>