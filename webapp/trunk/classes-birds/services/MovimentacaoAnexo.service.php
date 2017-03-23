<?php
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/connection/Connection.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/obj-status/ObjStatus.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-general/Session.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/entities/MovimentacaoAnexo.entity.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/Movimentacao.service.php';

class MovimentacaoAnexoService{

	private $objStatus;

	private $objConn;
	
	private $objMovimentacaoServ;
	
	private $userLog;

	function __construct(){
		
		$Session = new Session();
		$this->userLog = $Session->getLoggedUserId();

		$this->objStatus = new ObjStatus();
		$this->objConn = new Connection();
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
		
		$sql = 'SELECT ma_id, ma_mov_id, ma_arquivo
  					FROM tb_movimentacao_anexo
						INNER JOIN tb_movimentacao ON mov_id = ma_mov_id
						INNER JOIN tb_conta ON con_id = mov_con_id
						WHERE ma_id = ?
						AND con_usu_id = ?';
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
			$ma_id = $rs ['ma_id'];
			$ma_mov_id = $rs ['ma_mov_id'];
			$ma_arquivo = $rs ['ma_arquivo'];
			
			// entidade movimentacao
			$obj_movimentacao = new Movimentacao();
			$objStatus = $this->objMovimentacaoServ->buscaPorId($ma_mov_id);
			if($objStatus->isOk()){
				$obj_movimentacao = $objStatus->getRetByKey('ent');
			}
			// ---------------------
			
			$movimentacaoAnexo = new MovimentacaoAnexo($ma_id, $obj_movimentacao, $ma_arquivo);
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $movimentacaoAnexo);
			return $this->objStatus;
		}
	
	}

	/**
	 * Insere o MovimentacaoAnexo no BD.
	 * Chaves resposta: msg, ent
	 *
	 * @param MovimentacaoAnexo $movimentacaoAnexo        	
	 * @return ObjStatus
	 */
	public function insere(MovimentacaoAnexo $movimentacaoAnexo){

		if (! is_a($movimentacaoAnexo, 'MovimentacaoAnexo')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Variável não é Movimentação Anexo');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaInsere($movimentacaoAnexo);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		// prepara SQL
		if (is_numeric($movimentacaoAnexo->getId())) {
			$sql = 'INSERT INTO tb_movimentacao_anexo(ma_id, ma_mov_id, ma_arquivo)
    				VALUES (?, ?, ?)';
			$this->objConn->setSQL($sql);
			$this->objConn->addParameter($movimentacaoAnexo->getId());
		}
		else {
			$sql = 'INSERT INTO tb_movimentacao_anexo(ma_mov_id, ma_arquivo)
    				VALUES (?, ?)';
			$this->objConn->setSQL($sql);
		}
		
		$this->objConn->addParameter($movimentacaoAnexo->getMovimentacao()->getId());
		$this->objConn->addParameter($movimentacaoAnexo->getArquivo());
		
		$returnField = $this->objConn->insert('ma_id');
		// -----------
		
		if (! $this->objConn->getError()) {
			// versao atual da Entidade
			$movimentacaoAnexo = new MovimentacaoAnexo();
			$objRet = $this->buscaPorId($returnField [0]);
			if ($objRet->isOk()) {
				$movimentacaoAnexo = $objRet->getRetByKey('ent');
			}
			// ------------------------
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $movimentacaoAnexo);
			$this->objStatus->addRet('msg', 'Anexo incluído com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao inserir Movimentação Anexo.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 * Valida a insercao.
	 *
	 * @param MovimentacaoAnexo $movimentacaoAnexo        	
	 * @return ObjStatus
	 */
	private function validaInsere(MovimentacaoAnexo $movimentacaoAnexo){

		if($this->userLog != $movimentacaoAnexo->getMovimentacao()->getConta()->getUsuario()->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para inserir esse anexo!');
			return $this->objStatus;
		}
		
		if (!is_a($movimentacaoAnexo->getMovimentacao(), 'Movimentacao')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Movimentação inválida!');
			return $this->objStatus;
		}
		
		if (strlen($movimentacaoAnexo->getArquivo()) < 10) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Arquivo inválido!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 * faz a edicao do MovimentacaoAnexo
	 *
	 * @param MovimentacaoAnexo $movimentacaoAnexo        	
	 * @return ObjStatus
	 */
	public function edita(MovimentacaoAnexo $movimentacaoAnexo){

		if (! is_a($movimentacaoAnexo, 'MovimentacaoAnexo')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Variável não é Movimentação Anexo');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaEdita($movimentacaoAnexo);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		$sql = 'UPDATE tb_movimentacao_anexo
   					SET ma_mov_id = ?, ma_arquivo = ?
 						WHERE ma_id = ?';
		
		$this->objConn->setSQL($sql);
		
		$this->objConn->addParameter($movimentacaoAnexo->getMovimentacao()->getId());
		$this->objConn->addParameter($movimentacaoAnexo->getArquivo());
		$this->objConn->addParameter($movimentacaoAnexo->getId());
		
		$returnField = $this->objConn->update();
		// -----------
		
		if (! $this->objConn->getError()) {
			// versao atual da Entidade
			$movimentacaoAnexo = new MovimentacaoAnexo();
			$objRet = $this->buscaPorId($movimentacaoAnexo->getId());
			if ($objRet->isOk()) {
				$movimentacaoAnexo = $objRet->getRetByKey('ent');
			}
			// ------------------------
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $movimentacaoAnexo);
			$this->objStatus->addRet('msg', 'Anexo editado com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao editar Movimentação Anexo.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 * Valida a edicao
	 *
	 * @param MovimentacaoAnexo $movimentacaoAnexo        	
	 * @return ObjStatus
	 */
	private function validaEdita(MovimentacaoAnexo $movimentacaoAnexo){

		if($this->userLog != $movimentacaoAnexo->getMovimentacao()->getConta()->getUsuario()->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para editar esse anexo!');
			return $this->objStatus;
		}
		
		if (! is_numeric($movimentacaoAnexo->getId())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'ID inválido!');
			return $this->objStatus;
		}
		
		if (!is_a($movimentacaoAnexo->getMovimentacao(), 'Movimentacao')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Movimentação inválida!');
			return $this->objStatus;
		}
		
		if (strlen($movimentacaoAnexo->getArquivo()) < 10) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Arquivo inválido!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 *
	 * @param MovimentacaoAnexo $movimentacaoAnexo        	
	 * @return ObjStatus
	 */
	public function deleta(MovimentacaoAnexo $movimentacaoAnexo){

		if (! is_a($movimentacaoAnexo, 'MovimentacaoAnexo')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Variável não é Movimentação Anexo');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaDeleta($movimentacaoAnexo);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		$sql = 'DELETE FROM tb_movimentacao_anexo
 						WHERE ma_id = ?';
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($movimentacaoAnexo->getId());
		
		$qtLinhas = $this->objConn->delete();
		// -----------
		
		if (! $this->objConn->getError()) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $movimentacaoAnexo);
			$this->objStatus->addRet('msg', 'Anexo deletado com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao deletar Movimentação Anexo.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 *
	 * @param MovimentacaoAnexo $movimentacaoAnexo        	
	 * @return ObjStatus
	 */
	private function validaDeleta(MovimentacaoAnexo $movimentacaoAnexo){

		if($this->userLog != $movimentacaoAnexo->getMovimentacao()->getConta()->getUsuario()->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para deletar esse anexo!');
			return $this->objStatus;
		}
		
		if (! is_numeric($movimentacaoAnexo->getId())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'ID inválida!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

}
?>