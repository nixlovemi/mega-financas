<?php
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/connection/Connection.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/obj-status/ObjStatus.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-general/Session.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-general/Validation.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/entities/CartaoCreditoMov.entity.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/CartaoCreditoFat.service.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/MovimentacaoCat.service.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/Projeto.service.php';

class CartaoCreditoMovService{

	private $objStatus;

	private $objConn;
	
	private $objValidation;
	
	private $objCartaoCreditoFatServ;
	
	private $objMovimentacaoCatServ;
	
	private $objProjetoServ;
	
	private $userLog;

	function __construct(){
		
		$Session = new Session();
		$this->userLog = $Session->getLoggedUserId();

		$this->objStatus = new ObjStatus();
		$this->objConn = new Connection();
		$this->objCartaoCreditoFatServ = new CartaoCreditoFatService();
		$this->objMovimentacaoCatServ = new MovimentacaoCatService();
		$this->objProjetoServ = new ProjetoService();
		$this->objValidation = new Validation();
	
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
		
		$sql = 'WITH tot_parcela(tp_ccm_id_parcelado, tp_tot_parcelas) AS (
						  SELECT ccm_id_parcelado
						         ,COUNT(ccm_id_parcelado)
						  FROM tb_cartao_credito_mov
						  WHERE ccm_id_parcelado > 0
							AND ccm_deletado = FALSE
						  GROUP BY ccm_id_parcelado
						)
				
						SELECT ccm_id, ccm_ccf_id, ccm_descricao, ccm_valor, ccm_mc_id, ccm_id_parcelado, ccm_parcela, ccm_deletado, ccm_pro_id, ccm_data, COALESCE(tp_tot_parcelas, 0) AS tp_tot_parcelas
  					FROM tb_cartao_credito_mov
						INNER JOIN tb_cartao_credito_fat ON ccf_id = ccm_ccf_id
						INNER JOIN tb_cartao_credito ON cc_id = ccf_cc_id
						LEFT JOIN tot_parcela ON tp_ccm_id_parcelado = ccm_id_parcelado
						WHERE ccm_id = ?
						AND cc_usu_id = ?';
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
			$ccm_id = $rs ['ccm_id'];
			$ccm_ccf_id = $rs ['ccm_ccf_id'];
			$ccm_descricao = $rs ['ccm_descricao'];
			$ccm_valor = $rs ['ccm_valor'];
			$ccm_mc_id = $rs ['ccm_mc_id'];
			$ccm_id_parcelado = $rs ['ccm_id_parcelado'];
			$ccm_parcela = $rs ['ccm_parcela'];
			$ccm_deletado = $rs ['ccm_deletado'];
			$ccm_pro_id = $rs ['ccm_pro_id'];
			$ccm_data = $rs ['ccm_data'];
			$tot_parcelas = $rs ['tp_tot_parcelas'];
			
			// entidade CartaoCreditoFat
			$CartaoCreditoFat = new CartaoCreditoFat();
			$objStatus = $this->objCartaoCreditoFatServ->buscaPorId($ccm_ccf_id);
			if($objStatus->isOk()){
				$CartaoCreditoFat = $objStatus->getRetByKey('ent');
			}
			// -------------------------
			
			// entidade MovimentacaoCat
			$MovimentacaoCat = new MovimentacaoCat();
			$objStatus = $this->objMovimentacaoCatServ->buscaPorId($ccm_mc_id);
			if($objStatus->isOk()){
				$MovimentacaoCat = $objStatus->getRetByKey('ent');
			}
			// ------------------------
			
			// entidade Projeto
			$Projeto = null;
			if(is_numeric($ccm_pro_id)){
				$objStatus = $this->objProjetoServ->buscaPorId($ccm_pro_id);
				if($objStatus->isOk()){
					$Projeto = $objStatus->getRetByKey('ent');
				}
			}
			// ----------------
			
