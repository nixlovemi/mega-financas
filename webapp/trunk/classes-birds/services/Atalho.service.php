<?php
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/connection/Connection.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/obj-status/ObjStatus.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/entities/Atalho.entity.php';

class AtalhoService{

	private $objStatus;

	private $objConn;

	function __construct(){

		$this->objStatus = new ObjStatus();
		$this->objConn = new Connection();
	
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
		
		$sql = 'SELECT ata_id, ata_nome, ata_fa_icone, ata_controller, ata_action, ata_ativo
  				FROM tb_atalho
				WHERE ata_id = ?';
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($id);
		$rs = $this->objConn->selectRow();
		
		if (! count($rs) > 0) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Nenhum registro encontrado com esse id (' . $id . ')');
			return $this->objStatus;
		}
		else {
			$ata_id = $rs ['ata_id'];
			$ata_nome = $rs ['ata_nome'];
			$ata_fa_icone = $rs ['ata_fa_icone'];
			$ata_controller = $rs ['ata_controller'];
			$ata_action = $rs ['ata_action'];
			$ata_ativo = $rs ['ata_ativo'];
			
			$atalho = new Atalho($ata_id, $ata_nome, $ata_fa_icone, $ata_controller, $ata_action, $ata_ativo);
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $atalho);
			return $this->objStatus;
		}
	
	}

	/**
	 * Insere o Atalho no BD.
	 * Chaves resposta: msg, ent
	 *
	 * @param Atalho $atalho        	
	 * @return ObjStatus
	 */
	public function insere(Atalho $atalho){

		if (! is_a($atalho, 'Atalho')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Variável não é Atalho');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaInsere($atalho);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		// prepara SQL
		if (is_numeric($atalho->getId())) {
			$sql = 'INSERT INTO tb_atalho(ata_id, ata_nome, ata_fa_icone, ata_controller, ata_action, ata_ativo)
    				VALUES (?, ?, ?, ?, ?, ?)';
			$this->objConn->setSQL($sql);
			$this->objConn->addParameter($atalho->getId());
		}
		else {
			$sql = 'INSERT INTO tb_atalho(ata_nome, ata_fa_icone, ata_controller, ata_action, ata_ativo)
    				VALUES (?, ?, ?, ?, ?)';
			$this->objConn->setSQL($sql);
		}
		
		$this->objConn->addParameter($atalho->getNome());
		$this->objConn->addParameter($atalho->getIcone());
		$this->objConn->addParameter($atalho->getController());
		$this->objConn->addParameter($atalho->getAction());
		$this->objConn->addParameter($atalho->getAtivo());
		
		$returnField = $this->objConn->insert('ata_id');
		// -----------
		
		if (! $this->objConn->getError()) {
			// versao atual da Entidade
			$atalho = new Atalho();
			$objRet = $this->buscaPorId($returnField [0]);
			if ($objRet->isOk()) {
				$atalho = $objRet->getRetByKey('ent');
			}
			// ------------------------
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $atalho);
			$this->objStatus->addRet('msg', 'Atalho incluído com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao inserir Atalho.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 * Valida a insercao.
	 *
	 * @param Atalho $atalho        	
	 * @return ObjStatus
	 */
	private function validaInsere(Atalho $atalho){

		if (strlen($atalho->getNome()) <= 3) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Nome inválido!');
			return $this->objStatus;
		}
		
		if (strlen($atalho->getIcone()) <= 3) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Ícone inválido!');
			return $this->objStatus;
		}
		
		if (strlen($atalho->getController()) <= 2) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Controller inválido!');
			return $this->objStatus;
		}
		
		if (strlen($atalho->getAction()) <= 2) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Action inválida!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 * faz a edicao do Atalho
	 *
	 * @param Atalho $atalho        	
	 * @return ObjStatus
	 */
	public function edita(Atalho $atalho){

		if (! is_a($atalho, 'Atalho')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Variável não é Atalho');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaEdita($atalho);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		$sql = 'UPDATE tb_atalho
   				SET ata_nome = ?, ata_fa_icone = ?, ata_controller = ?, ata_action = ?, ata_ativo = ?
 				WHERE ata_id = ?';
		
		$this->objConn->setSQL($sql);
		
		$this->objConn->addParameter($atalho->getNome());
		$this->objConn->addParameter($atalho->getIcone());
		$this->objConn->addParameter($atalho->getController());
		$this->objConn->addParameter($atalho->getAction());
		$this->objConn->addParameter($atalho->getAtivo());
		$this->objConn->addParameter($atalho->getId());
		
		$returnField = $this->objConn->update();
		// -----------
		
		if (! $this->objConn->getError()) {
			// versao atual da Entidade
			$atalho = new Atalho();
			$objRet = $this->buscaPorId($atalho->getId());
			if ($objRet->isOk()) {
				$atalho = $objRet->getRetByKey('ent');
			}
			// ------------------------
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $atalho);
			$this->objStatus->addRet('msg', 'Atalho editado com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao editar Atalho.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 * Valida a edicao
	 *
	 * @param Atalho $atalho        	
	 * @return ObjStatus
	 */
	private function validaEdita(Atalho $atalho){

		if (! is_numeric($atalho->getId())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'ID inválido!');
			return $this->objStatus;
		}
		
		if (strlen($atalho->getNome()) <= 3) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Nome inválido!');
			return $this->objStatus;
		}
		
		if (strlen($atalho->getIcone()) <= 3) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Ícone inválido!');
			return $this->objStatus;
		}
		
		if (strlen($atalho->getController()) <= 2) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Controller inválido!');
			return $this->objStatus;
		}
		
		if (strlen($atalho->getAction()) <= 2) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Action inválida!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 *
	 * @param Atalho $atalho        	
	 * @return ObjStatus
	 */
	public function deleta(Atalho $atalho){

		if (! is_a($atalho, 'Atalho')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Variável não é Atalho');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaDeleta($atalho);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		$sql = 'DELETE FROM tb_atalho
 				WHERE ata_id = ?';
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($atalho->getId());
		
		$qtLinhas = $this->objConn->delete();
		// -----------
		
		if (! $this->objConn->getError()) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $atalho);
			$this->objStatus->addRet('msg', 'Atalho deletado com sucesso!');
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
	 * @param Atalho $atalho        	
	 * @return ObjStatus
	 */
	private function validaDeleta(Atalho $atalho){

		if (! is_numeric($atalho->getId())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'ID inválida!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

}
?>