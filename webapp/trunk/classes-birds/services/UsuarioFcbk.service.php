<?php
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/connection/Connection.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/obj-status/ObjStatus.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/Usuario.service.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/entities/UsuarioFcbk.entity.php';

class UsuarioFcbkService{

	private $objStatus;

	private $objConn;
	
	private $objUsuarioServ;

	function __construct(){

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
		
		$sql = 'SELECT uf_id, uf_usu_id, uf_fb_usu_id, uf_fb_prim_nome, uf_fb_sobrenome, uf_fb_nomecompleto, uf_fb_email, uf_fb_sexo, uf_fb_foto
  				FROM tb_usuario_fcbk
				WHERE uf_id = ?';
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($id);
		$rs = $this->objConn->selectRow();
		
		if (! count($rs) > 0) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Nenhum registro encontrado com esse id (' . $id . ')');
			return $this->objStatus;
		}
		else {
			$uf_id = $rs ['uf_id'];
			$uf_usu_id = $rs ['uf_usu_id'];
			$uf_fb_usu_id = $rs ['uf_fb_usu_id'];
			$uf_fb_prim_nome = $rs ['uf_fb_prim_nome'];
			$uf_fb_sobrenome = $rs ['uf_fb_sobrenome'];
			$uf_fb_nomecompleto = $rs ['uf_fb_nomecompleto'];
			$uf_fb_email = $rs ['uf_fb_email'];
			$uf_fb_sexo = $rs ['uf_fb_sexo'];
			$uf_fb_foto = $rs ['uf_fb_foto'];
			
			// entidade usuario
			$obj_usuario = new Usuario();
			$objStatus = $this->objUsuarioServ->buscaPorId($uf_usu_id);
			if($objStatus->isOk()){
				$obj_usuario = $objStatus->getRetByKey('ent');
			}
			// ----------------
			
			$usuarioFcbk = new UsuarioFcbk($uf_id, $obj_usuario, $uf_fb_usu_id, $uf_fb_prim_nome, $uf_fb_sobrenome, $uf_fb_nomecompleto, $uf_fb_email, $uf_fb_sexo, $uf_fb_foto);
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $usuarioFcbk);
			return $this->objStatus;
		}
	
	}

	/**
	 * 
	 * @param Usuario $Usuario
	 * @return ObjStatus
	 */
	public function buscaPorUsuario(Usuario $Usuario){
	
		if (! is_a($Usuario, "Usuario")) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para busca!');
			return $this->objStatus;
		}
	
		$sql = 'SELECT uf_id
  				FROM tb_usuario_fcbk
				WHERE uf_usu_id = ?';
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter( $Usuario->getId() );
		$rs = $this->objConn->selectRow();
	
		if (! count($rs) > 0) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Nenhum registro encontrado com esse usu&aacute;rio (' . $Usuario->getId() . ')');
			return $this->objStatus;
		}
		else {
			$uf_id = $rs ['uf_id'];
			
			$objResp = $this->buscaPorId($uf_id);
			if($objResp->isErro()){
				$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
				$this->objStatus->addRet('msg', 'Nenhum registro encontrado com esse id (' . $uf_id . ')');
				return $this->objStatus;
			}
			else{
				$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
				$this->objStatus->addRet('ent', $objResp->getRetByKey("ent"));
				return $this->objStatus;
			}
		}
	}
	
	/**
	 * Insere o UsuarioFcbk no BD.
	 * Chaves resposta: msg, ent
	 *
	 * @param UsuarioFcbk $usuarioFcbk        	
	 * @return ObjStatus
	 */
	public function insere(UsuarioFcbk $usuarioFcbk){

		if (! is_a($usuarioFcbk, 'UsuarioFcbk')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Variável não é UsuarioFcbk');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaInsere($usuarioFcbk);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		// prepara SQL
		if (is_numeric($usuarioFcbk->getId())) {
			$sql = 'INSERT INTO tb_usuario_fcbk(uf_id, uf_usu_id, uf_fb_usu_id, uf_fb_prim_nome, uf_fb_sobrenome, uf_fb_nomecompleto, uf_fb_email, uf_fb_sexo, uf_fb_foto)
    				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
			$this->objConn->setSQL($sql);
			$this->objConn->addParameter($usuarioFcbk->getId());
		}
		else {
			$sql = 'INSERT INTO tb_usuario_fcbk(uf_usu_id, uf_fb_usu_id, uf_fb_prim_nome, uf_fb_sobrenome, uf_fb_nomecompleto, uf_fb_email, uf_fb_sexo, uf_fb_foto)
    				VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
			$this->objConn->setSQL($sql);
		}
		
		$this->objConn->addParameter($usuarioFcbk->getUsuario()->getId());
		$this->objConn->addParameter($usuarioFcbk->getFbUsuId());
		$this->objConn->addParameter($usuarioFcbk->getFbPrimNome());
		$this->objConn->addParameter($usuarioFcbk->getFbSobrenome());
		$this->objConn->addParameter($usuarioFcbk->getFbNomecompleto());
		$this->objConn->addParameter($usuarioFcbk->getFbEmail());
		$this->objConn->addParameter($usuarioFcbk->getFbSexo());
		$this->objConn->addParameter($usuarioFcbk->getFbFoto());
		
		$returnField = $this->objConn->insert('uf_id');
		// -----------
		
		if (! $this->objConn->getError()) {
			// versao atual da Entidade
			$usuarioFcbk = new UsuarioFcbk();
			$objRet = $this->buscaPorId($returnField [0]);
			if ($objRet->isOk()) {
				$usuarioFcbk = $objRet->getRetByKey('ent');
			}
			// ------------------------
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $usuarioFcbk);
			$this->objStatus->addRet('msg', 'Informação do Facebook incluída com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao inserir Informações do Facebook.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 * Valida a insercao.
	 *
	 * @param UsuarioFcbk $usuarioFcbk        	
	 * @return ObjStatus
	 */
	private function validaInsere(UsuarioFcbk $usuarioFcbk){

		if (!is_numeric($usuarioFcbk->getUsuario()->getId())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usuário inválido!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 * faz a edicao do UsuarioFcbk
	 *
	 * @param UsuarioFcbk $usuarioFcbk        	
	 * @return ObjStatus
	 */
	public function edita(UsuarioFcbk $usuarioFcbk){

		if (! is_a($usuarioFcbk, 'UsuarioFcbk')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Variável não é UsuarioFcbk');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaEdita($usuarioFcbk);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		$sql = 'UPDATE tb_usuario_fcbk
   				SET uf_usu_id = ?, uf_fb_usu_id = ?, uf_fb_prim_nome = ?, uf_fb_sobrenome = ?, uf_fb_nomecompleto = ?, uf_fb_email = ?, uf_fb_sexo = ?, uf_fb_foto = ?
 				WHERE uf_id = ?';
		$this->objConn->setSQL($sql);
		
		$this->objConn->addParameter($usuarioFcbk->getUsuario()->getId());
		$this->objConn->addParameter($usuarioFcbk->getFbUsuId());
		$this->objConn->addParameter($usuarioFcbk->getFbPrimNome());
		$this->objConn->addParameter($usuarioFcbk->getFbSobrenome());
		$this->objConn->addParameter($usuarioFcbk->getFbNomecompleto());
		$this->objConn->addParameter($usuarioFcbk->getFbEmail());
		$this->objConn->addParameter($usuarioFcbk->getFbSexo());
		$this->objConn->addParameter($usuarioFcbk->getFbFoto());
		$this->objConn->addParameter($usuarioFcbk->getId());
		
		$returnField = $this->objConn->update();
		// -----------
		
		if (! $this->objConn->getError()) {
			// versao atual da Entidade
			$usuarioFcbk = new UsuarioFcbk();
			$objRet = $this->buscaPorId($usuarioFcbk->getId());
			if ($objRet->isOk()) {
				$usuarioFcbk = $objRet->getRetByKey('ent');
			}
			// ------------------------
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $usuarioFcbk);
			$this->objStatus->addRet('msg', 'Informações do Facebook editada com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao editar Informações do Facebook.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 * Valida a edicao
	 *
	 * @param UsuarioFcbk $usuarioFcbk        	
	 * @return ObjStatus
	 */
	private function validaEdita(UsuarioFcbk $usuarioFcbk){

		if (! is_numeric($usuarioFcbk->getId())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'ID inválido!');
			return $this->objStatus;
		}
		
		if (! is_numeric($usuarioFcbk->getUsuario()->getId())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usuário inválido!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 *
	 * @param UsuarioFcbk $usuarioFcbk        	
	 * @return ObjStatus
	 */
	public function deleta(UsuarioFcbk $usuarioFcbk){

		if (! is_a($usuarioFcbk, 'UsuarioFcbk')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Variável não é UsuarioFcbk');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaDeleta($usuarioFcbk);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		$sql = 'DELETE FROM tb_usuario_fcbk
 				WHERE uf_id = ?';
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($usuarioFcbk->getId());
		
		$qtLinhas = $this->objConn->delete();
		// -----------
		
		if (! $this->objConn->getError()) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $usuarioFcbk);
			$this->objStatus->addRet('msg', 'Informações do Facebook deletado com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao deletar UsuarioFcbk.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 *
	 * @param UsuarioFcbk $usuarioFcbk        	
	 * @return ObjStatus
	 */
	private function validaDeleta(UsuarioFcbk $usuarioFcbk){

		if (! is_numeric($usuarioFcbk->getId())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'ID inválida!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 * 
	 * @param UsuarioFcbk $UsuarioFcbk
	 */
	public function loginFcbk(UsuarioFcbk $UsuarioFcbk){
		require_once $_SERVER ['BIRDS_HOME'] . 'classes-general/Validation.class.php';
		$Validation = new Validation();
		
		// validacoes
		$eh_email_valido = $Validation->validateEmail( $UsuarioFcbk->getFbEmail() );
		$eh_nome_valido = ( $UsuarioFcbk->getFbPrimNome() != "" );
		// ==========
		
		if(!$eh_email_valido || !$eh_nome_valido){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'N&atilde;o conseguimos carregar as informa&ccedil;&otilde;es do Facebook para fazer o login.');
			return $this->objStatus;
		}
		
		// tenta cadastrar o user usando as info do facebook
		require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/entities/Usuario.entity.php';
		$Usuario = new Usuario();
		$Usuario->setCadLiberado(TRUE);
		$Usuario->setEmail( $UsuarioFcbk->getFbEmail() );
		$Usuario->setNome( $UsuarioFcbk->getFbPrimNome() );
		$Usuario->setSobrenome( $UsuarioFcbk->getFbSobrenome() );
		$Usuario->setSenha( md5(rand()) );
		
		require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/Usuario.service.php';
		$UsuarioServ = new UsuarioService();
		$objResp = $UsuarioServ->insere($Usuario);
		
		if($objResp->isOk()){
			$Usuario = $objResp->getRetByKey("ent");
		}
		else{
			$objResp = $UsuarioServ->buscaPorEmail($UsuarioFcbk->getFbEmail());
			
			if($objResp->isErro()){
				$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
				$this->objStatus->addRet('msg', 'N&atilde;o foi poss&iacute;vel carregar as informa&ccedil;&otilde;es do Facebook para fazer o login.');
				return $this->objStatus;
			}
			else{
				$Usuario = $objResp->getRetByKey("ent");
			}
		}
		// =================================================
		
		// tenta cadastrar a info do facebook
		$UsuarioFcbk->setUsuario($Usuario);
		$objResp = $this->insere($UsuarioFcbk);
		
		if($objResp->isErro()){
			$objResp = $this->buscaPorUsuario($Usuario);
			if($objResp->isOk()){
				$UsuarioFcbk->setId( $objResp->getRetByKey("ent")->getId() );
			}
		}
		else{
			$UsuarioFcbk->setId( $objResp->getRetByKey("ent")->getId() );
		}
		
		$objResp = $this->edita($UsuarioFcbk);
		// ==================================
		
		// se chegou ate aqui, td ok
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		$this->objStatus->addRet('UsuarioFcbk', $UsuarioFcbk);
		return $this->objStatus;
	}
}
?>