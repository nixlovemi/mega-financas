<?php
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/entities/Movimentacao.entity.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/Movimentacao.service.php';
require_once $_SERVER ['BIRDS_HOME'] . "classes-general/Session.class.php";

class MovimentacaoController extends BaseController{
	private $Session;
	private $loggedUserId;
	
	// add to the parent constructor
	public function __construct($action, $urlValues){

		parent::__construct($action, $urlValues);
		$this->Session = new Session();
		$this->loggedUserId = $this->Session->getLoggedUserId();
		
		// create the model object
		require ("models/Movimentacao.php");
		$this->model = new MovimentacaoModel();
	
	}
	
	protected function indexReceitas(){
		require_once $_SERVER ['BIRDS_HOME'] . "classes-general/StringUtils.class.php";
		$StringUtils = new StringUtils();
		
		// variaveis
		$userId = $this->loggedUserId;
		$erro_msg = "";
		$dtInicio = (isset($_POST["dtInicio"]) && strlen($_POST["dtInicio"]) == 10) ? $StringUtils->strDateBrToBd($_POST["dtInicio"]): date('Y-m-01');
		$dtFim = (isset($_POST["dtFim"]) && strlen($_POST["dtFim"]) == 10) ? $StringUtils->strDateBrToBd($_POST["dtFim"]): date('Y-m-t');
		$idConta = (isset($_POST["idConta"]) && is_numeric($_POST["idConta"])) ? $_POST["idConta"]: "";
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
		
		// Conta do Usuario ==================
		list($Conta, $arrContas) = $this->model->pegaContaUsuario($Usuario, $idConta);
		// ===================================
		
		$MovimentacaoServ = new MovimentacaoService();
		$objResp = $MovimentacaoServ->pegaMovimentacoesEntrada($Usuario, $Conta, $dtInicio, $dtFim);
		if($objResp->isErro()){
			$erro_msg = "Erro ao buscar Receitas. Erro: " . $objResp->getRetByKey("msg");
		}
		$arr_movEntradas = $objResp->getRetByKey("arr_movEntradas");
		// ===============================

                // cria array totalizador ========
                $arrTotais = $this->model->criaArrTotais($arr_movEntradas);
                // ===============================
		
		$viewModel = new ViewModel();
		$viewModel = $this->model->indexReceitas();
		$viewModel->set("erro_msg", $erro_msg);
		$viewModel->set("data_inicio", $dtInicio);
		$viewModel->set("data_fim", $dtFim);
		$viewModel->set("conta_descricao", $Conta->getNome());
		$viewModel->set("id_conta", $idConta);
		$viewModel->set("arr_contas", $arrContas);
		$viewModel->set("arr_movimentacoes", $arr_movEntradas);
                $viewModel->set("arr_totais", $arrTotais);
		$viewModel->set("btn_topo_id", "btn-nova-receita");
		$viewModel->set("btn_topo_text", "NOVA RECEITA");
		$viewModel->set("icon_topo_name", "fa-money");
		$viewModel->set("icon_topo_text", "Receitas");
		$viewModel->set("tp_movimentacao", "Receitas");
		
		$this->view->output($viewModel, "");
	
	}
	
	protected function indexDespesas(){
		require_once $_SERVER ['BIRDS_HOME'] . "classes-general/StringUtils.class.php";
		$StringUtils = new StringUtils();
		
		// variaveis
		$userId = $this->loggedUserId;
		$erro_msg = "";
		$dtInicio = (isset($_POST["dtInicio"]) && strlen($_POST["dtInicio"]) == 10) ? $StringUtils->strDateBrToBd($_POST["dtInicio"]): date('Y-m-01');
		$dtFim = (isset($_POST["dtFim"]) && strlen($_POST["dtFim"]) == 10) ? $StringUtils->strDateBrToBd($_POST["dtFim"]): date('Y-m-t');
		$idConta = (isset($_POST["idConta"]) && is_numeric($_POST["idConta"])) ? $_POST["idConta"]: "";
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
		
		// Conta do Usuario ==================
		list($Conta, $arrContas) = $this->model->pegaContaUsuario($Usuario, $idConta);
		// ===================================
		
		$MovimentacaoServ = new MovimentacaoService();
		$objResp = $MovimentacaoServ->pegaMovimentacoesSaida($Usuario, $Conta, $dtInicio, $dtFim);
		if($objResp->isErro()){
			$erro_msg = "Erro ao buscar Despesas. Erro: " . $objResp->getRetByKey("msg");
		}
		$arr_movSaida = $objResp->getRetByKey("arr_movSaidas");
		// ===============================

                // cria array totalizador ========
                $arrTotais = $this->model->criaArrTotais($arr_movSaida);
                // ===============================
		
		$viewModel = new ViewModel();
		$viewModel = $this->model->indexReceitas();
		$viewModel->set("erro_msg", $erro_msg);
		$viewModel->set("data_inicio", $dtInicio);
		$viewModel->set("data_fim", $dtFim);
		$viewModel->set("conta_descricao", $Conta->getNome());
		$viewModel->set("id_conta", $idConta);
		$viewModel->set("arr_contas", $arrContas);
		$viewModel->set("arr_movimentacoes", $arr_movSaida);
                $viewModel->set("arr_totais", $arrTotais);
		$viewModel->set("btn_topo_id", "btn-nova-despesa");
		$viewModel->set("btn_topo_text", "NOVA DESPESA");
		$viewModel->set("icon_topo_name", "fa-money");
		$viewModel->set("icon_topo_text", "Despesas");
		$viewModel->set("tp_movimentacao", "Despesas");
		
		$this->view->output($viewModel, "");
	}
	
	protected function incluir(){
		$viewModel = new ViewModel();
		$viewModel = $this->model->incluir();
		
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
		
		$id_form = "frm-nova-receita";
		$id_button = "btn-post-nova-receita";
		$titulo_janela = $_POST["alert_tit"];
		$id_movimentacao = ""; 
		
		// combo contas
		$nome_combo_contas = "mov_con_id";
		$valor_combo_contas = "";
		$combo_contas = $this->model->pegaHtmlContas($Usuario, $nome_combo_contas, $valor_combo_contas);
		// ============
		
		// combo categoria
		$nome_combo_cat = "mov_cat";
		$valor_combo_cat = "";
		$combo_categoria = $this->model->pegaHtmlCat($Usuario, $nome_combo_cat, $valor_combo_cat);
		// ===============
		
		$combo_subcategoria = "	<select class='form-control' name='mov_subcat' id='mov_subcat'>
									<option value=''></option>
								</select>";
		
		$viewModel->set("id_form", $id_form);
		$viewModel->set("id_button", $id_button);
		$viewModel->set("id_movimentacao", $id_movimentacao);
		$viewModel->set("titulo_janela", $titulo_janela);
		$viewModel->set("mov_descricao", "");
		$viewModel->set("combo_contas", $combo_contas);
		$viewModel->set("combo_categoria", $combo_categoria);
		$viewModel->set("combo_subcategoria", $combo_subcategoria);
		
		$this->view->output($this->model->incluir(), "");
	}

