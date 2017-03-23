<?php
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/connection/Connection.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/obj-status/ObjStatus.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/entities/MovimentacaoTipo.entity.php';

class MovimentacaoTipoService{

	private $objStatus;

	private $objConn;
	
	const ID_RECEITA = 1;
	
	const ID_DESPESA = 2;

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
		
		$sql = 'SELECT mt_id, mt_descricao, mt_ativo
  				FROM tb_movimentacao_tipo
				WHERE mt_id = ?';
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($id);
		$rs = $this->objConn->selectRow();
		
		if (! count($rs) > 0) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Nenhum registro encontrado com esse id (' . $id . ')');
			return $this->objStatus;
		}
		else {
			$mt_id = $rs ['mt_id'];
			$mt_descricao = $rs ['mt_descricao'];
			$mt_ativo = $rs ['mt_ativo'];
			
			$movimentacaoTipo = new MovimentacaoTipo($mt_id, $mt_descricao, $mt_ativo);
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $movimentacaoTipo);
			return $this->objStatus;
		}
	
	}

	/**
	 * 
	 * @return ObjStatus
	 */
	public function pegaReceita(){
		return $this->buscaPorId(MovimentacaoTipoService::ID_RECEITA);
	}
	
	/**
	 * 
	 * @return ObjStatus
	 */
	public function pegaDespesa(){
		return $this->buscaPorId(MovimentacaoTipoService::ID_DESPESA);
	}
	
	/**
	 * Insere o MovimentacaoTipo no BD.
	 * Chaves resposta: msg, ent
	 *
	 * @param MovimentacaoTipo $movimentacaoTipo        	
	 * @return ObjStatus
	 */
	public function insere(MovimentacaoTipo $movimentacaoTipo){

		if (! is_a($movimentacaoTipo, 'MovimentacaoTipo')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Variável não é um Tipo de Movimentação');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaInsere($movimentacaoTipo);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		// prepara SQL
		if (is_numeric($movimentacaoTipo->getId())) {
			$sql = 'INSERT INTO tb_movimentacao_tipo(mt_id, mt_descricao, mt_ativo)
    				VALUES (?, ?, ?)';
			$this->objConn->setSQL($sql);
			$this->objConn->addParameter($movimentacaoTipo->getId());
		}
		else {
			$sql = 'INSERT INTO tb_movimentacao_tipo(mt_descricao, mt_ativo)
    				VALUES (?, ?)';
			$this->objConn->setSQL($sql);
		}
		
		$this->objConn->addParameter($movimentacaoTipo->getDescricao());
		$this->objConn->addParameter($movimentacaoTipo->getAtivo());
		
		$returnField = $this->objConn->insert('mt_id');
		// -----------
		
		if (! $this->objConn->getError()) {
			// versao atual da Entidade
			$movimentacaoTipo = new MovimentacaoTipo();
			$objRet = $this->buscaPorId($returnField [0]);
			if ($objRet->isOk()) {
				$movimentacaoTipo = $objRet->getRetByKey('ent');
			}
			// ------------------------
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $movimentacaoTipo);
			$this->objStatus->addRet('msg', 'Tipo de Movimentação incluída com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao inserir Tipo de Movimentação.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 * Valida a insercao.
	 *
	 * @param MovimentacaoTipo $movimentacaoTipo        	
	 * @return ObjStatus
	 */
	private function validaInsere(MovimentacaoTipo $movimentacaoTipo){

		if (strlen($movimentacaoTipo->getDescricao()) <= 3) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Descrição inválida!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 * faz a edicao do MovimentacaoTipo
	 *
	 * @param MovimentacaoTipo $movimentacaoTipo        	
	 * @return ObjStatus
	 */
	public function edita(MovimentacaoTipo $movimentacaoTipo){

		if (! is_a($movimentacaoTipo, 'MovimentacaoTipo')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Variável não é um Tipo de Movimentação');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaEdita($movimentacaoTipo);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		$sql = 'UPDATE tb_movimentacao_tipo
   				SET mt_descricao = ?, mt_ativo = ?
 				WHERE mt_id = ?';
		
		$this->objConn->setSQL($sql);
		
		$this->objConn->addParameter($movimentacaoTipo->getDescricao());
		$this->objConn->addParameter($movimentacaoTipo->getAtivo());
		$this->objConn->addParameter($movimentacaoTipo->getId());
		
		$returnField = $this->objConn->update();
		// -----------
		
		if (! $this->objConn->getError()) {
			// versao atual da Entidade
			$movimentacaoTipo = new MovimentacaoTipo();
			$objRet = $this->buscaPorId($movimentacaoTipo->getId());
			if ($objRet->isOk()) {
				$movimentacaoTipo = $objRet->getRetByKey('ent');
			}
			// ------------------------
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $movimentacaoTipo);
			$this->objStatus->addRet('msg', 'Tipo de Movimentação editada com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao editar MovimentacaoTipo.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 * Valida a edicao
	 *
	 * @param MovimentacaoTipo $movimentacaoTipo        	
	 * @return ObjStatus
	 */
	private function validaEdita(MovimentacaoTipo $movimentacaoTipo){

		if (! is_numeric($movimentacaoTipo->getId())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'ID inválido!');
			return $this->objStatus;
		}
		
		if (strlen($movimentacaoTipo->getDescricao()) <= 3) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Descrição inválida!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 *
	 * @param MovimentacaoTipo $movimentacaoTipo        	
	 * @return ObjStatus
	 */
	public function deleta(MovimentacaoTipo $movimentacaoTipo){

		if (! is_a($movimentacaoTipo, 'MovimentacaoTipo')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Variável não é Tipo de Movimentação');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaDeleta($movimentacaoTipo);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		$sql = 'DELETE FROM tb_movimentacao_tipo
 				WHERE mt_id = ?';
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($movimentacaoTipo->getId());
		
		$qtLinhas = $this->objConn->delete();
		// -----------
		
		if (! $this->objConn->getError()) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $movimentacaoTipo);
			$this->objStatus->addRet('msg', 'Tipo de Movimentação deletado com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao deletar Tipo de Movimentação.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 *
	 * @param MovimentacaoTipo $movimentacaoTipo        	
	 * @return ObjStatus
	 */
	private function validaDeleta(MovimentacaoTipo $movimentacaoTipo){

		if (! is_numeric($movimentacaoTipo->getId())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'ID inválida!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}
	
	/**
	 *
	 * @param integer $userId
	 * @return ObjStatus
	 */
	public function pegaTodos(){	
		$sql = "SELECT *
				FROM tb_movimentacao_tipo
				WHERE mt_ativo = TRUE
				ORDER BY mt_descricao";
		$this->objConn->setSQL($sql);
		$resp = $this->objConn->executeSQL("ARRAY");
	
		if($this->objConn->getError()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao buscar tipo de movimenta&ccedil;&atilde;o.');
			return $this->objStatus;
		}
		else{
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('arr_mov_tipo', $resp);
			return $this->objStatus;
		}
	}

}
?>