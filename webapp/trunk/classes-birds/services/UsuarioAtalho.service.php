<?php
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/connection/Connection.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/obj-status/ObjStatus.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-general/Session.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/entities/UsuarioAtalho.entity.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/Atalho.service.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/Usuario.service.php';

class UsuarioAtalhoService{

	private $objStatus;

	private $objConn;
	
	private $objAtalhoService;
	
	private $objUsuarioService;
	
	private $userLog;

	function __construct(){
		
		$Session = new Session();
		$this->userLog = $Session->getLoggedUserId();

		$this->objStatus = new ObjStatus();
		$this->objConn = new Connection();
		$this->objAtalhoService = new UsuarioAtalhoService();
		$this->objUsuarioService = new UsuarioService();
	
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
			$this->objStatus->addRet('msg', 'ID inválido para busca!');
			return $this->objStatus;
		}
		
		$sql = 'SELECT ua_id, ua_ua_id, ua_usu_id
  					FROM tb_usuario_atalho
						WHERE ua_id = ?
						AND ua_usu_id = ?';
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
			$ua_id = $rs ['ua_id'];
			$ua_ua_id = $rs ['ua_ua_id'];
			$ua_usu_id = $rs ['ua_usu_id'];
			
			// atalho service
			$obj_atalho = new Atalho();
			$objStatus = $this->objAtalhoService->buscaPorId($ua_ua_id);
			if($objStatus->isOk()){
				$obj_atalho = $objStatus->getRetByKey('ent');
			}
			// --------------
			
			// usuario service
			$obj_usuario = new Usuario();
			$objStatus = $this->objUsuarioService->buscaPorId($ua_usu_id);
			if($objStatus->isOk()){
				$obj_usuario = $objStatus->getRetByKey('ent');
			}
			// ---------------
			
			$usuarioAtalho = new UsuarioAtalho($ua_id, $obj_atalho, $obj_usuario);
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $usuarioAtalho);
			return $this->objStatus;
		}
	
	}

	/**
	 * Insere o Atalho no BD.
	 * Chaves resposta: msg, ent
	 *
	 * @param UsuarioAtalho $usuarioAtalho
	 * @return ObjStatus
	 */
	public function insere(UsuarioAtalho $usuarioAtalho){

		if (! is_a($usuarioAtalho, 'UsuarioAtalho')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Variável não é UsuarioAtalho');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaInsere($usuarioAtalho);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		// prepara SQL
		if (is_numeric($usuarioAtalho->getId())) {
			$sql = 'INSERT INTO tb_usuario_atalho(ua_id, ua_ata_id, ua_usu_id)
    					VALUES (?, ?, ?)';
			$this->objConn->setSQL($sql);
			$this->objConn->addParameter($usuarioAtalho->getId());
		}
		else {
			$sql = 'INSERT INTO tb_usuario_atalho(ua_ata_id, ua_usu_id)
    					VALUES (?, ?)';
			$this->objConn->setSQL($sql);
		}
		
		$this->objConn->addParameter($usuarioAtalho->getAtalho()->getId());
		$this->objConn->addParameter($usuarioAtalho->getUsuario()->getId());
		
		$returnField = $this->objConn->insert('ua_id');
		// -----------
		
		if (! $this->objConn->getError()) {
			// versao atual da Entidade
			$usuarioAtalho = new UsuarioAtalho();
			$objRet = $this->buscaPorId($returnField [0]);
			if ($objRet->isOk()) {
				$usuarioAtalho = $objRet->getRetByKey('ent');
			}
			// ------------------------
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $usuarioAtalho);
			$this->objStatus->addRet('msg', 'Usuário Atalho incluído com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao inserir Usuário Atalho.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 * Valida a insercao.
	 *
	 * @param UsuarioAtalho $usuarioAtalho
	 * @return ObjStatus
	 */
	private function validaInsere(UsuarioAtalho $usuarioAtalho){
		
		if($this->userLog != $usuarioAtalho->getUsuario()->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para inserir esse atalho!');
			return $this->objStatus;
		}

		if (is_a($usuarioAtalho->getAtalho(), 'Atalho')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Atalho inválido!');
			return $this->objStatus;
		}
		
		if (is_a($usuarioAtalho->getUsuario(), 'Usuario')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usuário inválido!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 * faz a edicao do Atalho
	 *
	 * @param UsuarioAtalho $usuarioAtalho
	 * @return ObjStatus
	 */
	public function edita(UsuarioAtalho $usuarioAtalho){

		if (! is_a($usuarioAtalho, 'UsuarioAtalho')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Variável não é Usuário Atalho');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaEdita($usuarioAtalho);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		$sql = 'UPDATE tb_usuario_atalho
   					SET ua_ata_id = ?, ua_usu_id = ?
 						WHERE ua_id = ?';
		$this->objConn->setSQL($sql);
		
		$this->objConn->addParameter($usuarioAtalho->getAtalho()->getId());
		$this->objConn->addParameter($usuarioAtalho->getUsuario()->getId());
		$this->objConn->addParameter($usuarioAtalho->getId());
		
		$returnField = $this->objConn->update();
		// -----------
		
		if (! $this->objConn->getError()) {
			// versao atual da Entidade
			$usuarioAtalho = new UsuarioAtalho();
			$objRet = $this->buscaPorId($usuarioAtalho->getId());
			if ($objRet->isOk()) {
				$usuarioAtalho = $objRet->getRetByKey('ent');
			}
			// ------------------------
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $usuarioAtalho);
			$this->objStatus->addRet('msg', 'Usuário Atalho editado com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao editar Usuário Atalho.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 * Valida a edicao
	 *
	 * @param UsuarioAtalho $usuarioAtalho
	 * @return ObjStatus
	 */
	private function validaEdita(UsuarioAtalho $usuarioAtalho){
		
		if($this->userLog != $usuarioAtalho->getUsuario()->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para alterar esse atalho!');
			return $this->objStatus;
		}
		
		if(!is_numeric($usuarioAtalho->getId())){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'ID inválido!');
			return $this->objStatus;
		}

		if (is_a($usuarioAtalho->getAtalho(), 'Atalho')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Atalho inválido!');
			return $this->objStatus;
		}
		
		if (is_a($usuarioAtalho->getUsuario(), 'Usuario')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usuário inválido!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 *
	 * @param Atalho $usuarioAtalho        	
	 * @return ObjStatus
	 */
	public function deleta(UsuarioAtalho $usuarioAtalho){

		if (! is_a($usuarioAtalho, 'UsuarioAtalho')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Variável não é Usuário Atalho');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaDeleta($usuarioAtalho);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		$sql = 'DELETE FROM tb_usuario_atalho
 						WHERE ua_id = ?';
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($usuarioAtalho->getId());
		
		$qtLinhas = $this->objConn->delete();
		// -----------
		
		if (! $this->objConn->getError()) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $usuarioAtalho);
			$this->objStatus->addRet('msg', 'Usuário Atalho deletado com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao deletar Atalho.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 *
	 * @param Atalho $usuarioAtalho        	
	 * @return ObjStatus
	 */
	private function validaDeleta(UsuarioAtalho $usuarioAtalho){
		
		if($this->userLog != $usuarioAtalho->getUsuario()->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para deletar esse atalho!');
			return $this->objStatus;
		}

		if (! is_numeric($usuarioAtalho->getId())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'ID inválida!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

}
?>