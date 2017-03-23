<?php
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/connection/Connection.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/obj-status/ObjStatus.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-general/Session.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-general/Validation.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/entities/Movimentacao.entity.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/Projeto.service.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/Conta.service.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/Usuario.service.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/MovimentacaoCat.service.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/MovimentacaoTipo.service.php';

class MovimentacaoService{

	private $objStatus;

	private $objConn;
	
	private $objValidation;
	
	private $objProjetoServ;
	
	private $objContaServ;
	
	private $objUsuarioServ;
	
	private $objMovimentacaoCatServ;
	
	private $userLog;

	function __construct(){
		
		$Session = new Session();
		$this->userLog = $Session->getLoggedUserId();

		$this->objStatus = new ObjStatus();
		$this->objConn = new Connection();
		$this->objValidation = new Validation();
		$this->objProjetoServ = new ProjetoService();
		$this->objContaServ = new ContaService();
		$this->objUsuarioServ = new UsuarioService();
		$this->objMovimentacaoCatServ = new MovimentacaoCatService();
	
	}

	/**
	 * Retorna a entidade; busca por id (PK)
	 * Chave para entidade = ent
	 *
	 * @param VARIANT $id        	
	 * @return ObjStatus
	 */
	public function buscaPorId($id){

		if (! is_numeric($id)) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'id inv&aacute;lido para busca!');
			return $this->objStatus;
		}
		
		$sql = 'WITH tot_parcela(tp_mov_id_parcelado, tp_tot_parcelas) AS (
						  SELECT mov_id_parcelado
						         ,COUNT(mov_id_parcelado)
						  FROM tb_movimentacao
						  WHERE mov_id_parcelado > 0
							AND mov_deletado = FALSE
						  GROUP BY mov_id_parcelado
						)
						
						SELECT	mov_id, mov_pro_id, mov_con_id, mov_usu_id, mov_mc_id, mov_descricao, mov_observacao,
										mov_dt_competencia, mov_dt_vencimento, mov_valor, mov_dt_pagamento, mov_valor_pago,
										mov_id_parcelado, mov_parcela, mov_deletado, tp_tot_parcelas, mov_transferencia_id, mov_transferencia_tipo
		  			FROM tb_movimentacao
						INNER JOIN tb_conta ON con_id = mov_con_id
						LEFT JOIN tot_parcela ON tp_mov_id_parcelado = mov_id_parcelado
						WHERE mov_id = ?
						AND con_usu_id = ?
						AND mov_deletado = FALSE';
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($id);
		$this->objConn->addParameter($this->userLog);
		$rs = $this->objConn->selectRow();
		
		if (! count($rs) > 0) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Nenhum registro encontrado com esse id (' . $id . ')');
			return $this->objStatus;
		}
		else {
			$mov_id = $rs ['mov_id'];
			$mov_pro_id = $rs ['mov_pro_id'];
			$mov_con_id = $rs ['mov_con_id'];
			$mov_usu_id = $rs ['mov_usu_id'];
			$mov_mc_id = $rs ['mov_mc_id'];
			$mov_descricao = $rs ['mov_descricao'];
			$mov_observacao = $rs ['mov_observacao'];
			$mov_dt_competencia = $rs ['mov_dt_competencia'];
			$mov_dt_vencimento = $rs ['mov_dt_vencimento'];
			$mov_valor = $rs ['mov_valor'];
			$mov_dt_pagamento = $rs ['mov_dt_pagamento'];
			$mov_valor_pago = $rs ['mov_valor_pago'];
			$mov_id_parcelado = $rs ['mov_id_parcelado'];
			$mov_parcela = $rs ['mov_parcela'];
			$mov_deletado = $rs ['mov_deletado'];
			$mov_transferencia_id = $rs ['mov_transferencia_id'];
			$mov_transferencia_tipo = $rs ['mov_transferencia_tipo'];
			$v_tot_parcelas = (is_numeric($rs ['tp_tot_parcelas'])) ? $rs ['tp_tot_parcelas']: NULL;
			
			// entidade projeto
			$obj_projeto = new Projeto();
			if(is_numeric($mov_pro_id)){
				$objStatus = $this->objProjetoServ->buscaPorId($mov_pro_id);
				if($objStatus->isOk()){
					$obj_projeto = $objStatus->getRetByKey('ent');
				}
				else{
					$obj_projeto = null;
				}
			}
			else{
				$obj_projeto = null;
			}
			// ----------------
			
			// entidade conta
			$obj_conta = new Conta();
			$objStatus = $this->objContaServ->buscaPorId($mov_con_id);
			if($objStatus->isOk()){
				$obj_conta = $objStatus->getRetByKey('ent');
			}
			// --------------
			
			// entidade usuario
			$obj_usuario = new Usuario();
			$objStatus = $this->objUsuarioServ->buscaPorId($mov_usu_id);
			if($objStatus->isOk()){
				$obj_usuario = $objStatus->getRetByKey('ent');
			}
			// ----------------
			
			// entidade movimentacao cat
			$obj_movimentacao_cat = null;
			if( $mov_mc_id > 0 ){
				$obj_movimentacao_cat = new MovimentacaoCat();
				$objStatus = $this->objMovimentacaoCatServ->buscaPorId($mov_mc_id);
				if($objStatus->isOk()){
					$obj_movimentacao_cat = $objStatus->getRetByKey('ent');
				}
			}
			// -------------------------
			
			// entidade movimentacao tipo - mov_transferencia_tipo
			$obj_transferencia_tipo = null;
			if( $mov_transferencia_tipo > 0 ){
				$MovimentacaoTipoServ = new MovimentacaoTipoService();
				$objRet = $MovimentacaoTipoServ->buscaPorId($mov_transferencia_tipo);
				
				if( $objRet->isOk() ){
					$obj_transferencia_tipo = $objRet->getRetByKey("ent");
				}
			}
			// ===================================================
			
			$movimentacao = new Movimentacao($mov_id, $obj_projeto, $obj_conta, $obj_usuario, $obj_movimentacao_cat, $mov_descricao, $mov_observacao, $mov_dt_competencia, $mov_dt_vencimento, $mov_valor, $mov_dt_pagamento, $mov_valor_pago, $mov_id_parcelado, $mov_parcela, $mov_deletado, $v_tot_parcelas, $mov_transferencia_id, $obj_transferencia_tipo);
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $movimentacao);
			return $this->objStatus;
		}
	
	}

	/**
	 * Insere o Movimentacao no BD.
	 * Chaves resposta: msg, ent
	 *
	 * @param Movimentacao $movimentacao        	
	 * @return ObjStatus
	 */
	public function insere(Movimentacao $movimentacao){

		if (! is_a($movimentacao, 'Movimentacao')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Vari&aacute;vel n&atilde;o &eacute; uma Movimenta&ccedil;&atilde;o');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaInsere($movimentacao);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		// prepara SQL
		if (is_numeric($movimentacao->getId())) {
			$sql = 'INSERT INTO tb_movimentacao(mov_id, mov_pro_id, mov_con_id, mov_usu_id, mov_mc_id, mov_descricao, mov_observacao,
					   							mov_dt_competencia, mov_dt_vencimento, mov_valor, mov_dt_pagamento, mov_valor_pago,
												mov_id_parcelado, mov_parcela, mov_deletado, mov_transferencia_id, mov_transferencia_tipo)
    				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
			$this->objConn->setSQL($sql);
			$this->objConn->addParameter($movimentacao->getId());
		}
		else {
			$sql = 'INSERT INTO tb_movimentacao(mov_pro_id, mov_con_id, mov_usu_id, mov_mc_id, mov_descricao, mov_observacao,
					   							mov_dt_competencia, mov_dt_vencimento, mov_valor, mov_dt_pagamento, mov_valor_pago,
												mov_id_parcelado, mov_parcela, mov_deletado, mov_transferencia_id, mov_transferencia_tipo)
    				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
			$this->objConn->setSQL($sql);
		}
		
		$v_projeto = ( is_a($movimentacao->getProjeto(), 'Projeto') ) ? $movimentacao->getProjeto()->getId(): null;
		$this->objConn->addParameter($v_projeto);
		$this->objConn->addParameter($movimentacao->getConta()->getId());
		$this->objConn->addParameter($movimentacao->getUsuario()->getId());
		
		$v_categoria = ( is_a($movimentacao->getCategoria(), "MovimentacaoCat") ) ? $movimentacao->getCategoria()->getId(): null;
		$this->objConn->addParameter($v_categoria);
		
		$this->objConn->addParameter($movimentacao->getDescricao());
		$this->objConn->addParameter($movimentacao->getObservacao());
		$this->objConn->addParameter($movimentacao->getDtCompetencia());
		$this->objConn->addParameter($movimentacao->getDtVencimento());
		$this->objConn->addParameter($movimentacao->getValor());
		$this->objConn->addParameter($movimentacao->getDtPagamento());
		$this->objConn->addParameter($movimentacao->getValorPago());
		$this->objConn->addParameter($movimentacao->getIdParcelado());
		$this->objConn->addParameter($movimentacao->getParcela());
		$this->objConn->addParameter($movimentacao->getDeletado());
		$this->objConn->addParameter($movimentacao->getTransferenciaId());
		
		$v_transferencia_tipo = (is_a($movimentacao->getTransferenciaTipo(), "MovimentacaoTipo")) ? $movimentacao->getTransferenciaTipo()->getId(): null;
		$this->objConn->addParameter($v_transferencia_tipo);
		
		$returnField = $this->objConn->insert('mov_id');
		// -----------
		
		if (! $this->objConn->getError()) {
			// versao atual da Entidade
			$movimentacao = new Movimentacao();
			$objRet = $this->buscaPorId($returnField [0]);
			if ($objRet->isOk()) {
				$movimentacao = $objRet->getRetByKey('ent');
			}
			// ------------------------
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $movimentacao);
			$this->objStatus->addRet('msg', 'Movimenta&ccedil;&atilde;o inclu&iacute;da com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao inserir Movimenta&ccedil;&atilde;o.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 * Valida a insercao.
	 *
	 * @param Movimentacao $movimentacao        	
	 * @return ObjStatus
	 */
	private function validaInsere(Movimentacao $movimentacao){
		
		if($this->userLog != $movimentacao->getConta()->getUsuario()->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para inserir essa movimenta&ccedil;&atilde;o!');
			return $this->objStatus;
		}

		if (! is_a($movimentacao->getConta(), 'Conta')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Conta inv&aacute;lida!');
			return $this->objStatus;
		}
		
		if (! is_a($movimentacao->getUsuario(), 'Usuario')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido!');
			return $this->objStatus;
		}
		
		if ($movimentacao->getCategoria() != null && !is_a($movimentacao->getCategoria(), 'MovimentacaoCat')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Categoria inv&aacute;lida!');
			return $this->objStatus;
		}
		
		if (strlen($movimentacao->getDescricao()) <= 3) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Descri&ccedil;&atilde;o inv&aacute;lida!');
			return $this->objStatus;
		}
		
		if (!$this->objValidation->isDate($movimentacao->getDtCompetencia())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Data de Compet&ecirc;ncia inv&aacute;lida!');
			return $this->objStatus;
		}
		
		if (!$this->objValidation->isDate($movimentacao->getDtVencimento())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Data de Vencimento inv&aacute;lida!');
			return $this->objStatus;
		}
		
		if ($movimentacao->getValor() <= 0) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Valor inv&aacute;lido!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 * faz a edicao do Movimentacao
	 *
	 * @param Movimentacao $movimentacao        	
	 * @return ObjStatus
	 */
	public function edita(Movimentacao $movimentacao){

		if (! is_a($movimentacao, 'Movimentacao')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Vari&aacute;vel n&atilde;o &eacute; uma Movimenta&ccedil;&atilde;o');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaEdita($movimentacao);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		$sql = 'UPDATE tb_movimentacao
		   				SET mov_pro_id = ?, mov_con_id = ?, mov_usu_id = ?, mov_mc_id = ?, mov_descricao = ?, mov_observacao = ?,
							mov_dt_competencia = ?, mov_dt_vencimento = ?, mov_valor = ?, mov_dt_pagamento = ?, mov_valor_pago = ?,
							mov_id_parcelado = ?, mov_parcela = ?, mov_deletado = ?, mov_transferencia_id = ?, mov_transferencia_tipo = ?
		 				WHERE mov_id = ?';
		
		$this->objConn->setSQL($sql);
		
		$v_projeto = ( is_a($movimentacao->getProjeto(), 'Projeto') ) ? $movimentacao->getProjeto()->getId(): null;
		$this->objConn->addParameter($v_projeto);
		$this->objConn->addParameter($movimentacao->getConta()->getId());
		$this->objConn->addParameter($movimentacao->getUsuario()->getId());
		
		$v_categoria = (is_a($movimentacao->getCategoria(), "MovimentacaoCat")) ? $movimentacao->getCategoria()->getId(): null;
		$this->objConn->addParameter($v_categoria);
		
		$this->objConn->addParameter($movimentacao->getDescricao());
		$this->objConn->addParameter($movimentacao->getObservacao());
		$this->objConn->addParameter($movimentacao->getDtCompetencia());
		$this->objConn->addParameter($movimentacao->getDtVencimento());
		$this->objConn->addParameter($movimentacao->getValor());
		$this->objConn->addParameter($movimentacao->getDtPagamento());
		$this->objConn->addParameter($movimentacao->getValorPago());
		$this->objConn->addParameter($movimentacao->getIdParcelado());
		$this->objConn->addParameter($movimentacao->getParcela());
		$this->objConn->addParameter($movimentacao->getDeletado());
		$this->objConn->addParameter($movimentacao->getTransferenciaId());
		
		$v_transferencia_tipo = (is_a($movimentacao->getTransferenciaTipo(), "MovimentacaoTipo")) ? $movimentacao->getTransferenciaTipo()->getId(): null;
		$this->objConn->addParameter($v_transferencia_tipo);
		
		$this->objConn->addParameter($movimentacao->getId());
		$returnField = $this->objConn->update();
		// -----------
		
		if (! $this->objConn->getError()) {
			// versao atual da Entidade
			$movimentacao = new Movimentacao();
			$objRet = $this->buscaPorId($movimentacao->getId());
			if ($objRet->isOk()) {
				$movimentacao = $objRet->getRetByKey('ent');
			}
			// ------------------------
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $movimentacao);
			$this->objStatus->addRet('msg', 'Movimenta&ccedil;&atilde;o editada com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao editar Movimenta&ccedil;&atilde;o.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 * Valida a edicao
	 *
	 * @param Movimentacao $movimentacao        	
	 * @return ObjStatus
	 */
	private function validaEdita(Movimentacao $movimentacao){
		
		if($this->userLog != $movimentacao->getConta()->getUsuario()->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para editar essa movimenta&ccedil;&atilde;o!');
			return $this->objStatus;
		}

		if (! is_numeric($movimentacao->getId())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'ID inv&aacute;lido!');
			return $this->objStatus;
		}
		
		if (! is_a($movimentacao->getConta(), 'Conta')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Conta inv&aacute;lida!');
			return $this->objStatus;
		}
		
		if (! is_a($movimentacao->getUsuario(), 'Usuario')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido!');
			return $this->objStatus;
		}
		
		if ($movimentacao->getCategoria() != null && !is_a($movimentacao->getCategoria(), 'MovimentacaoCat')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Categoria inv&aacute;lida!');
			return $this->objStatus;
		}
		
		if (strlen($movimentacao->getDescricao()) <= 3) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Descri&ccedil;&atilde;o inv&aacute;lida!');
			return $this->objStatus;
		}
		
		if (!$this->objValidation->isDate($movimentacao->getDtCompetencia())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Data de Compet&ecirc;ncia inv&aacute;lida!');
			return $this->objStatus;
		}
		
		if (!$this->objValidation->isDate($movimentacao->getDtVencimento())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Data de Vencimento inv&aacute;lida!');
			return $this->objStatus;
		}
		
		if ($movimentacao->getValor() <= 0) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Valor inv&aacute;lido!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 *
	 * @param Movimentacao $movimentacao        	
	 * @return ObjStatus
	 */
	public function deleta(Movimentacao $movimentacao){

		if (! is_a($movimentacao, 'Movimentacao')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Vari&aacute;vel n&atilde;o &eacute; uma Movimenta&ccedil;&atilde;o');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaDeleta($movimentacao);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		$sql = 'DELETE FROM tb_movimentacao
 						WHERE mov_id = ?';
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($movimentacao->getId());
		
		$qtLinhas = $this->objConn->delete();
		// -----------
		
		if (! $this->objConn->getError()) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $movimentacao);
			$this->objStatus->addRet('msg', 'Movimenta&ccedil;&atilde;o deletada com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao deletar Movimenta&ccedil;&atilde;o.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 *
	 * @param Movimentacao $movimentacao        	
	 * @return ObjStatus
	 */
	private function validaDeleta(Movimentacao $movimentacao){
		
		if($this->userLog != $movimentacao->getConta()->getUsuario()->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para deletar essa movimenta&ccedil;&atilde;o!');
			return $this->objStatus;
		}

		if (! is_numeric($movimentacao->getId())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'ID inv&aacute;lida!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 * 
	 * @param Usuario $Usuario
	 * @param Conta $Conta
	 * @param string $dtInicio [formato YYYY-MM-DD]
	 * @param string $dtFim [formato YYYY-MM-DD]
	 * @return ObjStatus
	 */
	public function pegaMovimentacoesEntrada(Usuario $Usuario, Conta $Conta, $dtInicio="", $dtFim=""){
		//==============================================
		//@todo unir essa funcao e a de saida numa mesma
		// =============================================
		
		if($this->userLog != $Usuario->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para pesquisar essa movimenta&ccedil;&atilde;o!');
			return $this->objStatus;
		}
		
		// Movimentacao tipo ===========================
		require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/MovimentacaoTipo.service.php';
		$MovimentacaoTpServ = new MovimentacaoTipoService();
		$objResp = $MovimentacaoTpServ->pegaReceita();
		if($objResp->isErro()){
			return $objResp;
		}
		$MovimentacaoTp = $objResp->getRetByKey("ent");
		$id_Receita = $MovimentacaoTp->getId();
		// =============================================
		
		if( $dtInicio == "" || $dtFim == "" ){
			$dtInicio = date('Y-m-01');
			$dtFim = date('Y-m-t');
		}
		
		$V_USU_ID = $Usuario->getId();
		$V_CON_ID = $Conta->getId();
		
		$sql = "WITH tot_parcela(tp_mov_id_parcelado, tp_tot_parcelas) AS (
						  SELECT mov_id_parcelado
						         ,COUNT(mov_id_parcelado)
						  FROM tb_movimentacao
						  WHERE mov_usu_id = ?
						    AND mov_id_parcelado > 0
						    AND mov_deletado = FALSE
						  GROUP BY mov_id_parcelado
						)
						
						SELECT mov_id AS id,
						       con_nome AS conta,
						       mc_descricao AS categoria,
						       mov_descricao AS descricao,
						       mov_dt_competencia AS competencia,
						       mov_dt_vencimento AS vencimento,
						       mov_valor AS valor,
						       mov_dt_pagamento AS pagamento,
						       mov_valor_pago AS valor_pago,
						       mov_parcela AS parcela,
						       tp_tot_parcelas AS tot_parcelas
						FROM tb_movimentacao
						LEFT JOIN tb_conta ON con_id = mov_con_id
						LEFT JOIN tb_movimentacao_cat ON mc_id = mov_mc_id
						LEFT JOIN tot_parcela ON tp_mov_id_parcelado = mov_id_parcelado
						WHERE mov_usu_id = ?
						  AND mov_con_id = ?
						  AND mov_dt_competencia BETWEEN '$dtInicio 00:00:00' AND '$dtFim 23:59:59'
						  AND COALESCE(mc_mt_id, mov_transferencia_tipo) = ?
						  AND mov_deletado = FALSE
						ORDER BY mov_dt_competencia DESC;";
		$this->objConn->setSQL($sql);
	
		$this->objConn->addParameter($V_USU_ID);
		$this->objConn->addParameter($V_USU_ID);
		$this->objConn->addParameter($V_CON_ID);
		$this->objConn->addParameter($id_Receita);
		$resp = $this->objConn->executeSQL("ARRAY");
		
		if($this->objConn->getError()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao buscar movimenta&ccedil;&oatilde;es de entrada.');
			return $this->objStatus;
		}
		else{
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('arr_movEntradas', $resp);
			return $this->objStatus;
		}
	}
	
	/**
	 *
	 * @param Usuario $Usuario
	 * @param Conta $Conta
	 * @param string $dtInicio [formato YYYY-MM-DD]
	 * @param string $dtFim [formato YYYY-MM-DD]
	 * @return ObjStatus
	 */
	public function pegaMovimentacoesSaida(Usuario $Usuario, Conta $Conta, $dtInicio="", $dtFim=""){
		//================================================
		//@todo unir essa funcao e a de entrada numa mesma
		// ===============================================
		
		if($this->userLog != $Usuario->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para buscar essa movimenta&ccedil;&atilde;o!');
			return $this->objStatus;
		}
		
		// Movimentacao tipo ===========================
		require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/MovimentacaoTipo.service.php';
		$MovimentacaoTpServ = new MovimentacaoTipoService();
		$objResp = $MovimentacaoTpServ->pegaDespesa();
		if($objResp->isErro()){
			return $objResp;
		}
		$MovimentacaoTp = $objResp->getRetByKey("ent");
		$id_Despesa = $MovimentacaoTp->getId();
		// =============================================
	
		if( $dtInicio == "" || $dtFim == "" ){
			$dtInicio = date('Y-m-01');
			$dtFim = date('Y-m-t');
		}
	
		$V_USU_ID = $Usuario->getId();
		$V_CON_ID = $Conta->getId();
	
		$sql = "WITH tot_parcela(tp_mov_id_parcelado, tp_tot_parcelas) AS (
						  SELECT mov_id_parcelado
						         ,COUNT(mov_id_parcelado)
						  FROM tb_movimentacao
						  WHERE mov_usu_id = ?
						    AND mov_id_parcelado > 0
						    AND mov_deletado = FALSE
						  GROUP BY mov_id_parcelado
						)
						
						SELECT mov_id AS id,
						       con_nome AS conta,
						       mc_descricao AS categoria,
						       mov_descricao AS descricao,
						       mov_dt_competencia AS competencia,
						       mov_dt_vencimento AS vencimento,
						       mov_valor AS valor,
						       mov_dt_pagamento AS pagamento,
						       mov_valor_pago AS valor_pago,
						       mov_parcela AS parcela,
						       tp_tot_parcelas AS tot_parcelas
						FROM tb_movimentacao
						LEFT JOIN tb_conta ON con_id = mov_con_id
						LEFT JOIN tb_movimentacao_cat ON mc_id = mov_mc_id
						LEFT JOIN tot_parcela ON tp_mov_id_parcelado = mov_id_parcelado
						WHERE mov_usu_id = ?
						  AND mov_con_id = ?
						  AND mov_dt_competencia BETWEEN '$dtInicio 00:00:00' AND '$dtFim 23:59:59'
						  AND COALESCE(mc_mt_id, mov_transferencia_tipo) = ?
						  AND mov_deletado = FALSE
						ORDER BY mov_dt_competencia DESC;";
		$this->objConn->setSQL($sql);
	
		$this->objConn->addParameter($V_USU_ID);
		$this->objConn->addParameter($V_USU_ID);
		$this->objConn->addParameter($V_CON_ID);
		$this->objConn->addParameter($id_Despesa);
		$resp = $this->objConn->executeSQL("ARRAY");
	
		if($this->objConn->getError()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao buscar movimenta&ccedil;&oatilde;es de saida.');
			return $this->objStatus;
		}
		else{
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('arr_movSaidas', $resp);
			return $this->objStatus;
		}
	}

	/**
	 *
	 * @param Usuario $Usuario
	 * @param int $idParcelado
	 * @return ObjStatus
	 */
	public function pegaMovimentacoesParcelamento(Usuario $Usuario, $idParcelado){
		//================================================
		//@todo unir essa funcao e a de entrada/saida numa mesma
		// ===============================================
		
		if($this->userLog != $Usuario->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para buscar essa movimenta&ccedil;&atilde;o!');
			return $this->objStatus;
		}
	
		$V_USU_ID = $Usuario->getId();
	
		$sql = "WITH tot_parcela(tp_mov_id_parcelado, tp_tot_parcelas) AS (
							SELECT mov_id_parcelado
									,COUNT(mov_id_parcelado)
							FROM tb_movimentacao
							WHERE mov_usu_id = ?
								AND mov_id_parcelado > 0
								AND mov_deletado = FALSE
							GROUP BY mov_id_parcelado
						)
					
						SELECT mov_id AS id,
								con_nome AS conta,
								mc_descricao AS categoria,
								mov_descricao AS descricao,
								mov_dt_competencia AS competencia,
								mov_dt_vencimento AS vencimento,
								mov_valor AS valor,
								mov_dt_pagamento AS pagamento,
								mov_valor_pago AS valor_pago,
								mov_parcela AS parcela,
								tp_tot_parcelas AS tot_parcelas
						FROM tb_movimentacao
						LEFT JOIN tb_conta ON con_id = mov_con_id
						LEFT JOIN tb_movimentacao_cat ON mc_id = mov_mc_id
						LEFT JOIN tot_parcela ON tp_mov_id_parcelado = mov_id_parcelado
						WHERE mov_usu_id = ?
						AND mov_id_parcelado = ?
						AND mov_deletado = FALSE
						ORDER BY mov_dt_competencia ASC;";
		$this->objConn->setSQL($sql);
	
		$this->objConn->addParameter($V_USU_ID);
		$this->objConn->addParameter($V_USU_ID);
		$this->objConn->addParameter($idParcelado);
		$resp = $this->objConn->executeSQL("ARRAY");
	
		if($this->objConn->getError()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao buscar parcelamento.');
			return $this->objStatus;
		}
		else{
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('arr_movParcelado', $resp);
			return $this->objStatus;
		}
	}
	
	/**
	 * pega o ID PARCELADO a partir de uma Movimentacao
	 * @param Movimentacao $Movimentacao
	 * @return int
	 */
	public function pegaIdParceladoPorMovimentacao(Movimentacao $Movimentacao){
		$V_MOV_ID = $Movimentacao->getId();
		
		$sql = "SELECT mov_id_parcelado
						FROM tb_movimentacao
						INNER JOIN tb_conta ON con_id = mov_con_id
						WHERE mov_id = ?
						AND con_usu_id = ?";
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($V_MOV_ID);
		$this->objConn->addParameter($this->userLog);
		$row = $this->objConn->selectRow();
		
		return $row["mov_id_parcelado"];
		
	}
	
	/**
	 * pega o proximo ID PARCELADO
	 * @return int
	 */
	public function pegaIdParcelado(){
		$sql = "SELECT NEXTVAL('tb_movimentacao_id_parcelado_seq') AS id_parcelado";
		$this->objConn->setSQL($sql);
		$row = $this->objConn->selectRow();
		
		return $row["id_parcelado"];
	}

	/**
	 * insere uma transferencia, que na verdade é movimentar saida da origem e entrada do destino
	 * 
	 * @param Conta $ContaOrig
	 * @param Conta $ContaDest
	 * @param string $DtTransf [YYYY-MM-DD]
	 * @param string $VlrTransf [X.XX]
	 */
	public function insereTransferencia(Usuario $Usuario, Conta $ContaOrig, Conta $ContaDest, $DtTransf, $VlrTransf){
		// valida as informacoes
		if ($ContaOrig->getId() <= 0) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Conta de Origem inv&aacute;lida!');
			return $this->objStatus;
		}
		
		if ($ContaDest->getId() <= 0) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Conta de Destino inv&aacute;lida!');
			return $this->objStatus;
		}
		
		if ($ContaOrig->getId() == $ContaDest->getId()) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Conta de Origem e Destino devem ser diferentes!');
			return $this->objStatus;
		}
		
		if (!$this->objValidation->isDate($DtTransf)) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Data de Transfer&ecirc;ncia inv&aacute;lida!');
			return $this->objStatus;
		}
		
		if ($VlrTransf <= 0) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Valor inv&aacute;lido!');
			return $this->objStatus;
		}
		// =====================
		
		$MovimentacaoTipoServ = new MovimentacaoTipoService();
		
		// faz movimentacao de saida da conta origem
		$objRet = $MovimentacaoTipoServ->buscaPorId($MovimentacaoTipoServ->pegaDespesa());
		$objMovTipoDespesa = $objRet->getRetByKey("ent");
		
		$MovimentacaoOrig = new Movimentacao();
		$MovimentacaoOrig->setConta($ContaOrig);
		$MovimentacaoOrig->setUsuario($Usuario);
		$MovimentacaoOrig->setDescricao(utf8_encode("Transferência para Conta " . $ContaDest->getNome()));
		$MovimentacaoOrig->setDtCompetencia($DtTransf);
		$MovimentacaoOrig->setDtVencimento($DtTransf);
		$MovimentacaoOrig->setValor($VlrTransf);
		$MovimentacaoOrig->setDtPagamento($DtTransf);
		$MovimentacaoOrig->setValorPago($VlrTransf);
		$MovimentacaoOrig->setTransferenciaTipo($objMovTipoDespesa);
		$MovimentacaoOrig->setDeletado('f');
		
		$objRet = $this->insere($MovimentacaoOrig);
		if($objRet->isErro()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao inserir a Transfer&ecirc;ncia!');
			return $this->objStatus;
		}
		
		$obj_mov_transf_origem = $objRet->getRetByKey("ent");
		// =========================================
		
		// faz movimentacao de entrada da conta destino
		$objRet = $MovimentacaoTipoServ->buscaPorId($MovimentacaoTipoServ->pegaReceita());
		$objMovTipoReceita = $objRet->getRetByKey("ent");
		
		$MovimentacaoDest = new Movimentacao();
		$MovimentacaoDest->setConta($ContaDest);
		$MovimentacaoDest->setUsuario($Usuario);
		$MovimentacaoDest->setDescricao(utf8_encode("Transferência da Conta " . $ContaOrig->getNome()));
		$MovimentacaoDest->setDtCompetencia($DtTransf);
		$MovimentacaoDest->setDtVencimento($DtTransf);
		$MovimentacaoDest->setValor($VlrTransf);
		$MovimentacaoDest->setDtPagamento($DtTransf);
		$MovimentacaoDest->setValorPago($VlrTransf);
		$MovimentacaoDest->setTransferenciaTipo($objMovTipoReceita);
		$MovimentacaoDest->setDeletado('f');
		
		$objRet = $this->insere($MovimentacaoDest);
		if($objRet->isErro()){
			// deleta a movimentacao da origem
			$this->deleta($obj_mov_transf_origem);
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao inserir a Transfer&ecirc;ncia!');
			return $this->objStatus;
		}
		
		$obj_mov_transf_destino = $objRet->getRetByKey("ent");
		// ============================================
		
		// atualiza as mov_transferencia_id ===========
		$obj_mov_transf_origem->setTransferenciaId( $obj_mov_transf_destino->getId() );
		$this->edita($obj_mov_transf_origem);
		
		$obj_mov_transf_destino->setTransferenciaId( $obj_mov_transf_origem->getId() );
		$this->edita($obj_mov_transf_destino);
		// ============================================
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		$this->objStatus->addRet('msg', 'Transfer&ecirc;ncia inclu&iacute;da com sucesso!');
		return $this->objStatus;
	}

	public function editaTransferencia($vIdMovimentacao, $ContaOrig, $ContaDest, $DtTransf, $VlrTransf){
		// busca entidade movimentacao - origem
		$objRet = $this->buscaPorId($vIdMovimentacao);
		if($objRet->isErro()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao buscar transfer&ecirc;ncia!');
			return $this->objStatus;
		}
		
		$MovimentacaoOrig = $objRet->getRetByKey("ent");
		// ====================================
		
		// valida as informacoes
		if($MovimentacaoOrig->getTransferenciaId() <= 0){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Transfer&ecirc;ncia inv&aacute;lida para alterar!');
			return $this->objStatus;
		}
		
		if ($ContaOrig->getId() <= 0) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Conta de Origem inv&aacute;lida!');
			return $this->objStatus;
		}
		
		if ($ContaDest->getId() <= 0) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Conta de Destino inv&aacute;lida!');
			return $this->objStatus;
		}
		
		if ($ContaOrig->getId() == $ContaDest->getId()) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Conta de Origem e Destino devem ser diferentes!');
			return $this->objStatus;
		}
		
		if (!$this->objValidation->isDate($DtTransf)) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Data de Transfer&ecirc;ncia inv&aacute;lida!');
			return $this->objStatus;
		}
		
		if ($VlrTransf <= 0) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Valor inv&aacute;lido!');
			return $this->objStatus;
		}
		// =====================
		
		// busca entidade movimentacao - destino
		$objRet = $this->buscaPorId($MovimentacaoOrig->getTransferenciaId());
		if($objRet->isErro()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao buscar transfer&ecirc;ncia destino!');
			return $this->objStatus;
		}
		
		$MovimentacaoDest = $objRet->getRetByKey("ent");
		// =====================================
		
		// acerta a origem =====================
		$MovimentacaoOrig->setConta($ContaOrig);
		$MovimentacaoOrig->setDescricao(utf8_encode("Transferência para Conta " . $ContaDest->getNome()));
		$MovimentacaoOrig->setDtCompetencia($DtTransf);
		$MovimentacaoOrig->setDtVencimento($DtTransf);
		$MovimentacaoOrig->setValor($VlrTransf);
		$MovimentacaoOrig->setDtPagamento($DtTransf);
		$MovimentacaoOrig->setValorPago($VlrTransf);
		
		$this->edita($MovimentacaoOrig);
		// =====================================
		
		// acerta o destino ====================
		$MovimentacaoDest->setConta($ContaDest);
		$MovimentacaoDest->setDescricao(utf8_encode("Transferência da Conta " . $ContaOrig->getNome()));
		$MovimentacaoDest->setDtCompetencia($DtTransf);
		$MovimentacaoDest->setDtVencimento($DtTransf);
		$MovimentacaoDest->setValor($VlrTransf);
		$MovimentacaoDest->setDtPagamento($DtTransf);
		$MovimentacaoDest->setValorPago($VlrTransf);
		
		$this->edita($MovimentacaoDest);
		// =====================================
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		$this->objStatus->addRet('msg', 'Transfer&ecirc;ncia alterada com sucesso!');
		return $this->objStatus;
	}
	
	/**
	 * campos retorno: id_movimentacao, origem, destino, data, valor, id_transf
	 * 
	 * @param Usuario $Usuario
	 * @param string $dtInicio
	 * @param string $dtFim
	 * @return ObjStatus
	 */
	public function pegaTransferencias(Usuario $Usuario, Conta $ContaOrig, $dtInicio="", $dtFim=""){
		if($this->userLog != $Usuario->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para buscar essa transfer&ecirc;ncia!');
			return $this->objStatus;
		}
		
		if( $dtInicio == "" || $dtFim == "" ){
			$dtInicio = date('Y-m-01');
			$dtFim = date('Y-m-t');
		}
		
		$V_USU_ID = $Usuario->getId();
		$V_ID_SAIDA = 2;
		$V_ID_ENTRADA = 1;
		
		$sql = "WITH transf_destinos(td_mov_id, td_con_nome) AS (
						  SELECT mov_id
						         ,con_nome
						  FROM tb_conta
						  INNER JOIN tb_movimentacao ON mov_con_id = con_id
						  WHERE mov_transferencia_tipo = $V_ID_ENTRADA
						  AND mov_usu_id = ?
						)
						
						SELECT mov_id AS id_movimentacao
										,con_nome AS origem
										,td_con_nome AS destino
										,mov_dt_competencia AS data
										,mov_valor_pago AS valor
										,mov_transferencia_id AS id_transf
						FROM tb_movimentacao
						INNER JOIN tb_movimentacao_tipo ON mt_id = mov_transferencia_tipo
						INNER JOIN tb_conta ON con_id = mov_con_id
						INNER JOIN transf_destinos ON td_mov_id = mov_transferencia_id 	
						WHERE mt_id = $V_ID_SAIDA
						AND mov_dt_competencia BETWEEN '$dtInicio 00:00:00' AND '$dtFim 23:59:59'
						AND mov_deletado = FALSE
						AND mov_usu_id = ?
						AND con_id = ?";
		$this->objConn->setSQL($sql);
	
		$this->objConn->addParameter($V_USU_ID);
		$this->objConn->addParameter($V_USU_ID);
		$this->objConn->addParameter($ContaOrig->getId());
		$resp = $this->objConn->executeSQL("ARRAY");
		
		if($this->objConn->getError()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao buscar transfer&ecirc;ncias.');
			return $this->objStatus;
		}
		else{
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('arr_transferencias', $resp);
			return $this->objStatus;
		}
	}
}
?>