	protected function incluirDespesa(){
		$viewModel = new ViewModel();
		$viewModel = $this->model->incluirDespesa();
		
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
		
		$id_form = "frm-nova-despesa";
		$id_button = "btn-post-nova-despesa";
		$titulo_janela = $_POST["alert_tit"];
		$id_movimentacao = "";
		
		// combo contas
		$nome_combo_contas = "mov_con_id";
		$valor_combo_contas = "";
		$combo_contas = $this->model->pegaHtmlContas($Usuario, $nome_combo_contas, $valor_combo_contas);
		// ============
		
		// combo categoria
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/MovimentacaoTipo.service.php";
		$MovimentacaoTipoServ = new MovimentacaoTipoService();
		$objRet = $MovimentacaoTipoServ->pegaDespesa();
		if($objRet->isErro()){
			die("Erro ao buscar despesas. Tente novamente!"); //@todo forcar erro e ver se msg aparece
		}
		$MovimentacaoTipo = $objRet->getRetByKey("ent");
		
		$nome_combo_cat = "mov_cat";
		$valor_combo_cat = "";
		$combo_categoria = $this->model->pegaHtmlCat($Usuario, $nome_combo_cat, $valor_combo_cat, $MovimentacaoTipo);
		// ===============
		
		$combo_subcategoria = "	<select class='form-control' name='mov_subcat' id='mov_subcat'>
									<option value=''></option>
								</select>";
		
		$viewModel->set("id_form", $id_form);
		$viewModel->set("id_button", $id_button);
		$viewModel->set("id_movimentacao", $id_movimentacao);
		$viewModel->set("titulo_janela", $titulo_janela);
		$viewModel->set("mov_descricao", "");
		$viewModel->set("combo_contas", $combo_contas);
		$viewModel->set("combo_categoria", $combo_categoria);
		$viewModel->set("combo_subcategoria", $combo_subcategoria);
		
		$this->view->output($this->model->incluirDespesa(), "");
	}
	
	protected function postNovaReceita(){
	//@todo esse metodo eh igual ao postNovaReceita; deixar 1 soh
		//===========================================================
		
		require_once $_SERVER ['BIRDS_HOME'] . "classes-general/Validation.class.php";
		require_once $_SERVER ['BIRDS_HOME'] . "classes-general/StringUtils.class.php";
		$Validation = new Validation();
		$StringUtils = new StringUtils();
		$MovimentacaoServ = new MovimentacaoService();
		
		// array resposta JSON
		$arrJSON = array();
		$arrJSON["retorno"] = ""; // OK, ERRO
		$arrJSON["msg"] = "";
		// ===================
		
		// variaveis
		$userId = $this->loggedUserId;
		$vConId = (isset($_POST["mov_con_id"])) ? $_POST["mov_con_id"]: "";
		$vMovDescricao = (isset($_POST["mov_descricao"])) ? $_POST["mov_descricao"]: "";
		$vIdCat = (isset($_POST["mov_cat"])) ? $_POST["mov_cat"]: "";
		$vIdSubCat = (isset($_POST["mov_subcat"])) ? $_POST["mov_subcat"]: "";
		$vDtVcto = (isset($_POST["mov_dt_vencimento"])) ? $StringUtils->strDateBrToBd($_POST["mov_dt_vencimento"]): "";
		$vValor = (isset($_POST["mov_valor"])) ? $StringUtils->strVlrBrToBd($_POST["mov_valor"]): "";
		$vDtPgto = (isset($_POST["mov_dt_pagamento"])) ? $StringUtils->strDateBrToBd($_POST["mov_dt_pagamento"]): NULL;
		$vValorPago = (isset($_POST["mov_valor_pago"])) ? $StringUtils->strVlrBrToBd($_POST["mov_valor_pago"]): NULL;
		$vRepetirPor = (isset($_POST["mov_repetir_por"]) && is_numeric($_POST["mov_repetir_por"])) ? $_POST["mov_repetir_por"]: 0;
		$vIdParcelado = ($vRepetirPor > 0) ? $MovimentacaoServ->pegaIdParcelado(): NULL;
		// =========
		
		// pega entidade Usuario
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/entities/Usuario.entity.php";
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/Usuario.service.php";
		$Usuario = new Usuario();
		$UsuarioServ = new UsuarioService();
		
		$objRet = $UsuarioServ->buscaPorId($userId);
		if($objRet->isOk()){
			$Usuario = $objRet->getRetByKey("ent");
		}
		// =====================
		
		// pega entidade Conta
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/entities/Conta.entity.php";
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/Conta.service.php";
		$Conta = new Conta();
		$ContaServ = new ContaService();
		
		$objRet = $ContaServ->buscaPorId($vConId);
		if($objRet->isOk()){
			$Conta = $objRet->getRetByKey("ent");
		}
		// ===================
		
		// pega entidade MovimentacaoCat
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/entities/MovimentacaoCat.entity.php";
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/MovimentacaoCat.service.php";
		$MovimentacaoCat = new MovimentacaoCat();
		$MovimentacaoCatServ = new MovimentacaoCatService();
		
		$id_cat_busca = (is_numeric($vIdSubCat)) ? $vIdSubCat: $vIdCat;
		$objRet = $MovimentacaoCatServ->buscaPorId($id_cat_busca);
		if($objRet->isOk()){
			$MovimentacaoCat = $objRet->getRetByKey("ent");
		}
		// =============================
		
		// cria Movimentacao para inserir
		$V_parcela = 0;
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
			
			$Movimentacao = new Movimentacao();
			$Movimentacao->setCategoria($MovimentacaoCat);
			$Movimentacao->setConta($Conta);
			$Movimentacao->setDeletado("f");
			$Movimentacao->setDescricao($vMovDescricao);
			$Movimentacao->setDtCompetencia($vDtVcto);
			$Movimentacao->setDtPagamento($vDtPgto);
			$Movimentacao->setDtVencimento($vDtVcto);
			$Movimentacao->setIdParcelado($vIdParcelado);
			$Movimentacao->setParcela($V_parcela);
			// $Movimentacao->setObservacao($observacao);
			// $Movimentacao->setProjeto();
			$Movimentacao->setUsuario($Usuario);
			$Movimentacao->setValor($vValor);
			$Movimentacao->setValorPago($vValorPago);
			
			$objResp = $MovimentacaoServ->insere($Movimentacao);
		}
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

