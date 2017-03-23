<?php
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/connection/Connection.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/obj-status/ObjStatus.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-general/Session.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/entities/Projeto.entity.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/Usuario.service.php';

class ProjetoService{

	private $objStatus;

	private $objConn;
	
	private $objUsuarioServ;
	
	private $userLog;

	function __construct(){
		
		$Session = new Session();
		$this->userLog = $Session->getLoggedUserId();

		$this->objStatus = new ObjStatus();
		$this->objConn = new Connection();
		$this->objUsuarioServ = new UsuarioService();
	
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
		
		$sql = 'SELECT pro_id, pro_descricao, pro_usu_id, pro_finalizado, pro_observacao, pro_deletado
  					FROM tb_projeto
						WHERE pro_id = ?
						AND pro_usu_id = ?';
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
			$pro_id = $rs ['pro_id'];
			$pro_descricao = $rs ['pro_descricao'];
			$pro_usu_id = $rs ['pro_usu_id'];
			$pro_finalizado = $rs ['pro_finalizado'];
			$pro_observacao = $rs ['pro_observacao'];
			$pro_deletado = $rs ['pro_deletado'];
			
			// entidade usuario
			$obj_usuario = new Usuario();
			$objStatus = $this->objUsuarioServ->buscaPorId($pro_usu_id);
			if($objStatus->isOk()){
				$obj_usuario = $objStatus->getRetByKey('ent');
			}
			// ----------------
			
			$projeto = new Projeto($pro_id, $pro_descricao, $obj_usuario, $pro_finalizado, $pro_observacao, $pro_deletado);
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $projeto);
			return $this->objStatus;
		}
	
	}

	/**
	 * Insere o Projeto no BD.
	 * Chaves resposta: msg, ent
	 *
	 * @param Projeto $projeto        	
	 * @return ObjStatus
	 */
	public function insere(Projeto $projeto){

		if (! is_a($projeto, 'Projeto')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Variável não é Projeto');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaInsere($projeto);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		// prepara SQL
		if (is_numeric($projeto->getId())) {
			$sql = 'INSERT INTO tb_projeto(pro_id, pro_descricao, pro_usu_id, pro_finalizado, pro_observacao, pro_deletado)
    				VALUES (?, ?, ?, ?, ?, ?)';
			$this->objConn->setSQL($sql);
			$this->objConn->addParameter($projeto->getId());
		}
		else {
			$sql = 'INSERT INTO tb_projeto(pro_descricao, pro_usu_id, pro_finalizado, pro_observacao, pro_deletado)
    				VALUES (?, ?, ?, ?, ?)';
			$this->objConn->setSQL($sql);
		}
		
		$this->objConn->addParameter($projeto->getDescricao());
		$this->objConn->addParameter($projeto->getUsuario()->getId());
		$this->objConn->addParameter($projeto->getFinalizado());
		$this->objConn->addParameter($projeto->getObservacao());
		$this->objConn->addParameter($projeto->getDeletado());
		
		$returnField = $this->objConn->insert('pro_id');
		// -----------
		
		if (! $this->objConn->getError()) {
			// versao atual da Entidade
			$projeto = new Projeto();
			$objRet = $this->buscaPorId($returnField [0]);
			if ($objRet->isOk()) {
				$projeto = $objRet->getRetByKey('ent');
			}
			// ------------------------
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $projeto);
			$this->objStatus->addRet('msg', 'Projeto incluído com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao inserir Projeto.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 * Valida a insercao.
	 *
	 * @param Projeto $projeto        	
	 * @return ObjStatus
	 */
	private function validaInsere(Projeto $projeto){
		
		if($this->userLog != $projeto->getUsuario()->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para inserir esse projeto!');
			return $this->objStatus;
		}

		if (strlen($projeto->getDescricao()) <= 3) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Descrição inválida!');
			return $this->objStatus;
		}
		
		if (!is_a($projeto->getUsuario(), 'Usuario')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usuário inválido!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 * faz a edicao do Projeto
	 *
	 * @param Projeto $projeto        	
	 * @return ObjStatus
	 */
	public function edita(Projeto $projeto){

		if (! is_a($projeto, 'Projeto')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Variável não é Projeto');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaEdita($projeto);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		$sql = 'UPDATE tb_projeto
   					SET pro_descricao = ?, pro_usu_id = ?, pro_finalizado = ?, pro_observacao = ?, pro_deletado = ?
 						WHERE pro_id = ?';
		
		$this->objConn->setSQL($sql);
		
		$this->objConn->addParameter($projeto->getDescricao());
		$this->objConn->addParameter($projeto->getUsuario()->getId());
		$this->objConn->addParameter($projeto->getFinalizado());
		$this->objConn->addParameter($projeto->getObservacao());
		$this->objConn->addParameter($projeto->getDeletado());
		$this->objConn->addParameter($projeto->getId());
		
		$returnField = $this->objConn->update();
		// -----------
		
		if (! $this->objConn->getError()) {
			// versao atual da Entidade
			$projeto = new Projeto();
			$objRet = $this->buscaPorId($projeto->getId());
			if ($objRet->isOk()) {
				$projeto = $objRet->getRetByKey('ent');
			}
			// ------------------------
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $projeto);
			$this->objStatus->addRet('msg', 'Projeto editado com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao editar Projeto.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 * Valida a edicao
	 *
	 * @param Projeto $projeto        	
	 * @return ObjStatus
	 */
	private function validaEdita(Projeto $projeto){
		
		if($this->userLog != $projeto->getUsuario()->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para alterar esse projeto!');
			return $this->objStatus;
		}

		if (! is_numeric($projeto->getId())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'ID inválido!');
			return $this->objStatus;
		}
		
		if (strlen($projeto->getDescricao()) <= 3) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Descrição inválida!');
			return $this->objStatus;
		}
		
		if (!is_a($projeto->getUsuario(), 'Usuario')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usuário inválido!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 *
	 * @param Projeto $projeto        	
	 * @return ObjStatus
	 */
	public function deleta(Projeto $projeto){

		if (! is_a($projeto, 'Projeto')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Variável não é Projeto');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaDeleta($projeto);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		$sql = 'DELETE FROM tb_projeto
 						WHERE pro_id = ?';
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($projeto->getId());
		
		$qtLinhas = $this->objConn->delete();
		// -----------
		
		if (! $this->objConn->getError()) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $projeto);
			$this->objStatus->addRet('msg', 'Projeto deletado com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao deletar Projeto.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 *
	 * @param Projeto $projeto        	
	 * @return ObjStatus
	 */
	private function validaDeleta(Projeto $projeto){
		
		if($this->userLog != $projeto->getUsuario()->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para deletar esse projeto!');
			return $this->objStatus;
		}

		if (! is_numeric($projeto->getId())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'ID inválida!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

}
?>