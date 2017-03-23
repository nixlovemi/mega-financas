<?php
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/connection/Connection.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/obj-status/ObjStatus.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/entities/BandeiraCartao.entity.php';

class BandeiraCartaoService{

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
		
		$sql = 'SELECT bc_id, bc_descricao, bc_mini_imagem, bc_ativo
  				FROM tb_bandeira_cartao
				WHERE bc_id = ?';
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($id);
		$rs = $this->objConn->selectRow();
		
		if (! count($rs) > 0) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Nenhum registro encontrado com esse id (' . $id . ')');
			return $this->objStatus;
		}
		else {
			$bc_id = $rs ['bc_id'];
			$bc_descricao = $rs ['bc_descricao'];
			$bc_mini_imagem = $rs ['bc_mini_imagem'];
			$bc_ativo = $rs ['bc_ativo'];
			
			$bandeiraCartao = new BandeiraCartao($bc_id, $bc_descricao, $bc_mini_imagem, $bc_ativo);
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $bandeiraCartao);
			return $this->objStatus;
		}
	
	}

	/**
	 * Insere o BandeiraCartao no BD.
	 * Chaves resposta: msg, ent
	 *
	 * @param BandeiraCartao $bandeiraCartao        	
	 * @return ObjStatus
	 */
	public function insere(BandeiraCartao $bandeiraCartao){

		if (! is_a($bandeiraCartao, 'BandeiraCartao')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Variável não é BandeiraCartao');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaInsere($bandeiraCartao);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		// prepara SQL
		if (is_numeric($bandeiraCartao->getId())) {
			$sql = 'INSERT INTO tb_bandeira_cartao(bc_id, bc_descricao, bc_mini_imagem, bc_ativo)
    				VALUES (?, ?, ?, ?)';
			$this->objConn->setSQL($sql);
			$this->objConn->addParameter($bandeiraCartao->getId());
		}
		else {
			$sql = 'INSERT INTO tb_bandeira_cartao(bc_descricao, bc_mini_imagem, bc_ativo)
    				VALUES (?, ?, ?)';
			$this->objConn->setSQL($sql);
		}
		
		$this->objConn->addParameter($bandeiraCartao->getDescricao());
		$this->objConn->addParameter($bandeiraCartao->getMiniImagem());
		$this->objConn->addParameter($bandeiraCartao->getAtivo());
		
		$returnField = $this->objConn->insert('bc_id');
		// -----------
		
		if (! $this->objConn->getError()) {
			// versao atual da Entidade
			$bandeiraCartao = new BandeiraCartao();
			$objRet = $this->buscaPorId($returnField [0]);
			if ($objRet->isOk()) {
				$bandeiraCartao = $objRet->getRetByKey('ent');
			}
			// ------------------------
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $bandeiraCartao);
			$this->objStatus->addRet('msg', 'Bandeira do Cartão incluído com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao inserir Bandeira do Cartão.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 * Valida a insercao.
	 *
	 * @param BandeiraCartao $bandeiraCartao        	
	 * @return ObjStatus
	 */
	private function validaInsere(BandeiraCartao $bandeiraCartao){

		if (strlen($bandeiraCartao->getDescricao()) <= 3) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Descrição inválida!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 * faz a edicao do BandeiraCartao
	 *
	 * @param BandeiraCartao $bandeiraCartao        	
	 * @return ObjStatus
	 */
	public function edita(BandeiraCartao $bandeiraCartao){

		if (! is_a($bandeiraCartao, 'BandeiraCartao')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Variável não é BandeiraCartao');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaEdita($bandeiraCartao);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		$sql = 'UPDATE tb_bandeira_cartao
   				SET bc_descricao = ?, bc_mini_imagem = ?, bc_ativo = ?
 				WHERE bc_id = ?';
		$this->objConn->setSQL($sql);
		
		$this->objConn->addParameter($bandeiraCartao->getDescricao());
		$this->objConn->addParameter($bandeiraCartao->getMiniImagem());
		$this->objConn->addParameter($bandeiraCartao->getAtivo());
		$this->objConn->addParameter($bandeiraCartao->getId());
		
		$returnField = $this->objConn->update();
		// -----------
		
		if (! $this->objConn->getError()) {
			// versao atual da Entidade
			$bandeiraCartao = new BandeiraCartao();
			$objRet = $this->buscaPorId($bandeiraCartao->getId());
			if ($objRet->isOk()) {
				$bandeiraCartao = $objRet->getRetByKey('ent');
			}
			// ------------------------
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $bandeiraCartao);
			$this->objStatus->addRet('msg', 'Bandeira do Cartão editado com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao editar Bandeira do Cartão.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 * Valida a edicao
	 *
	 * @param BandeiraCartao $bandeiraCartao        	
	 * @return ObjStatus
	 */
	private function validaEdita(BandeiraCartao $bandeiraCartao){

		if (! is_numeric($bandeiraCartao->getId())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'ID inválido!');
			return $this->objStatus;
		}
		
		if (strlen($bandeiraCartao->getDescricao()) <= 3) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Nome inválido!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 *
	 * @param BandeiraCartao $bandeiraCartao        	
	 * @return ObjStatus
	 */
	public function deleta(BandeiraCartao $bandeiraCartao){

		if (! is_a($bandeiraCartao, 'BandeiraCartao')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Variável não é BandeiraCartao');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaDeleta($bandeiraCartao);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		$sql = 'DELETE FROM tb_bandeira_cartao
 				WHERE bc_id = ?';
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($bandeiraCartao->getId());
		
		$qtLinhas = $this->objConn->delete();
		// -----------
		
		if (! $this->objConn->getError()) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $bandeiraCartao);
			$this->objStatus->addRet('msg', 'Bandeira do Cartão deletado com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao deletar Bandeira do Cartão.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 *
	 * @param BandeiraCartao $bandeiraCartao        	
	 * @return ObjStatus
	 */
	private function validaDeleta(BandeiraCartao $bandeiraCartao){

		if (! is_numeric($bandeiraCartao->getId())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'ID inválida!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 * 
	 * @return ObjStatus
	 */
	public function pegaTodos(){
		$sql = "SELECT *
				FROM tb_bandeira_cartao
				WHERE bc_ativo = TRUE
				ORDER BY bc_descricao";
		$this->objConn->setSQL($sql);
		$resp = $this->objConn->executeSQL("ARRAY");
		
		if($this->objConn->getError()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao buscar as bandeiras dos cart&otilde;es.');
			return $this->objStatus;
		}
		else{
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('arr_bandeira_cartao', $resp);
			return $this->objStatus;
		}
	}
}
?>