	protected function postNovaDespesa(){
		//@todo esse metodo eh igual ao postNovaReceita; deixar 1 soh
		//===========================================================
		
		require_once $_SERVER ['BIRDS_HOME'] . "classes-general/Validation.class.php";
		require_once $_SERVER ['BIRDS_HOME'] . "classes-general/StringUtils.class.php";
		$Validation = new Validation();
		$StringUtils = new StringUtils();
		$MovimentacaoServ = new MovimentacaoService();
		
		// array resposta JSON
		$arrJSON = array();
		$arrJSON["retorno"] = ""; // OK, ERRO
		$arrJSON["msg"] = "";
		// ===================
		
		// variaveis
		$userId = $this->loggedUserId;
		$vConId = (isset($_POST["mov_con_id"])) ? $_POST["mov_con_id"]: "";
		$vMovDescricao = (isset($_POST["mov_descricao"])) ? $_POST["mov_descricao"]: "";
		$vIdCat = (isset($_POST["mov_cat"])) ? $_POST["mov_cat"]: "";
		$vIdSubCat = (isset($_POST["mov_subcat"])) ? $_POST["mov_subcat"]: "";
		$vDtVcto = (isset($_POST["mov_dt_vencimento"])) ? $StringUtils->strDateBrToBd($_POST["mov_dt_vencimento"]): "";
		$vValor = (isset($_POST["mov_valor"])) ? $StringUtils->strVlrBrToBd($_POST["mov_valor"]): "";
		$vDtPgto = (isset($_POST["mov_dt_pagamento"])) ? $StringUtils->strDateBrToBd($_POST["mov_dt_pagamento"]): NULL;
		$vValorPago = (isset($_POST["mov_valor_pago"])) ? $StringUtils->strVlrBrToBd($_POST["mov_valor_pago"]): NULL;
		$vRepetirPor = (isset($_POST["mov_repetir_por"]) && is_numeric($_POST["mov_repetir_por"])) ? $_POST["mov_repetir_por"]: 0;
		$vIdParcelado = ($vRepetirPor > 0) ? $MovimentacaoServ->pegaIdParcelado(): NULL;
		// =========
		
		// pega entidade Usuario
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/entities/Usuario.entity.php";
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/Usuario.service.php";
		$Usuario = new Usuario();
		$UsuarioServ = new UsuarioService();
		
		$objRet = $UsuarioServ->buscaPorId($userId);
		if($objRet->isOk()){
			$Usuario = $objRet->getRetByKey("ent");
		}
		// =====================
		
		// pega entidade Conta
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/entities/Conta.entity.php";
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/Conta.service.php";
		$Conta = new Conta();
		$ContaServ = new ContaService();
		
		$objRet = $ContaServ->buscaPorId($vConId);
		if($objRet->isOk()){
			$Conta = $objRet->getRetByKey("ent");
		}
		// ===================
		
		// pega entidade MovimentacaoCat
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/entities/MovimentacaoCat.entity.php";
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/MovimentacaoCat.service.php";
		$MovimentacaoCat = new MovimentacaoCat();
		$MovimentacaoCatServ = new MovimentacaoCatService();
		
		$id_cat_busca = (is_numeric($vIdSubCat)) ? $vIdSubCat: $vIdCat;
		$objRet = $MovimentacaoCatServ->buscaPorId($id_cat_busca);
		if($objRet->isOk()){
			$MovimentacaoCat = $objRet->getRetByKey("ent");
		}
		// =============================
		
		// cria Movimentacao para inserir
		$V_parcela = 0;
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
			
			$Movimentacao = new Movimentacao();
			$Movimentacao->setCategoria($MovimentacaoCat);
			$Movimentacao->setConta($Conta);
			$Movimentacao->setDeletado("f");
			$Movimentacao->setDescricao($vMovDescricao);
			$Movimentacao->setDtCompetencia($vDtVcto);
			$Movimentacao->setDtPagamento($vDtPgto);
			$Movimentacao->setDtVencimento($vDtVcto);
			$Movimentacao->setIdParcelado($vIdParcelado);
			$Movimentacao->setParcela($V_parcela);
			// $Movimentacao->setObservacao($observacao);
			// $Movimentacao->setProjeto();
			$Movimentacao->setUsuario($Usuario);
			$Movimentacao->setValor($vValor);
			$Movimentacao->setValorPago($vValorPago);
			
			$objResp = $MovimentacaoServ->insere($Movimentacao);
		}
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
		$viewModel = $this->model->editar();
		
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
		
		$id_form = "frm-editar-receita";
		$id_button = "btn-editar-nova-receita";
		$id_movimentacao = $_POST["id"];
		$titulo_janela = $_POST["alert_tit"] . " #" . $id_movimentacao;
		
		// busca a entidade da receita ====
		$MovimentacaoServ = new MovimentacaoService();
		$objResp = $MovimentacaoServ->buscaPorId($id_movimentacao);
		if($objResp->isErro()){
			echo "Erro ao editar a Receita! Desc.:" . $objResp->getRetByKey("msg"); //@todo forcar erro pra ver se msg aparece
			return;
		}
		
		$Movimentacao = new Movimentacao();
		$Movimentacao = $objResp->getRetByKey("ent");
		// ================================
		
		// combo contas
		$nome_combo_contas = "mov_con_id";
		$valor_combo_contas = $Movimentacao->getConta()->getId();
		$combo_contas = $this->model->pegaHtmlContas($Usuario, $nome_combo_contas, $valor_combo_contas);
		// ============
		
		// combo categoria
		list($V_MC_ID_PAI, $V_MC_ID_FILHO) = $this->model->pegaCatSubcat($Movimentacao->getCategoria());
		
