<?php
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/connection/Connection.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/obj-status/ObjStatus.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-general/Session.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/entities/CartaoCredito.entity.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/Usuario.service.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/BandeiraCartao.service.php';

class CartaoCreditoService{

	private $objStatus;

	private $objConn;
	
	private $objUsuarioServ;
	
	private $objBandeiraCartaoServ;
	
	private $userLog;

	function __construct(){
		
		$Session = new Session();
		$this->userLog = $Session->getLoggedUserId();

		$this->objStatus = new ObjStatus();
		$this->objConn = new Connection();
		$this->objUsuarioServ = new UsuarioService();
		$this->objBandeiraCartaoServ = new BandeiraCartaoService();
	
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
		
		$sql = 'SELECT cc_id, cc_descricao, cc_usu_id, cc_bc_id, cc_limite, cc_dia_fechamento, cc_dia_pagamento, cc_deletado
  					FROM tb_cartao_credito
						WHERE cc_id = ?
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
			$cc_id = $rs ['cc_id'];
			$cc_descricao = $rs ['cc_descricao'];
			$cc_usu_id = $rs ['cc_usu_id'];
			$cc_bc_id = $rs ['cc_bc_id'];
			$cc_limite = $rs ['cc_limite'];
			$cc_dia_fechamento = $rs ['cc_dia_fechamento'];
			$cc_dia_pagamento = $rs ['cc_dia_pagamento'];
			$cc_deletado = $rs ['cc_deletado'];
			
			// entidade usuario
			$obj_usuario = new Usuario();
			$objStatus = $this->objUsuarioServ->buscaPorId($cc_usu_id);
			if($objStatus->isOk()){
				$obj_usuario = $objStatus->getRetByKey('ent');
			}
			// ----------------
			
			// entidade bandeira cartao
			$obj_bandeira_cartao = new BandeiraCartao();
			$objStatus = $this->objBandeiraCartaoServ->buscaPorId($cc_bc_id);
			if($objStatus->isOk()){
				$obj_bandeira_cartao =  $objStatus->getRetByKey('ent');
			}
			// ------------------------
			
			$cartaoCredito = new CartaoCredito($cc_id, $cc_descricao, $obj_usuario, $obj_bandeira_cartao, $cc_limite, $cc_dia_fechamento, $cc_dia_pagamento, $cc_deletado);
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $cartaoCredito);
			return $this->objStatus;
		}
	
	}

	/**
	 * Insere o CartaoCredito no BD.
	 * Chaves resposta: msg, ent
	 *
	 * @param CartaoCredito $cartaoCredito        	
	 * @return ObjStatus
	 */
	public function insere(CartaoCredito $cartaoCredito){

		if (! is_a($cartaoCredito, 'CartaoCredito')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Vari&aacute;vel n&atilde;o &eacute; Cart&atilde;o Cr&eacute;dito');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaInsere($cartaoCredito);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		// prepara SQL
		if (is_numeric($cartaoCredito->getId())) {
			$sql = 'INSERT INTO tb_cartao_credito(cc_id, cc_descricao, cc_usu_id, cc_bc_id, cc_limite, cc_dia_fechamento, cc_dia_pagamento, cc_deletado)
    				VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
			$this->objConn->setSQL($sql);
			$this->objConn->addParameter($cartaoCredito->getId());
		}
		else {
			$sql = 'INSERT INTO tb_cartao_credito(cc_descricao, cc_usu_id, cc_bc_id, cc_limite, cc_dia_fechamento, cc_dia_pagamento, cc_deletado)
    				VALUES (?, ?, ?, ?, ?, ?, ?)';
			$this->objConn->setSQL($sql);
		}
		
		$this->objConn->addParameter($cartaoCredito->getDescricao());
		$this->objConn->addParameter($cartaoCredito->getUsuario()->getId());
		$this->objConn->addParameter($cartaoCredito->getBandeiraCartao()->getId());
		$this->objConn->addParameter($cartaoCredito->getLimite());
		$this->objConn->addParameter($cartaoCredito->getDiaFechamento());
		$this->objConn->addParameter($cartaoCredito->getDiaPagamento());
		$this->objConn->addParameter($cartaoCredito->getDeletado());
		
		$returnField = $this->objConn->insert('cc_id');
		// -----------
		
		if (! $this->objConn->getError()) {
			// versao atual da Entidade
			$cartaoCredito = new CartaoCredito();
			$objRet = $this->buscaPorId($returnField [0]);
			if ($objRet->isOk()) {
				$cartaoCredito = $objRet->getRetByKey('ent');
			}
			// ------------------------
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $cartaoCredito);
			$this->objStatus->addRet('msg', 'Cart&atilde;o de Cr&eacute;dito inclu&iacute;do com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao inserir CartaoCredito.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 * Valida a insercao.
	 *
	 * @param CartaoCredito $cartaoCredito        	
	 * @return ObjStatus
	 */
	private function validaInsere(CartaoCredito $cartaoCredito){

		if($this->userLog != $cartaoCredito->getUsuario()->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para inserir esse cart&atilde;o!');
			return $this->objStatus;
		}
		
		if (strlen($cartaoCredito->getDescricao()) <= 3) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Descri&ccedil;&atilde;o inv&aacute;lida!');
			return $this->objStatus;
		}
		
		if (!is_a($cartaoCredito->getUsuario(), 'Usuario')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido!');
			return $this->objStatus;
		}
		
		if (!is_a($cartaoCredito->getBandeiraCartao(), 'BandeiraCartao')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Bandeira inv&aacute;lido!');
			return $this->objStatus;
		}
		
		if ($cartaoCredito->getLimite() <= 0) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Limite inv&aacute;lido!');
			return $this->objStatus;
		}
		
		if (!is_numeric($cartaoCredito->getDiaFechamento())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Dia de Fechamento inv&aacute;lido!');
			return $this->objStatus;
		}
		
		if (!is_numeric($cartaoCredito->getDiaPagamento())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Dia de Pagamento inv&aacute;lido!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 * faz a edicao do CartaoCredito
	 *
	 * @param CartaoCredito $cartaoCredito        	
	 * @return ObjStatus
	 */
	public function edita(CartaoCredito $cartaoCredito){

		if (! is_a($cartaoCredito, 'CartaoCredito')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Vari&aacute;vel n&atilde;o &eacute; Cart&atilde;o Cr&eacute;dito');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaEdita($cartaoCredito);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		$sql = 'UPDATE tb_cartao_credito
   				SET cc_descricao = ?, cc_usu_id = ?, cc_bc_id = ?, cc_limite = ?, cc_dia_fechamento = ?, cc_dia_pagamento = ?, cc_deletado = ?
 				WHERE cc_id = ?';
		
		$this->objConn->setSQL($sql);
		
		$this->objConn->addParameter($cartaoCredito->getDescricao());
		$this->objConn->addParameter($cartaoCredito->getUsuario()->getId());
		$this->objConn->addParameter($cartaoCredito->getBandeiraCartao()->getId());
		$this->objConn->addParameter($cartaoCredito->getLimite());
		$this->objConn->addParameter($cartaoCredito->getDiaFechamento());
		$this->objConn->addParameter($cartaoCredito->getDiaPagamento());
		$this->objConn->addParameter($cartaoCredito->getDeletado());
		$this->objConn->addParameter($cartaoCredito->getId());
		
		$returnField = $this->objConn->update();
		// -----------
		
		if (! $this->objConn->getError()) {
			// versao atual da Entidade
			$cartaoCredito = new CartaoCredito();
			$objRet = $this->buscaPorId($cartaoCredito->getId());
			if ($objRet->isOk()) {
				$cartaoCredito = $objRet->getRetByKey('ent');
			}
			// ------------------------
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $cartaoCredito);
			$this->objStatus->addRet('msg', 'Cart&atilde;o de Cr&eacute;dito editado com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao editar Cart&atilde;o Cr&eacute;dito.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 * Valida a edicao
	 *
	 * @param CartaoCredito $cartaoCredito        	
	 * @return ObjStatus
	 */
	private function validaEdita(CartaoCredito $cartaoCredito){

		if($this->userLog != $cartaoCredito->getUsuario()->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para editar esse cart&atilde;o!');
			return $this->objStatus;
		}
		
		if (! is_numeric($cartaoCredito->getId())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'ID inv&aacute;lido!');
			return $this->objStatus;
		}
		
		if (strlen($cartaoCredito->getDescricao()) <= 3) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Descri&ccedil;&atilde;o inv&aacute;lida!');
			return $this->objStatus;
		}
		
		if (!is_a($cartaoCredito->getUsuario(), 'Usuario')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido!');
			return $this->objStatus;
		}
		
		if (!is_a($cartaoCredito->getBandeiraCartao(), 'BandeiraCartao')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Bandeira inv&aacute;lido!');
			return $this->objStatus;
		}
		
		if ($cartaoCredito->getLimite() <= 0) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Limite inv&aacute;lido!');
			return $this->objStatus;
		}
		
		if (!is_numeric($cartaoCredito->getDiaFechamento())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Dia de Fechamento inv&aacute;lido!');
			return $this->objStatus;
		}
		
		if (!is_numeric($cartaoCredito->getDiaPagamento())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Dia de Pagamento inv&aacute;lido!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 *
	 * @param CartaoCredito $cartaoCredito        	
	 * @return ObjStatus
	 */
	public function deleta(CartaoCredito $cartaoCredito){

		if (! is_a($cartaoCredito, 'CartaoCredito')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Vari&aacute;vel n&atilde;o &eacute; Cart&atilde;o Cr&eacute;dito');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaDeleta($cartaoCredito);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		$sql = 'DELETE FROM tb_cartao_credito
 						WHERE cc_id = ?';
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($cartaoCredito->getId());
		
		$qtLinhas = $this->objConn->delete();
		// -----------
		
		if (! $this->objConn->getError()) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $cartaoCredito);
			$this->objStatus->addRet('msg', 'Cart&atilde;o de Cr&eacute;dito deletado com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao deletar Cart&atilde;o Cr&eacute;dito.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 *
	 * @param CartaoCredito $cartaoCredito        	
	 * @return ObjStatus
	 */
	private function validaDeleta(CartaoCredito $cartaoCredito){

		if($this->userLog != $cartaoCredito->getUsuario()->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para deletar esse cart&atilde;o!');
			return $this->objStatus;
		}
		
		if (! is_numeric($cartaoCredito->getId())) {
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
	 * @return ObjStatus
	 */
	public function pegaListaCartoes(Usuario $Usuario){
		
		if($this->userLog != $Usuario->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para listar cart&otilde;es!');
			return $this->objStatus;
		}
		
		$sql = "SELECT cc_id
								,cc_descricao
								,bc_descricao
								,bc_mini_imagem
								,cc_limite
								,cc_dia_fechamento
								,cc_dia_pagamento
						FROM tb_cartao_credito
						LEFT JOIN tb_bandeira_cartao ON bc_id = cc_bc_id
						WHERE cc_deletado = FALSE
						AND cc_usu_id = ?
						ORDER BY cc_descricao";
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($Usuario->getId());
		$resp = $this->objConn->executeSQL("ARRAY");
		
		if($this->objConn->getError()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao buscar os cart&otilde;es.');
			return $this->objStatus;
		}
		else{
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('arr_lista_cartao', $resp);
			return $this->objStatus;
		}
	}
}
?>