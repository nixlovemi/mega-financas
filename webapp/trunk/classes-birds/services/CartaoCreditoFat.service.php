<?php
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/connection/Connection.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/obj-status/ObjStatus.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-general/Session.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/entities/CartaoCreditoFat.entity.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/CartaoCredito.service.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/Movimentacao.service.php';

class CartaoCreditoFatService{

	private $objStatus;

	private $objConn;
	
	private $objCartaoCreditoServ;
	
	private $objMovimentacaoServ;
	
	private $userLog;

	function __construct(){
		
		$Session = new Session();
		$this->userLog = $Session->getLoggedUserId();

		$this->objStatus = new ObjStatus();
		$this->objConn = new Connection();
		$this->objCartaoCreditoServ = new CartaoCreditoService();
		$this->objMovimentacaoServ = new MovimentacaoService();
	
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
			$this->objStatus->addRet('msg', 'id inválido para busca!');
			return $this->objStatus;
		}
		
		$sql = 'SELECT ccf_id, ccf_cc_id, ccf_mes, ccf_ano, ccf_total, ccf_mov_id, ccf_fechado
  					FROM tb_cartao_credito_fat
						INNER JOIN tb_cartao_credito ON cc_id = ccf_cc_id
						WHERE ccf_id = ?
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
			$ccf_id = $rs ['ccf_id'];
			$ccf_cc_id = $rs ['ccf_cc_id'];
			$ccf_mes = $rs ['ccf_mes'];
			$ccf_ano = $rs ['ccf_ano'];
			$ccf_total = $rs ['ccf_total'];
			$ccf_mov_id = $rs ['ccf_mov_id'];
			$ccf_fechado = $rs ['ccf_fechado'];
			
			// entidade CartaoCredito
			$CartaoCredito = new CartaoCredito();
			$objStatus = $this->objCartaoCreditoServ->buscaPorId($ccf_cc_id);
			if($objStatus->isOk()){
				$CartaoCredito = $objStatus->getRetByKey('ent');
			}
			// ----------------------
			
			// entidade Movimentacao
			$Movimentacao = null;
			if(is_numeric($ccf_mov_id)){
				$objStatus = $this->objMovimentacaoServ->buscaPorId($ccf_mov_id);
				if($objStatus->isOk()){
					$Movimentacao = $objStatus->getRetByKey('ent');
				}
			}
			// ---------------------
			