		$nome_combo_cat = "mov_cat";
		$valor_combo_cat = $V_MC_ID_PAI;
		$combo_categoria = $this->model->pegaHtmlCat($Usuario, $nome_combo_cat, $valor_combo_cat);
		// ===============
		
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
		
		$V_Vencimento = ($Movimentacao->getDtVencimento() != "") ? date("d/m/Y", strtotime($Movimentacao->getDtVencimento())): "";
		$V_Valor = ($Movimentacao->getValor() != "") ? $Movimentacao->getValor(): "";
		$V_Pagamento = ($Movimentacao->getDtPagamento() != "") ? date("d/m/Y", strtotime($Movimentacao->getDtPagamento())): "";
		$V_ValorPago = ($Movimentacao->getValorPago() != "") ? $Movimentacao->getValorPago(): "";
		
		$V_Parcela = $Movimentacao->getParcela();
		$V_QtdeParcelas = $Movimentacao->getQtdeParcelas();
		$V_StrParcela = (is_numeric($V_Parcela) && is_numeric($V_QtdeParcelas)) ? "$V_Parcela/$V_QtdeParcelas": "";
		
		$viewModel->set("id_form", $id_form);
		$viewModel->set("id_button", $id_button);
		$viewModel->set("id_movimentacao", $id_movimentacao);
		$viewModel->set("titulo_janela", $titulo_janela);
		$viewModel->set("mov_descricao", $Movimentacao->getDescricao());
		$viewModel->set("combo_contas", $combo_contas);
		$viewModel->set("combo_categoria", $combo_categoria);
		$viewModel->set("combo_subcategoria", $combo_subcategoria);
		$viewModel->set("mov_dt_vencimento", $V_Vencimento);
		$viewModel->set("mov_valor", $V_Valor);
		$viewModel->set("mov_dt_pagamento", $V_Pagamento);
		$viewModel->set("mov_valor_pago", $V_ValorPago);
		$viewModel->set("str_parcela", $V_StrParcela);
		