			$cartaoCreditoMov = new CartaoCreditoMov($ccm_id, $CartaoCreditoFat, $ccm_descricao, $ccm_valor, $MovimentacaoCat, $ccm_id_parcelado, $ccm_parcela, $ccm_deletado, $Projeto, $ccm_data, $tot_parcelas);
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $cartaoCreditoMov);
			return $this->objStatus;
		}
	
	}

	/**
	 * Insere o CartaoCreditoMov no BD.
	 * Chaves resposta: msg, ent
	 *
	 * @param CartaoCreditoMov $cartaoCreditoMov        	
	 * @return ObjStatus
	 */
	public function insere(CartaoCreditoMov $cartaoCreditoMov){

		if (! is_a($cartaoCreditoMov, 'CartaoCreditoMov')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Vari&aacute;vel n&atilde;o &eacute; Movimenta&ccedil;&atilde;o de Cart&atilde;o');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaInsere($cartaoCreditoMov);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		// prepara SQL
		if (is_numeric($cartaoCreditoMov->getId())) {
			$sql = 'INSERT INTO tb_cartao_credito_mov(ccm_id, ccm_ccf_id, ccm_descricao, ccm_valor, ccm_mc_id, ccm_id_parcelado, ccm_parcela, ccm_deletado, ccm_pro_id, ccm_data)
    					VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
			$this->objConn->setSQL($sql);
			$this->objConn->addParameter($cartaoCreditoMov->getId());
		}
		else {
			$sql = 'INSERT INTO tb_cartao_credito_mov(ccm_ccf_id, ccm_descricao, ccm_valor, ccm_mc_id, ccm_id_parcelado, ccm_parcela, ccm_deletado, ccm_pro_id, ccm_data)
    					VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
			$this->objConn->setSQL($sql);
		}
		
		$this->objConn->addParameter($cartaoCreditoMov->getCartaoCreditoFat()->getId());
		$this->objConn->addParameter($cartaoCreditoMov->getDescricao());
		$this->objConn->addParameter($cartaoCreditoMov->getValor());
		$this->objConn->addParameter($cartaoCreditoMov->getMovimentacaoCat()->getId());
		$this->objConn->addParameter($cartaoCreditoMov->getIdParcelado());
		$this->objConn->addParameter($cartaoCreditoMov->getParcela());
		$this->objConn->addParameter($cartaoCreditoMov->getDeletado());
		
		$v_projeto = ( is_a($cartaoCreditoMov->getProjeto(), 'Projeto') ) ? $cartaoCreditoMov->getProjeto()->getId(): null;
		$this->objConn->addParameter($v_projeto);
		
		$this->objConn->addParameter($cartaoCreditoMov->getData());
		
		$returnField = $this->objConn->insert('ccm_id');
		// -----------
		
		if (! $this->objConn->getError()) {
			// versao atual da Entidade
			$cartaoCreditoMov = new CartaoCreditoMov();
			$objRet = $this->buscaPorId($returnField [0]);
			if ($objRet->isOk()) {
				$cartaoCreditoMov = $objRet->getRetByKey('ent');
			}
			// ------------------------
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $cartaoCreditoMov);
			$this->objStatus->addRet('msg', 'Movimenta&ccedil;&atilde;o de Cart&atilde;o inclu&iacute;da com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao inserir Movimenta&ccedil;&atilde;o de Cart&atilde;o.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 * Valida a insercao.
	 *
	 * @param CartaoCreditoMov $cartaoCreditoMov        	
	 * @return ObjStatus
	 */
	private function validaInsere(CartaoCreditoMov $cartaoCreditoMov){
		
		if($this->userLog != $cartaoCreditoMov->getCartaoCreditoFat()->getCartaoCredito()->getUsuario()->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para inserir essa movimenta&ccedil;&atilde;o!');
			return $this->objStatus;
		}

		if (!is_a($cartaoCreditoMov->getCartaoCreditoFat(), 'CartaoCreditoFat')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Fatura de Cart&atilde;o inv&aacute;lida!');
			return $this->objStatus;
		}
		
		if (strlen($cartaoCreditoMov->getDescricao()) < 3) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Descri&ccedil;&atilde;o inv&aacute;lida!');
			return $this->objStatus;
		}
		
		if ($cartaoCreditoMov->getValor() < 0) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Valor inv&aacute;lido!');
			return $this->objStatus;
		}
		
		if (!is_a($cartaoCreditoMov->getMovimentacaoCat(), 'MovimentacaoCat')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Categoria inv&aacute;lida!');
			return $this->objStatus;
		}
		
		if (!$this->objValidation->isDate($cartaoCreditoMov->getData())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Data da Despesa inv&aacute;lida!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 * faz a edicao do CartaoCreditoMov
	 *
	 * @param CartaoCreditoMov $cartaoCreditoMov        	
	 * @return ObjStatus
	 */
	public function edita(CartaoCreditoMov $cartaoCreditoMov){

		if (! is_a($cartaoCreditoMov, 'CartaoCreditoMov')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Vari&aacute;vel n&atilde;o &eacute; Movimenta&ccedil;&atilde;o de Cart&atilde;o');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaEdita($cartaoCreditoMov);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		$sql = 'UPDATE tb_cartao_credito_mov
   					SET ccm_ccf_id = ?, ccm_descricao = ?, ccm_valor = ?, ccm_mc_id = ?, ccm_id_parcelado = ?, ccm_parcela = ?, ccm_deletado = ?, ccm_pro_id = ?, ccm_data = ?
 						WHERE ccm_id = ?';
		$this->objConn->setSQL($sql);
		
		$this->objConn->addParameter($cartaoCreditoMov->getCartaoCreditoFat()->getId());
		$this->objConn->addParameter($cartaoCreditoMov->getDescricao());
		$this->objConn->addParameter($cartaoCreditoMov->getValor());
		$this->objConn->addParameter($cartaoCreditoMov->getMovimentacaoCat()->getId());
		$this->objConn->addParameter($cartaoCreditoMov->getIdParcelado());
		$this->objConn->addParameter($cartaoCreditoMov->getParcela());
		$this->objConn->addParameter($cartaoCreditoMov->getDeletado());
		
		$v_projeto = ( is_a($cartaoCreditoMov->getProjeto(), 'Projeto') ) ? $cartaoCreditoMov->getProjeto()->getId(): null;
		$this->objConn->addParameter($v_projeto);
		
		$this->objConn->addParameter($cartaoCreditoMov->getData());
		$this->objConn->addParameter($cartaoCreditoMov->getId());
		
		$returnField = $this->objConn->update();
		// -----------
		
		FAZER TRIGGER PARA QDO ALTERAR/DELETAR E TIVER PARCELAMENTO,
		APAGAR TUDO E/OU SETAR TUDO COMO DELETADO!!!!!!!!!!!!!!!!!!!
		(MUDAR NO METODO DELETAR TB)
		
		if (! $this->objConn->getError()) {
			// versao atual da Entidade
			$cartaoCreditoMov = new CartaoCreditoMov();
			$objRet = $this->buscaPorId($cartaoCreditoMov->getId());
			if ($objRet->isOk()) {
				$cartaoCreditoMov = $objRet->getRetByKey('ent');
			}
			// ------------------------
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $cartaoCreditoMov);
			$this->objStatus->addRet('msg', 'Movimenta&ccedil;&atilde;o de Cart&atilde;o editada com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao editar Movimenta&ccedil;&atilde;o de Cart&atilde;o.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 * Valida a edicao
	 *
	 * @param CartaoCreditoMov $cartaoCreditoMov        	
	 * @return ObjStatus
	 */
	private function validaEdita(CartaoCreditoMov $cartaoCreditoMov){
		
		if($this->userLog != $cartaoCreditoMov->getCartaoCreditoFat()->getCartaoCredito()->getUsuario()->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para editar essa movimenta&ccedil;&atilde;o!');
			return $this->objStatus;
		}
		
		if (!is_numeric($cartaoCreditoMov->getId())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'ID inv&aacute;lido!');
			return $this->objStatus;
		}

		if (!is_a($cartaoCreditoMov->getCartaoCreditoFat(), 'CartaoCreditoFat')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Fatura de Cart&atilde;o inv&aacute;lida!');
			return $this->objStatus;
		}
		
		if (strlen($cartaoCreditoMov->getDescricao()) < 3) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Descri&ccedil;&atilde;o inv&aacute;lida!');
			return $this->objStatus;
		}
		
		if ($cartaoCreditoMov->getValor() < 0) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Valor inv&aacute;lido!');
			return $this->objStatus;
		}
		
		if (!is_a($cartaoCreditoMov->getMovimentacaoCat(), 'MovimentacaoCat')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Categoria inv&aacute;lida!');
			return $this->objStatus;
		}
		
		if (!$this->objValidation->isDate($cartaoCreditoMov->getData())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Data da Despesa inv&aacute;lida!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 *
	 * @param CartaoCreditoMov $cartaoCreditoMov        	
	 * @return ObjStatus
	 */
	public function deleta(CartaoCreditoMov $cartaoCreditoMov){

		if (! is_a($cartaoCreditoMov, 'CartaoCreditoMov')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Vari&aacute;vel n&atilde;o &eacute; Movimenta&ccedil;&atilde;o de Cart&atilde;o');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaDeleta($cartaoCreditoMov);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		if($cartaoCreditoMov->getIdParcelado() > 0){
			$sql = 'DELETE FROM tb_cartao_credito_mov
 							WHERE ccm_id_parcelado = ?';
			$this->objConn->setSQL($sql);
			$this->objConn->addParameter($cartaoCreditoMov->getIdParcelado());
			
			$msgOk = "Movimenta&ccedil;&atilde;o de Cart&atilde;o e parcelamento deletados com sucesso!";
		} else {
			$sql = 'DELETE FROM tb_cartao_credito_mov
 							WHERE ccm_id = ?';
			$this->objConn->setSQL($sql);
			$this->objConn->addParameter($cartaoCreditoMov->getId());
			
			$msgOk = "Movimenta&ccedil;&atilde;o de Cart&atilde;o deletada com sucesso!";
		}
		
		$qtLinhas = $this->objConn->delete();
		// -----------
		
		if (! $this->objConn->getError()) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $cartaoCreditoMov);
			$this->objStatus->addRet('msg', $msgOk);
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao deletar Movimenta&ccedil;&atilde;o de Cart&atilde;o.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 *
	 * @param CartaoCreditoMov $cartaoCreditoMov        	
	 * @return ObjStatus
	 */
	private function validaDeleta(CartaoCreditoMov $cartaoCreditoMov){

		if($this->userLog != $cartaoCreditoMov->getCartaoCreditoFat()->getCartaoCredito()->getUsuario()->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para deletar essa movimenta&ccedil;&atilde;o!');
			return $this->objStatus;
		}
		
		if (! is_numeric($cartaoCreditoMov->getId())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'ID inv&aacute;lida!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 * busca todas as movimentacoes do cartao no mes.ano especifico
	 * 
	 * @param CartaoCredito $CartaoCredito
	 * @param integer $mes
	 * @param integer $ano
	 * @return ObjStatus
	 */
	public function buscaMovimentacoes(CartaoCredito $CartaoCredito, $mes, $ano){
		if (! is_numeric($mes)) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'M&ecirc;s inv&aacute;lido para busca!');
			return $this->objStatus;
		}
		
		if (! is_numeric($ano)) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Ano inv&aacute;lido para busca!');
			return $this->objStatus;
		}
		
		if (! is_numeric($CartaoCredito->getId())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Cart&atilde;o inv&aacute;lido para busca!');
			return $this->objStatus;
		}
		
		$sql = "WITH tot_parcela(tp_ccm_id_parcelado, tp_tot_parcelas) AS (
						  SELECT ccm_id_parcelado
						         ,COUNT(ccm_id_parcelado)
						  FROM tb_cartao_credito_mov
						  WHERE ccm_id_parcelado > 0
							AND ccm_deletado = FALSE
						  GROUP BY ccm_id_parcelado
						)
				
						SELECT ccm_id
						       ,ccm_descricao
						       ,ccm_valor
						       ,ccm_data
						       ,mc_descricao
									 ,ccm_parcela
									 ,tp_tot_parcelas
						FROM tb_cartao_credito_mov
						INNER JOIN tb_cartao_credito_fat ON ccf_id = ccm_ccf_id
						INNER JOIN tb_cartao_credito ON cc_id = ccf_cc_id
						LEFT JOIN tb_movimentacao_cat ON mc_id = ccm_mc_id
						LEFT JOIN tot_parcela ON tp_ccm_id_parcelado = ccm_id_parcelado
						WHERE ccf_cc_id = ?
						AND ccf_mes = ?
						AND ccf_ano = ?
						AND cc_usu_id = ?
						AND ccm_deletado = FALSE
						ORDER BY ccm_data DESC";
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($CartaoCredito->getId());
		$this->objConn->addParameter($mes);
		$this->objConn->addParameter($ano);
		$this->objConn->addParameter($this->userLog);
		$rs = $this->objConn->select();
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		$this->objStatus->addRet('rs', $rs);
		return $this->objStatus;
	}

	/**
	 * pega o proximo ID PARCELADO
	 * @return int
	 */
	public function pegaIdParcelado(){
		$sql = "SELECT NEXTVAL('tb_cartao_credito_mov_id_parcelado_seq') AS id_parcelado";
		$this->objConn->setSQL($sql);
		$row = $this->objConn->selectRow();
		
		return $row["id_parcelado"];
	}

	/**
	 * 
	 * @param Usuario $Usuario
	 * @param unknown $idParcelamento
	 * @return ObjStatus
	 */
	public function pegaMovimentacoesParcelamento(Usuario $Usuario, $idParcelamento){
		if($this->userLog != $Usuario->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para buscar essa movimenta&ccedil;&atilde;o!');
			return $this->objStatus;
		}
		
		$V_USU_ID = $Usuario->getId();
		
		$sql = 'WITH tot_parcela(tp_ccm_id_parcelado, tp_tot_parcelas) AS (
						  SELECT ccm_id_parcelado
						         ,COUNT(ccm_id_parcelado)
						  FROM tb_cartao_credito_mov
						  WHERE ccm_id_parcelado > 0
							AND ccm_deletado = FALSE
						  GROUP BY ccm_id_parcelado
						)
		
						SELECT ccm_id
										, ccm_ccf_id
										, ccm_descricao
										, ccm_valor
										, ccm_mc_id
										, ccm_id_parcelado
										, ccm_parcela
										, ccm_deletado
										, ccm_pro_id
										, ccm_data
										, COALESCE(tp_tot_parcelas, 0) AS tp_tot_parcelas
										, cc_descricao
										, mc_descricao
										, ccf_mes
										, ccf_ano
  					FROM tb_cartao_credito_mov
						INNER JOIN tb_cartao_credito_fat ON ccf_id = ccm_ccf_id
						INNER JOIN tb_cartao_credito ON cc_id = ccf_cc_id
						LEFT JOIN tb_movimentacao_cat ON mc_id = ccm_mc_id
						LEFT JOIN tot_parcela ON tp_ccm_id_parcelado = ccm_id_parcelado
						WHERE ccm_id_parcelado = ?
						AND cc_usu_id = ?
						ORDER BY ccm_parcela';
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($idParcelamento);
		$this->objConn->addParameter($V_USU_ID);
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
}
?>