<?php
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/connection/Connection.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/obj-status/ObjStatus.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-general/Session.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/entities/Conta.entity.php';

class ContaService{

	private $objStatus;

	private $objConn;
	
	private $userLog;

	function __construct(){
		
		$Session = new Session();
		$this->userLog = $Session->getLoggedUserId();

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
			$this->objStatus->addRet('msg', 'id inv&aacute;lido para busca!');
			return $this->objStatus;
		}
		
		$sql = 'SELECT con_id, con_usu_id, con_nome, con_saldo_inicial, con_cor, con_ativo
  					FROM tb_conta
						WHERE con_id = ?
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
			$con_id = $rs ['con_id'];
			$con_usu_id = $rs ['con_usu_id'];
			$con_nome = $rs ['con_nome'];
			$con_saldo_inicial = $rs ['con_saldo_inicial'];
			$con_cor = $rs ['con_cor'];
			$con_ativo = $rs ['con_ativo'];
			
			require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/Usuario.service.php';
			$UsuarioServ = new UsuarioService();
			$objResp = $UsuarioServ->buscaPorId($con_usu_id);
			
			$conta = new Conta($con_id, $objResp->getRetByKey("ent"), $con_nome, $con_saldo_inicial, $con_cor, $con_ativo);
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $conta);
			
			return $this->objStatus;
		}
	
	}

	/**
	 * Insere o Conta no BD.
	 * Chaves resposta: msg, ent
	 *
	 * @param Conta $conta        	
	 * @return ObjStatus
	 */
	public function insere(Conta $conta){

		if (! is_a($conta, 'Conta')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Vari&aacute;vel n&atilde;o &eacute; Conta');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaInsere($conta);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		// prepara SQL
		if (is_numeric($conta->getId())) {
			$sql = 'INSERT INTO tb_conta(con_id, con_usu_id, con_nome, con_saldo_inicial, con_cor, con_ativo)
    				VALUES (?, ?, ?, ?, ?, ?)';
			$this->objConn->setSQL($sql);
			$this->objConn->addParameter($conta->getId());
		}
		else {
			$sql = 'INSERT INTO tb_conta(con_usu_id, con_nome, con_saldo_inicial, con_cor, con_ativo)
    				VALUES (?, ?, ?, ?, ?)';
			$this->objConn->setSQL($sql);
		}
		
		$this->objConn->addParameter($conta->getUsuario()->getId());
		$this->objConn->addParameter($conta->getNome());
		$this->objConn->addParameter($conta->getSaldoInicial());
		$this->objConn->addParameter($conta->getCor());
		$this->objConn->addParameter($conta->getAtivo());
		
		$returnField = $this->objConn->insert('con_id');
		// -----------
		
		if (! $this->objConn->getError()) {
			// versao atual da Entidade
			$conta = new Conta();
			$objRet = $this->buscaPorId($returnField [0]);
			if ($objRet->isOk()) {
				$conta = $objRet->getRetByKey('ent');
			}
			// ------------------------
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $conta);
			$this->objStatus->addRet('msg', 'Conta inclu&iacute;da com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao inserir Conta.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 * Valida a insercao.
	 *
	 * @param Conta $conta        	
	 * @return ObjStatus
	 */
	private function validaInsere(Conta $conta){
		
		if($this->userLog != $conta->getUsuario()->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para inserir essa conta!');
			return $this->objStatus;
		}

		if (!is_a($conta->getUsuario(), 'Usuario')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido!');
			return $this->objStatus;
		}
		
		if (strlen($conta->getNome()) <= 3) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Nome inv&aacute;lido!');
			return $this->objStatus;
		}
		
		if (!is_numeric($conta->getSaldoInicial())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Saldo inicial inv&aacute;lido!');
			return $this->objStatus;
		}
		
		if (strlen($conta->getCor()) != 6) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Cor inv&aacute;lida!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 * faz a edicao do Conta
	 *
	 * @param Conta $conta        	
	 * @return ObjStatus
	 */
	public function edita(Conta $conta){

		if (! is_a($conta, 'Conta')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Vari&aacute;vel n&atilde;o &eacute; Conta');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaEdita($conta);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		$sql = 'UPDATE tb_conta
   					SET con_usu_id = ?, con_nome = ?, con_saldo_inicial = ?, con_cor = ?, con_ativo = ?
 						WHERE con_id = ?';
		
		$this->objConn->setSQL($sql);
		
		$this->objConn->addParameter($conta->getUsuario()->getId());
		$this->objConn->addParameter($conta->getNome());
		$this->objConn->addParameter($conta->getSaldoInicial());
		$this->objConn->addParameter($conta->getCor());
		$this->objConn->addParameter($conta->getAtivo());
		$this->objConn->addParameter($conta->getId());
		
		$returnField = $this->objConn->update();
		// -----------
		
		if (! $this->objConn->getError()) {
			// versao atual da Entidade
			$conta = new Conta();
			$objRet = $this->buscaPorId($conta->getId());
			if ($objRet->isOk()) {
				$conta = $objRet->getRetByKey('ent');
			}
			// ------------------------
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $conta);
			$this->objStatus->addRet('msg', 'Conta editada com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao editar Conta.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 * Valida a edicao
	 *
	 * @param Conta $conta        	
	 * @return ObjStatus
	 */
	private function validaEdita(Conta $conta){
		
		if($this->userLog != $conta->getUsuario()->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para editar essa conta!');
			return $this->objStatus;
		}
		
		if (!is_numeric($conta->getId())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'ID inv&aacute;lido!');
			return $this->objStatus;
		}

		if (!is_a($conta->getUsuario(), 'Usuario')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido!');
			return $this->objStatus;
		}
		
		if (strlen($conta->getNome()) <= 3) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Nome inv&aacute;lido!');
			return $this->objStatus;
		}
		
		if (!is_numeric($conta->getSaldoInicial())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Saldo inicial inv&aacute;lido!');
			return $this->objStatus;
		}
		
		if (strlen($conta->getCor()) != 6) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Cor inv&aacute;lida!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 *
	 * @param Conta $conta
	 * @return ObjStatus
	 */
	public function deleta(Conta $conta){

		if (! is_a($conta, 'Conta')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Vari&aacute;vel n&atilde;o &eacute; Conta');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaDeleta($conta);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		$sql = 'DELETE FROM tb_conta
 						WHERE con_id = ?';
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($conta->getId());
		
		$qtLinhas = $this->objConn->delete();
		// -----------
		
		if (! $this->objConn->getError()) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $conta);
			$this->objStatus->addRet('msg', 'Conta deletada com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao deletar Conta.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 *
	 * @param Conta $conta        	
	 * @return ObjStatus
	 */
	private function validaDeleta(Conta $conta){
		
		if($this->userLog != $conta->getUsuario()->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para deletar essa conta!');
			return $this->objStatus;
		}

		if (! is_numeric($conta->getId())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'ID inv&aacute;lida!');
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
	public function pegaTodos($userId){
		if(!is_numeric($userId)){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Id do usu&aacute;rio inv&aacute;lido.');
			return $this->objStatus;
		}
		
		if($this->userLog != $userId){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para buscar as conta!');
			return $this->objStatus;
		}
		
		$sql = "SELECT *
						FROM tb_conta
						WHERE con_usu_id = ?
						AND con_ativo = TRUE
						ORDER BY con_nome";
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($userId);
		$resp = $this->objConn->executeSQL("ARRAY");
		
		if($this->objConn->getError()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao buscar contas.');
			return $this->objStatus;
		}
		else{
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('arr_contas', $resp);
			return $this->objStatus;
		}
	}

	/**
	 * 
	 * @param Usuario $Usuario
	 * @param string $valor
	 * @return string
	 */
	public function getHtmlCbContas(Usuario $Usuario, $nome="cb_conta", $valor=""){
		if($this->userLog != $Usuario->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para buscar HTML das contas!');
			return $this->objStatus;
		}
		
		$sql = "SELECT  con_id
								,con_nome
						FROM tb_conta
						WHERE con_ativo = true
						AND con_usu_id = ?
						ORDER BY con_nome";
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($Usuario->getId());
		$rs = $this->objConn->select();
		
		if(count($rs) > 0){
			$html = "";
			$html .= "<select class='form-control' name='$nome' id='$nome'>
						  <option value=''></option>";
			foreach($rs as $conta){
				$con_id = $conta["con_id"];
				$con_nome = $conta["con_nome"];
				$selctd = ($con_id == $valor) ? " selected ": "";
				
				$html .= "<option $selctd value='$con_id'>$con_nome</option>";
			}
			
			$html .= "</select>";
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('msg', '');
			$this->objStatus->addRet('html_cb_contas', $html);
			return $this->objStatus;
		}
		else{
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Nenhuma conta encontrada');
			$this->objStatus->addRet('html_cb_contas', '');
			return $this->objStatus;
		}
	}
}
?>