		$this->view->output($this->model->editar(), "");
	}

	protected function editarDespesa(){
		$viewModel = new ViewModel();
		$viewModel = $this->model->editar();
		
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
		
		$id_form = "frm-editar-despesa";
		$id_button = "btn-editar-nova-despesa";
		$id_movimentacao = $_POST["id"];
		$titulo_janela = $_POST["alert_tit"] . " #" . $id_movimentacao;
		
		// busca a entidade da receita ====
		$MovimentacaoServ = new MovimentacaoService();
		$objResp = $MovimentacaoServ->buscaPorId($id_movimentacao);
		if($objResp->isErro()){
			echo "Erro ao editar a Receita! Desc.:" . $objResp->getRetByKey("msg"); //@todo forcar erro pra ver se msg aparece
			return;
		}
		
		$Movimentacao = new Movimentacao();
		$Movimentacao = $objResp->getRetByKey("ent");
		// ================================
		
		// combo contas
		$nome_combo_contas = "mov_con_id";
		$valor_combo_contas = $Movimentacao->getConta()->getId();
		$combo_contas = $this->model->pegaHtmlContas($Usuario, $nome_combo_contas, $valor_combo_contas);
		// ============
		
		// combo categoria
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/MovimentacaoTipo.service.php";
		$MovimentacaoTipoServ = new MovimentacaoTipoService();
		$objRet = $MovimentacaoTipoServ->pegaDespesa();
		if($objRet->isErro()){
			die("Erro ao buscar despesas. Tente novamente!"); //@todo forcar erro e ver se msg aparece
		}
		$MovimentacaoTipo = $objRet->getRetByKey("ent");
		
		list($V_MC_ID_PAI, $V_MC_ID_FILHO) = $this->model->pegaCatSubcat($Movimentacao->getCategoria());
		
		$nome_combo_cat = "mov_cat";
		$valor_combo_cat = $V_MC_ID_PAI;
		$combo_categoria = $this->model->pegaHtmlCat($Usuario, $nome_combo_cat, $valor_combo_cat, $MovimentacaoTipo);
		// ===============
		
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
		
		$V_Vencimento = ($Movimentacao->getDtVencimento() != "") ? date("d/m/Y", strtotime($Movimentacao->getDtVencimento())): "";
		$V_Valor = ($Movimentacao->getValor() != "") ? $Movimentacao->getValor(): "";
		$V_Pagamento = ($Movimentacao->getDtPagamento() != "") ? date("d/m/Y", strtotime($Movimentacao->getDtPagamento())): "";
		$V_ValorPago = ($Movimentacao->getValorPago() != "") ? $Movimentacao->getValorPago(): "";
		
		$V_Parcela = $Movimentacao->getParcela();
		$V_QtdeParcelas = $Movimentacao->getQtdeParcelas();
		$V_StrParcela = (is_numeric($V_Parcela) && is_numeric($V_QtdeParcelas)) ? "$V_Parcela/$V_QtdeParcelas": "";
		
		$viewModel->set("id_form", $id_form);
		$viewModel->set("id_button", $id_button);
		$viewModel->set("id_movimentacao", $id_movimentacao);
		$viewModel->set("titulo_janela", $titulo_janela);
		$viewModel->set("mov_descricao", $Movimentacao->getDescricao());
		$viewModel->set("combo_contas", $combo_contas);
		$viewModel->set("combo_categoria", $combo_categoria);
		$viewModel->set("combo_subcategoria", $combo_subcategoria);
		$viewModel->set("mov_dt_vencimento", $V_Vencimento);
		$viewModel->set("mov_valor", $V_Valor);
		$viewModel->set("mov_dt_pagamento", $V_Pagamento);
		$viewModel->set("mov_valor_pago", $V_ValorPago);
		$viewModel->set("str_parcela", $V_StrParcela);
		
		$this->view->output($this->model->editar(), "");
	}
	
	protected function postEditarReceita(){
		require_once $_SERVER ['BIRDS_HOME'] . "classes-general/StringUtils.class.php";
		$StringUtils = new StringUtils();
		
		// array resposta JSON
		$arrJSON = array();
		$arrJSON["retorno"] = ""; // OK, ERRO
		$arrJSON["msg"] = "";
		// ===================
		
		// variaveis
		$vMovId = (isset($_POST["hddn-id-movimentacao"])) ? $_POST["hddn-id-movimentacao"]: "";
		$userId = $this->loggedUserId;
		$vConId = (isset($_POST["mov_con_id"])) ? $_POST["mov_con_id"]: "";
		$vMovDescricao = (isset($_POST["mov_descricao"])) ? $_POST["mov_descricao"]: "";
		$vIdCat = (isset($_POST["mov_cat"])) ? $_POST["mov_cat"]: "";
		$vIdSubCat = (isset($_POST["mov_subcat"])) ? $_POST["mov_subcat"]: "";
		$vDtVcto = (isset($_POST["mov_dt_vencimento"])) ? $StringUtils->strDateBrToBd($_POST["mov_dt_vencimento"]): "";
		$vValor = (isset($_POST["mov_valor"])) ? $StringUtils->strVlrBrToBd($_POST["mov_valor"]): "";
		$vDtPgto = (isset($_POST["mov_dt_pagamento"])) ? $StringUtils->strDateBrToBd($_POST["mov_dt_pagamento"]): NULL;
		$vValorPago = (isset($_POST["mov_valor_pago"])) ? $StringUtils->strVlrBrToBd($_POST["mov_valor_pago"]): NULL;
		// =========
		
		// pega entidade Usuario
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/entities/Usuario.entity.php";
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/Usuario.service.php";
		$Usuario = new Usuario();
		$UsuarioServ = new UsuarioService();
		
		$objRet = $UsuarioServ->buscaPorId($userId);
		if($objRet->isOk()){
			$Usuario = $objRet->getRetByKey("ent");
		}
		// =====================
		
		// pega entidade Conta
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/entities/Conta.entity.php";
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/Conta.service.php";
		$Conta = new Conta();
		$ContaServ = new ContaService();
		
		$objRet = $ContaServ->buscaPorId($vConId);
		if($objRet->isOk()){
			$Conta = $objRet->getRetByKey("ent");
		}
		// ===================
		
		// pega entidade MovimentacaoCat
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/entities/MovimentacaoCat.entity.php";
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/MovimentacaoCat.service.php";
		$MovimentacaoCat = new MovimentacaoCat();
		$MovimentacaoCatServ = new MovimentacaoCatService();
		
		$id_cat_busca = (is_numeric($vIdSubCat)) ? $vIdSubCat: $vIdCat;
		$objRet = $MovimentacaoCatServ->buscaPorId($id_cat_busca);
		if($objRet->isOk()){
			$MovimentacaoCat = $objRet->getRetByKey("ent");
		}
		// =============================
		
		// cria Movimentacao para inserir
		$MovimentacaoServ = new MovimentacaoService();
		$Movimentacao = new Movimentacao();
		
		$objResp = $MovimentacaoServ->buscaPorId($vMovId);
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = $objResp->getRetByKey("msg");
			
			echo json_encode($arrJSON);
			return;
		}
		$Movimentacao = $objResp->getRetByKey("ent");
		
		$Movimentacao->setCategoria($MovimentacaoCat);
		$Movimentacao->setConta($Conta);
		// $Movimentacao->setDeletado("f");
		$Movimentacao->setDescricao($vMovDescricao);
		$Movimentacao->setDtCompetencia($vDtVcto);
		$Movimentacao->setDtPagamento($vDtPgto);
		$Movimentacao->setDtVencimento($vDtVcto);
		// $Movimentacao->setIdParcelado($id_parcelado);
		// $Movimentacao->setObservacao($observacao);
		// $Movimentacao->setParcela($parcela);
		// $Movimentacao->setProjeto();
		$Movimentacao->setUsuario($Usuario);
		$Movimentacao->setValor($vValor);
		$Movimentacao->setValorPago($vValorPago);
		
		$objResp = $MovimentacaoServ->edita($Movimentacao);
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
	
	protected function postEditarDespesa(){
		require_once $_SERVER ['BIRDS_HOME'] . "classes-general/StringUtils.class.php";
		$StringUtils = new StringUtils();
		
		// array resposta JSON
		$arrJSON = array();
		$arrJSON["retorno"] = ""; // OK, ERRO
		$arrJSON["msg"] = "";
		// ===================
		
		// variaveis
		$vMovId = (isset($_POST["hddn-id-movimentacao"])) ? $_POST["hddn-id-movimentacao"]: "";
		$userId = $this->loggedUserId;
		$vConId = (isset($_POST["mov_con_id"])) ? $_POST["mov_con_id"]: "";
		$vMovDescricao = (isset($_POST["mov_descricao"])) ? $_POST["mov_descricao"]: "";
		$vIdCat = (isset($_POST["mov_cat"])) ? $_POST["mov_cat"]: "";
		$vIdSubCat = (isset($_POST["mov_subcat"])) ? $_POST["mov_subcat"]: "";
		$vDtVcto = (isset($_POST["mov_dt_vencimento"])) ? $StringUtils->strDateBrToBd($_POST["mov_dt_vencimento"]): "";
		$vValor = (isset($_POST["mov_valor"])) ? $StringUtils->strVlrBrToBd($_POST["mov_valor"]): "";
		$vDtPgto = (isset($_POST["mov_dt_pagamento"])) ? $StringUtils->strDateBrToBd($_POST["mov_dt_pagamento"]): NULL;
		$vValorPago = (isset($_POST["mov_valor_pago"])) ? $StringUtils->strVlrBrToBd($_POST["mov_valor_pago"]): NULL;
		// =========
		
		// pega entidade Usuario
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/entities/Usuario.entity.php";
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/Usuario.service.php";
		$Usuario = new Usuario();
		$UsuarioServ = new UsuarioService();
		
		$objRet = $UsuarioServ->buscaPorId($userId);
		if($objRet->isOk()){
			$Usuario = $objRet->getRetByKey("ent");
		}
		// =====================
		
		// pega entidade Conta
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/entities/Conta.entity.php";
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/Conta.service.php";
		$Conta = new Conta();
		$ContaServ = new ContaService();
		
		$objRet = $ContaServ->buscaPorId($vConId);
		if($objRet->isOk()){
			$Conta = $objRet->getRetByKey("ent");
		}
		// ===================
		
		// pega entidade MovimentacaoCat
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/entities/MovimentacaoCat.entity.php";
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/MovimentacaoCat.service.php";
		$MovimentacaoCat = new MovimentacaoCat();
		$MovimentacaoCatServ = new MovimentacaoCatService();
		
		$id_cat_busca = (is_numeric($vIdSubCat)) ? $vIdSubCat: $vIdCat;
		$objRet = $MovimentacaoCatServ->buscaPorId($id_cat_busca);
		if($objRet->isOk()){
			$MovimentacaoCat = $objRet->getRetByKey("ent");
		}
		// =============================
		
		// cria Movimentacao para inserir
		$MovimentacaoServ = new MovimentacaoService();
		$Movimentacao = new Movimentacao();
		
		$objResp = $MovimentacaoServ->buscaPorId($vMovId);
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = $objResp->getRetByKey("msg");
				
			echo json_encode($arrJSON);
			return;
		}
		$Movimentacao = $objResp->getRetByKey("ent");
		
		$Movimentacao->setCategoria($MovimentacaoCat);
		$Movimentacao->setConta($Conta);
		// $Movimentacao->setDeletado("f");
		$Movimentacao->setDescricao($vMovDescricao);
		$Movimentacao->setDtCompetencia($vDtVcto);
		$Movimentacao->setDtPagamento($vDtPgto);
		$Movimentacao->setDtVencimento($vDtVcto);
		// $Movimentacao->setIdParcelado($id_parcelado);
		// $Movimentacao->setObservacao($observacao);
		// $Movimentacao->setParcela($parcela);
		// $Movimentacao->setProjeto();
		$Movimentacao->setUsuario($Usuario);
		$Movimentacao->setValor($vValor);
		$Movimentacao->setValorPago($vValorPago);
		
		$objResp = $MovimentacaoServ->edita($Movimentacao);
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
		$vMovId = (isset($_POST["id"])) ? $_POST["id"]: "";
		// =========
		
		// busca Conta para editar
		$Movimentacao = new Movimentacao();
		$MovimentacaoServ = new MovimentacaoService();
		
		$objResp = $MovimentacaoServ->buscaPorId($vMovId);
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao buscar Receita. Motivo: " . $objResp->getRetByKey("msg");
		
			echo json_encode($arrJSON);
			return;
		}
		
		$Movimentacao = $objResp->getRetByKey("ent");
		$Movimentacao->setDeletado(TRUE);
		
		if( $Movimentacao->getUsuario()->getId() != $this->loggedUserId ){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = "Erro ao deletar Receita. Motivo: usu&aacute;rio diferente.";
				
			echo json_encode($arrJSON);
			return;
		}
		
		$objResp = $MovimentacaoServ->edita($Movimentacao);
		// =======================
		
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = $objResp->getRetByKey("msg");
				
			echo json_encode($arrJSON);
			return;
		}
		else{
			$arrJSON["retorno"] = "OK"; // OK, ERRO
			$arrJSON["msg"] = "Entrada deletada com sucesso!";
				
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
		
		$mov_id = $_POST["mov_id"];
		$titulo_janela = "Exibi&ccedil;&atilde;o do Parcelamento";
		
		// busca a entidade Movimentacao ================
		$Movimentacao = new Movimentacao();
		$MovimentacaoServ = new MovimentacaoService();
		$objRet = $MovimentacaoServ->buscaPorId($mov_id);
		if($objRet->isErro()){
			$erro_msg = "Erro ao buscar parcelamento. Desc.:" . $objRet->getRetByKey("msg");
		}
		else{
			$Movimentacao = $objRet->getRetByKey("ent");
		}
		// ==============================================
		
		// pega as movimentacoes baseadas no parcelamento
		$V_id_parcelado = $MovimentacaoServ->pegaIdParceladoPorMovimentacao($Movimentacao);
		$V_id_parcelado = (is_numeric($V_id_parcelado)) ? $V_id_parcelado: -1;
		$V_arr_parcelamento = array();
		
		$objRet = $MovimentacaoServ->pegaMovimentacoesParcelamento($Usuario, $V_id_parcelado);
		if($objRet->isErro()){
			$erro_msg = "Erro ao buscar movimenta&ccedil;&otilde;es do parcelamento.";
		}
		else{
			$V_arr_parcelamento = $objRet->getRetByKey("arr_movParcelado");
		}
		// ==============================================
		
		$V_ID_MOV_TIPO = $Movimentacao->getCategoria()->getMovimentacaoTipo()->getId(); //1 Receita; 2 Despesa
		$V_STR_MOVIMENTACAO = ($V_ID_MOV_TIPO == 1) ? "receita": "despesa";
		
		$viewModel->set("erro_msg", $erro_msg);
		$viewModel->set("titulo_janela", $titulo_janela);
		$viewModel->set("arr_movimentacoes", $V_arr_parcelamento);
		$viewModel->set("str_movimentacao", $V_STR_MOVIMENTACAO);
		
		$this->view->output($this->model->mostraParcelas(), "");
	}

	protected function editarCompetencia(){
		$viewModel = new ViewModel();
		$viewModel = $this->model->editarCompetencia();
		
		$id_movimentacao = $_POST["id"];
		$str_movimentacao = $_POST["str_movimentacao"];
		$titulo_janela = "Editar Compet&ecirc;ncia #" . $id_movimentacao;
		
		// busca a entidade da receita ====
		$MovimentacaoServ = new MovimentacaoService();
		$objResp = $MovimentacaoServ->buscaPorId($id_movimentacao);
		if($objResp->isErro()){
			echo "Erro ao editar a Compet&ecirc;ncia! Desc.:" . $objResp->getRetByKey("msg"); //@todo forcar erro pra ver se msg aparece
			return;
		}
		
		$Movimentacao = new Movimentacao();
		$Movimentacao = $objResp->getRetByKey("ent");
		// ================================
		
		$viewModel->set("id_movimentacao", $id_movimentacao);
		$viewModel->set("titulo_janela", $titulo_janela);
		$viewModel->set("str_movimentacao", $str_movimentacao);
		$viewModel->set("str_conta", $Movimentacao->getConta()->getNome());
		$viewModel->set("str_categoria", $Movimentacao->getCategoria()->getDescricao());
		$viewModel->set("str_descricao", $Movimentacao->getDescricao());
		$viewModel->set("str_competencia", $Movimentacao->getDtCompetencia());
		$viewModel->set("str_vencimento", $Movimentacao->getDtVencimento());
		$viewModel->set("str_valor", $Movimentacao->getValor());
		
		$this->view->output($this->model->editarCompetencia(), "");
	}
	
	protected function postEditarCompetencia(){
		require_once $_SERVER ['BIRDS_HOME'] . "classes-general/StringUtils.class.php";
		$StringUtils = new StringUtils();
		
		// array resposta JSON
		$arrJSON = array();
		$arrJSON["retorno"] = ""; // OK, ERRO
		$arrJSON["msg"] = "";
		// ===================
		
		// variaveis
		$userId = $this->loggedUserId;
		$vMovId = (isset($_POST["hddn-id-movimentacao"])) ? $_POST["hddn-id-movimentacao"]: "";
		$vDtCompetencia = (isset($_POST["emc_competencia"])) ? $StringUtils->strDateBrToBd($_POST["emc_competencia"]): NULL;
		// =========
		
		// pega entidade Usuario
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/entities/Usuario.entity.php";
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/Usuario.service.php";
		$Usuario = new Usuario();
		$UsuarioServ = new UsuarioService();
		
		$objRet = $UsuarioServ->buscaPorId($userId);
		if($objRet->isOk()){
			$Usuario = $objRet->getRetByKey("ent");
		}
		// =====================
		
		// cria Movimentacao para inserir
		$MovimentacaoServ = new MovimentacaoService();
		$Movimentacao = new Movimentacao();
		
		$objResp = $MovimentacaoServ->buscaPorId($vMovId);
		if($objResp->isErro()){
			$arrJSON["retorno"] = "ERRO"; // OK, ERRO
			$arrJSON["msg"] = $objResp->getRetByKey("msg");
		
			echo json_encode($arrJSON);
			return;
		}
		$Movimentacao = $objResp->getRetByKey("ent");
		
		$Movimentacao->setDtCompetencia($vDtCompetencia);
		$objResp = $MovimentacaoServ->edita($Movimentacao);
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

	protected function indexTransferencias(){
		require_once $_SERVER ['BIRDS_HOME'] . "classes-general/StringUtils.class.php";
		$StringUtils = new StringUtils();
		
		// variaveis
		$userId = $this->loggedUserId;
		$erro_msg = "";
		$idConta = (isset($_POST["idConta"]) && is_numeric($_POST["idConta"])) ? $_POST["idConta"]: "";
		$dtInicio = (isset($_POST["dtInicio"]) && strlen($_POST["dtInicio"]) == 10) ? $StringUtils->strDateBrToBd($_POST["dtInicio"]): date('Y-m-01');
		$dtFim = (isset($_POST["dtFim"]) && strlen($_POST["dtFim"]) == 10) ? $StringUtils->strDateBrToBd($_POST["dtFim"]): date('Y-m-t');
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
		
		// pega contas do usuario ============
		list($Conta, $arrContas) = $this->model->pegaContaUsuario($Usuario, $idConta);
		// ===================================
		
		$MovimentacaoServ = new MovimentacaoService();
		$objRet = $MovimentacaoServ->pegaTransferencias($Usuario, $Conta);
		$arrTransf = ($objRet->isOk()) ? $objRet->getRetByKey("arr_transferencias"): array();

                // cria array totalizador ========
                $arrTotais = $this->model->criaArrTotais($arrTransf);
                // ===============================
		
		$viewModel = new ViewModel();
		$viewModel = $this->model->indexTransferencias();
		
		$viewModel->set("erro_msg", $erro_msg);
		$viewModel->set("arr_movimentacoes", $arrTransf);
                $viewModel->set("arr_totais", $arrTotais);
		$viewModel->set("conta_descricao", $Conta->getNome());
		$viewModel->set("arr_contas", $arrContas);
		$viewModel->set("data_inicio", $dtInicio);
		$viewModel->set("data_fim", $dtFim);
		$viewModel->set("id_conta", $idConta);
		
		$this->view->output($viewModel, "");
	}

	protected function incluirTransferencia(){
		$viewModel = new ViewModel();
		$viewModel = $this->model->incluirTransferencia();
		
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
		
		$id_form = "frm-nova-transferencia";
		$id_button = "btn-post-nova-transferencia";
		$titulo_janela = $_POST["alert_tit"];
		$id_movimentacao = "";
		$vencimento = "";
		$valor = "";
		
		// combo contas
		$nome_combo_contas_orig = "mov_con_id_orig";
		$valor_combo_contas_orig = "";
		$combo_contas_orig = $this->model->pegaHtmlContas($Usuario, $nome_combo_contas_orig, $valor_combo_contas_orig);
		
		$nome_combo_contas_dest = "mov_con_id_dest";
		$valor_combo_contas_dest = "";
		$combo_contas_dest = $this->model->pegaHtmlContas($Usuario, $nome_combo_contas_dest, $valor_combo_contas_dest);
		// ============
		
		$viewModel->set("id_form", $id_form);
		$viewModel->set("id_button", $id_button);
		$viewModel->set("id_movimentacao", $id_movimentacao);
		$viewModel->set("titulo_janela", $titulo_janela);
		$viewModel->set("combo_contas_orig", $combo_contas_orig);
		$viewModel->set("combo_contas_dest", $combo_contas_dest);
		$viewModel->set("vencimento", $vencimento);
		$viewModel->set("valor", $valor);
		
		$this->view->output($this->model->incluirTransferencia(), "");
	}

	protected function postNovaTransferencia(){
		require_once $_SERVER ['BIRDS_HOME'] . "classes-general/Validation.class.php";
		require_once $_SERVER ['BIRDS_HOME'] . "classes-general/StringUtils.class.php";
		$Validation = new Validation();
		$StringUtils = new StringUtils();
		$MovimentacaoServ = new MovimentacaoService();
		
		// array resposta JSON
		$arrJSON = array();
		$arrJSON["retorno"] = ""; // OK, ERRO
		$arrJSON["msg"] = "";
		// ===================
		
		// variaveis
		$userId = $this->loggedUserId;
		$vIdMovimentacao = (isset($_POST["hddn-id-movimentacao"])) ? $_POST["hddn-id-movimentacao"]: "";
		$vIdContaOrigem = (isset($_POST["mov_con_id_orig"])) ? $_POST["mov_con_id_orig"]: "";
		$vIdContaDestino = (isset($_POST["mov_con_id_dest"])) ? $_POST["mov_con_id_dest"]: "";
		$vDtTransf = (isset($_POST["mov_dt_vencimento"])) ? $StringUtils->strDateBrToBd($_POST["mov_dt_vencimento"]): NULL;
		$vVlrTranf = (isset($_POST["mov_valor"])) ? $StringUtils->strVlrBrToBd($_POST["mov_valor"]): "";
		// =========
		
		// pega entidade Usuario
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/entities/Usuario.entity.php";
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/Usuario.service.php";
		$Usuario = new Usuario();
		$UsuarioServ = new UsuarioService();
		
		$objRet = $UsuarioServ->buscaPorId($userId);
		if($objRet->isOk()){
			$Usuario = $objRet->getRetByKey("ent");
		}
		// =====================
		
		// pega entidade Conta
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/entities/Conta.entity.php";
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/Conta.service.php";
		$ContaOrig = new Conta();
		$ContaDest = new Conta();
		$ContaServ = new ContaService();
		
		$objRet = $ContaServ->buscaPorId($vIdContaOrigem);
		if($objRet->isOk()){
			$ContaOrig = $objRet->getRetByKey("ent");
		}
		
		$objRet = $ContaServ->buscaPorId($vIdContaDestino);
		if($objRet->isOk()){
			$ContaDest = $objRet->getRetByKey("ent");
		}
		// ===================
		
		// insere transferencia ==
		$objResp = $MovimentacaoServ->insereTransferencia($Usuario, $ContaOrig, $ContaDest, $vDtTransf, $vVlrTranf);
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

	protected function editarTransferencia(){
		$MovimentacaoServ = new MovimentacaoService();
		$viewModel = new ViewModel();
		$viewModel = $this->model->editarTransferencia();
		
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
		
		$id_form = "frm-edita-transferencia";
		$id_button = "btn-post-edita-transferencia";
		$titulo_janela = $_POST["alert_tit"];
		$id_movimentacao = $_POST["id"];
		
		// busca a entidade movimentacao - mov_id
		$MovimentacaoOrig = new Movimentacao();
		
		$objRet = $MovimentacaoServ->buscaPorId($id_movimentacao);
		if($objRet->isErro()){
			echo "Erro ao editar a Transfer&ecirc;ncia! Desc.:" . $objRet->getRetByKey("msg"); //@todo forcar erro pra ver se msg aparece
			return;
		}
		$MovimentacaoOrig = $objRet->getRetByKey("ent");
		// ======================================
		
		$vencimento = $MovimentacaoOrig->getDtCompetencia();
		$valor = $MovimentacaoOrig->getValorPago();
		
		// busca a entidade movimentacao - mov_transferencia_id
		$MovimentacaoDest = new Movimentacao();
		
		$objRet = $MovimentacaoServ->buscaPorId( $MovimentacaoOrig->getTransferenciaId() );
		if($objRet->isErro()){
			echo "Erro ao editar a Transfer&ecirc;ncia! Desc.:" . $objRet->getRetByKey("msg"); //@todo forcar erro pra ver se msg aparece
			return;
		}
		$MovimentacaoDest = $objRet->getRetByKey("ent");
		// ====================================================
		
		// combo contas
		$nome_combo_contas_orig = "mov_con_id_orig";
		$valor_combo_contas_orig = $MovimentacaoOrig->getConta()->getId();
		$combo_contas_orig = $this->model->pegaHtmlContas($Usuario, $nome_combo_contas_orig, $valor_combo_contas_orig);
		
		$nome_combo_contas_dest = "mov_con_id_dest";
		$valor_combo_contas_dest = $MovimentacaoDest->getConta()->getId();
		$combo_contas_dest = $this->model->pegaHtmlContas($Usuario, $nome_combo_contas_dest, $valor_combo_contas_dest);
		// ============
		
		$viewModel->set("id_form", $id_form);
		$viewModel->set("id_button", $id_button);
		$viewModel->set("id_movimentacao", $id_movimentacao);
		$viewModel->set("titulo_janela", $titulo_janela);
		$viewModel->set("combo_contas_orig", $combo_contas_orig);
		$viewModel->set("combo_contas_dest", $combo_contas_dest);
		$viewModel->set("vencimento", date("d/m/Y", strtotime($vencimento)));
		$viewModel->set("valor", $valor);
		
		$this->view->output($this->model->editarTransferencia(), "");
	}

	protected function postEditarTransferencia(){
	require_once $_SERVER ['BIRDS_HOME'] . "classes-general/Validation.class.php";
		require_once $_SERVER ['BIRDS_HOME'] . "classes-general/StringUtils.class.php";
		$Validation = new Validation();
		$StringUtils = new StringUtils();
		$MovimentacaoServ = new MovimentacaoService();
		
		// array resposta JSON
		$arrJSON = array();
		$arrJSON["retorno"] = ""; // OK, ERRO
		$arrJSON["msg"] = "";
		// ===================
		
		// variaveis
		$userId = $this->loggedUserId;
		$vIdMovimentacao = (isset($_POST["hddn-id-movimentacao"])) ? $_POST["hddn-id-movimentacao"]: "";
		$vIdContaOrigem = (isset($_POST["mov_con_id_orig"])) ? $_POST["mov_con_id_orig"]: "";
		$vIdContaDestino = (isset($_POST["mov_con_id_dest"])) ? $_POST["mov_con_id_dest"]: "";
		$vDtTransf = (isset($_POST["mov_dt_vencimento"])) ? $StringUtils->strDateBrToBd($_POST["mov_dt_vencimento"]): NULL;
		$vVlrTranf = (isset($_POST["mov_valor"])) ? $StringUtils->strVlrBrToBd($_POST["mov_valor"]): "";
		// =========
		
		// pega entidade Usuario
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/entities/Usuario.entity.php";
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/Usuario.service.php";
		$Usuario = new Usuario();
		$UsuarioServ = new UsuarioService();
		
		$objRet = $UsuarioServ->buscaPorId($userId);
		if($objRet->isOk()){
			$Usuario = $objRet->getRetByKey("ent");
		}
		// =====================
		
		// pega entidade Conta
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/entities/Conta.entity.php";
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/Conta.service.php";
		$ContaOrig = new Conta();
		$ContaDest = new Conta();
		$ContaServ = new ContaService();
		
		$objRet = $ContaServ->buscaPorId($vIdContaOrigem);
		if($objRet->isOk()){
			$ContaOrig = $objRet->getRetByKey("ent");
		}
		
		$objRet = $ContaServ->buscaPorId($vIdContaDestino);
		if($objRet->isOk()){
			$ContaDest = $objRet->getRetByKey("ent");
		}
		// ===================
		
		// altera transferencia ==
		$objResp = $MovimentacaoServ->editaTransferencia($vIdMovimentacao, $ContaOrig, $ContaDest, $vDtTransf, $vVlrTranf);
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
}