			$cartaoCreditoFat = new CartaoCreditoFat($ccf_id, $CartaoCredito, $ccf_mes, $ccf_ano, $ccf_total, $Movimentacao, $ccf_fechado);
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $cartaoCreditoFat);
			return $this->objStatus;
		}
	
	}

	/**
	 * Retorna a entidade; busca por cartao/mes/ano
	 * 
	 * @param CartaoCredito $CartaoCredito
	 * @param integer $mes
	 * @param integer $ano
	 * @return ObjStatus
	 */
	public function buscaPorCartaoMesAno(CartaoCredito $CartaoCredito, $mes, $ano){
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
		
		$sql = 'SELECT ccf_id
  					FROM tb_cartao_credito_fat
						INNER JOIN tb_cartao_credito ON cc_id = ccf_cc_id
						WHERE ccf_cc_id = ?
						AND ccf_mes = ?
						AND ccf_ano = ?
						AND cc_usu_id = ?';
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($CartaoCredito->getId());
		$this->objConn->addParameter($mes);
		$this->objConn->addParameter($ano);
		$this->objConn->addParameter($this->userLog);
		$rs = $this->objConn->selectRow();
		
		if (! count($rs) > 0) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Nenhum registro encontrado com esse id (' . $CartaoCredito->getId() . ')');
			return $this->objStatus;
		}
		else {
			return $this->buscaPorId($rs["ccf_id"]);
		}
	}
	
	/**
	 * Insere o CartaoCreditoFat no BD.
	 * Chaves resposta: msg, ent
	 *
	 * @param CartaoCreditoFat $cartaoCreditoFat        	
	 * @return ObjStatus
	 */
	public function insere(CartaoCreditoFat $cartaoCreditoFat){

		if (! is_a($cartaoCreditoFat, 'CartaoCreditoFat')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Variável não é Fatura Cartão');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaInsere($cartaoCreditoFat);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		// prepara SQL
		if (is_numeric($cartaoCreditoFat->getId())) {
			$sql = 'INSERT INTO tb_cartao_credito_fat(ccf_id, ccf_cc_id, ccf_mes, ccf_ano, ccf_total, ccf_mov_id, ccf_fechado)
    					VALUES (?, ?, ?, ?, ?, ?, ?)';
			$this->objConn->setSQL($sql);
			$this->objConn->addParameter($cartaoCreditoFat->getId());
		}
		else {
			$sql = 'INSERT INTO tb_cartao_credito_fat(ccf_cc_id, ccf_mes, ccf_ano, ccf_total, ccf_mov_id, ccf_fechado)
    					VALUES (?, ?, ?, ?, ?, ?)';
			$this->objConn->setSQL($sql);
		}
		
		$this->objConn->addParameter($cartaoCreditoFat->getCartaoCredito()->getId());
		$this->objConn->addParameter($cartaoCreditoFat->getMes());
		$this->objConn->addParameter($cartaoCreditoFat->getAno());
		$this->objConn->addParameter($cartaoCreditoFat->getTotal());
		
		$v_movimentacao = ( is_a($cartaoCreditoFat->getMovimentacao(), 'Movimentacao') ) ? $cartaoCreditoFat->getMovimentacao()->getId(): null;
		$this->objConn->addParameter($v_movimentacao);
		
		$this->objConn->addParameter($cartaoCreditoFat->getFechado());
		$returnField = $this->objConn->insert('ccf_id');
		// -----------
		
		if (! $this->objConn->getError()) {
			// versao atual da Entidade
			$cartaoCreditoFat = new CartaoCreditoFat();
			$objRet = $this->buscaPorId($returnField [0]);
			if ($objRet->isOk()) {
				$cartaoCreditoFat = $objRet->getRetByKey('ent');
			}
			// ------------------------
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $cartaoCreditoFat);
			$this->objStatus->addRet('msg', 'Fatura Cartão incluída com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao inserir Fatura Cartão.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 * Valida a insercao.
	 *
	 * @param CartaoCreditoFat $cartaoCreditoFat        	
	 * @return ObjStatus
	 */
	private function validaInsere(CartaoCreditoFat $cartaoCreditoFat){

		if($this->userLog != $cartaoCreditoFat->getCartaoCredito()->getUsuario()->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para inserir essa fatura!');
			return $this->objStatus;
		}
		
		if (!is_a($cartaoCreditoFat->getCartaoCredito(), 'CartaoCredito')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Cartão de Crédito inválido!');
			return $this->objStatus;
		}
		
		if (!(($cartaoCreditoFat->getMes() >= 1) && ($cartaoCreditoFat->getMes() <= 12))) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Mês inválido!');
			return $this->objStatus;
		}
		
		if (strlen($cartaoCreditoFat->getAno()) != 4) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Ano inválido!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 * faz a edicao do CartaoCreditoFat
	 *
	 * @param CartaoCreditoFat $cartaoCreditoFat        	
	 * @return ObjStatus
	 */
	public function edita(CartaoCreditoFat $cartaoCreditoFat){

		if (! is_a($cartaoCreditoFat, 'CartaoCreditoFat')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Variável não é Fatura Cartão');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaEdita($cartaoCreditoFat);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		$sql = 'UPDATE tb_cartao_credito_fat
   					SET ccf_cc_id = ?, ccf_mes = ?, ccf_ano = ?, ccf_total = ?, ccf_mov_id = ?, ccf_fechado = ?
 						WHERE ccf_id = ?';
		
		$this->objConn->setSQL($sql);
		
		$this->objConn->addParameter($cartaoCreditoFat->getCartaoCredito()->getId());
		$this->objConn->addParameter($cartaoCreditoFat->getMes());
		$this->objConn->addParameter($cartaoCreditoFat->getAno());
		$this->objConn->addParameter($cartaoCreditoFat->getTotal());
		
		$v_movimentacao = ( is_a($cartaoCreditoFat->getMovimentacao(), 'Movimentacao') ) ? $cartaoCreditoFat->getMovimentacao()->getId(): null;
		$this->objConn->addParameter($v_movimentacao);
		
		$this->objConn->addParameter($cartaoCreditoFat->getFechado());
		$this->objConn->addParameter($cartaoCreditoFat->getId());
		$returnField = $this->objConn->update();
		// -----------
		
		if (! $this->objConn->getError()) {
			// versao atual da Entidade
			$cartaoCreditoFat = new CartaoCreditoFat();
			$objRet = $this->buscaPorId($cartaoCreditoFat->getId());
			if ($objRet->isOk()) {
				$cartaoCreditoFat = $objRet->getRetByKey('ent');
			}
			// ------------------------
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $cartaoCreditoFat);
			$this->objStatus->addRet('msg', 'Fatura Cartão editada com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao editar Fatura Cartão.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 * Valida a edicao
	 *
	 * @param CartaoCreditoFat $cartaoCreditoFat        	
	 * @return ObjStatus
	 */
	private function validaEdita(CartaoCreditoFat $cartaoCreditoFat){
		
		if($this->userLog != $cartaoCreditoFat->getCartaoCredito()->getUsuario()->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para editar essa fatura!');
			return $this->objStatus;
		}
		
		if (!is_numeric($cartaoCreditoFat->getId())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'ID inválido!');
			return $this->objStatus;
		}

		if (!is_a($cartaoCreditoFat->getCartaoCredito(), 'CartaoCredito')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Cartão de Crédito inválido!');
			return $this->objStatus;
		}
		
		if (!(($cartaoCreditoFat->getMes() >= 1) && ($cartaoCreditoFat->getMes() <= 12))) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Mês inválido!');
			return $this->objStatus;
		}
		
		if (strlen($cartaoCreditoFat->getAno()) != 4) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Ano inválido!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 *
	 * @param CartaoCreditoFat $cartaoCreditoFat        	
	 * @return ObjStatus
	 */
	public function deleta(CartaoCreditoFat $cartaoCreditoFat){

		if (! is_a($cartaoCreditoFat, 'CartaoCreditoFat')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Variável não é Fatura Cartão');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaDeleta($cartaoCreditoFat);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		$sql = 'DELETE FROM tb_cartao_credito_fat
 				WHERE ccf_id = ?';
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($cartaoCreditoFat->getId());
		
		$qtLinhas = $this->objConn->delete();
		// -----------
		
		if (! $this->objConn->getError()) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $cartaoCreditoFat);
			$this->objStatus->addRet('msg', 'Fatura Cartão deletada com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao deletar Fatura Cartão.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 *
	 * @param CartaoCreditoFat $cartaoCreditoFat        	
	 * @return ObjStatus
	 */
	private function validaDeleta(CartaoCreditoFat $cartaoCreditoFat){

		if($this->userLog != $cartaoCreditoFat->getCartaoCredito()->getUsuario()->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para deletar essa fatura!');
			return $this->objStatus;
		}
		
		if (! is_numeric($cartaoCreditoFat->getId())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'ID inválida!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 * a partir de um cartao de credito, retorna os meses/anos das faturas dele
	 * @param CartaoCredito $CartaoCredito
	 * @return ObjStatus
	 */
	public function pegaMesesFatura(CartaoCredito $CartaoCredito){
		$sql = "SELECT DISTINCT ccf_ano || '-' || ccf_mes || '-01' AS mes_ano
						FROM tb_cartao_credito_fat
						INNER JOIN tb_cartao_credito ON cc_id = ccf_cc_id
						WHERE ccf_cc_id = ?
						AND cc_usu_id = ?
						ORDER BY ccf_ano || '-' || ccf_mes || '-01'";
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($CartaoCredito->getId());
		$this->objConn->addParameter($this->userLog);
		$resp = $this->objConn->executeSQL("ARRAY");
		
		if($this->objConn->getError()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao buscar os meses das faturas.');
			return $this->objStatus;
		}
		else{
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('arr_lista_meses_fatura', $resp);
			return $this->objStatus;
		}
	}

	/**
	 * retorna a entidade cartao credito fat com base nos parametros
	 * 
	 * @param CartaoCredito $CartaoCredito
	 * @param string $dtDespesa [data no formato YYYY-MM-DD]
	 * @return CartaoCreditoFat
	 */
	public function buscaFaturaPorDespesa(CartaoCredito $CartaoCredito, $dtDespesa){
		$diaFech = $CartaoCredito->getDiaFechamento();
		$arrDtDespesa = explode("-", $dtDespesa);
		$diaDespesa = $arrDtDespesa[2];
		$mesDespesa = $arrDtDespesa[1];
		$anoDespesa = $arrDtDespesa[0];
		
		// analisa qual vai ser o mes.ano da fatura
		$mesFatura = $mesDespesa;
		$anoFatura = $anoDespesa;
		
		if($diaDespesa >= $diaFech){
			$mesFatura++;
			
			if($mesFatura > 12){
				$mesFatura = 1;
				$anoFatura++;
			}
		}
		// ========================================
		
		$CartaoCreditoFat = new CartaoCreditoFat();
		$objResp = $this->buscaPorCartaoMesAno($CartaoCredito, $mesFatura, $anoFatura);
		
		if($objResp->isErro()){
			$CartaoCreditoFat->setAno($anoFatura);
			$CartaoCreditoFat->setCartaoCredito($CartaoCredito);
			$CartaoCreditoFat->setMes($mesFatura);
			
			$objResp = $this->insere($CartaoCreditoFat);
			if($objResp->isOk()){
				$CartaoCreditoFat = $objResp->getRetByKey("ent");
			}
		}
		else {
			$CartaoCreditoFat = $objResp->getRetByKey("ent");
		}
		
		return $CartaoCreditoFat;
	}
}
?>