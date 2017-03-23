<?php
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/connection/Connection.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/obj-status/ObjStatus.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-general/Session.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/entities/MovimentacaoCat.entity.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/Usuario.service.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/MovimentacaoTipo.service.php';

class MovimentacaoCatService{

	private $objStatus;

	private $objConn;
	
	private $objUsuarioServ;
	
	private $objMovimentacaoTipoServ;
	
	private $userLog;

	function __construct(){
		
		$Session = new Session();
		$this->userLog = $Session->getLoggedUserId();

		$this->objStatus = new ObjStatus();
		$this->objConn = new Connection();
		$this->objUsuarioServ = new UsuarioService();
		$this->objMovimentacaoTipoServ = new MovimentacaoTipoService();
	
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
		
		$sql = 'SELECT mc_id, mc_usu_id, mc_descricao, mc_ativo, mc_id_pai, mc_mt_id
  					FROM tb_movimentacao_cat
						WHERE mc_id = ?
						AND mc_usu_id = ?';
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
			$mc_id = $rs ['mc_id'];
			$mc_usu_id = $rs ['mc_usu_id'];
			$mc_descricao = $rs ['mc_descricao'];
			$mc_ativo = $rs ['mc_ativo'];
			$mc_id_pai = $rs ['mc_id_pai'];
			$mc_mt_id = $rs ['mc_mt_id'];
			
			// entidade usuario
			$obj_usuario = new Usuario();
			$objStatus = $this->objUsuarioServ->buscaPorId($mc_usu_id);
			if($objStatus->isOk()){
				$obj_usuario = $objStatus->getRetByKey('ent');
			}
			// ----------------
			
			// entidade movimentacao cat
			$obj_categoria_pai = new MovimentacaoCat();
			if(is_numeric($mc_id_pai)){
				$objStatus = $this->buscaPorId($mc_id_pai);
				if($objStatus->isOk()){
					$obj_categoria_pai = $objStatus->getRetByKey('ent');
				}
				else{
					$obj_categoria_pai = null;
				}
			}
			else{
				$obj_categoria_pai = null;
			}
			// -------------------------
			
			// entidade movimentacao tipo
			$obj_movimentacao_tipo = new MovimentacaoTipo();
			$objStatus = $this->objMovimentacaoTipoServ->buscaPorId($mc_mt_id);
			if($objStatus->isOk()){
				$obj_movimentacao_tipo = $objStatus->getRetByKey('ent');
			}
			// --------------------------
			
			$movimentacaoCat = new MovimentacaoCat($mc_id, $obj_usuario, $mc_descricao, $mc_ativo, $obj_categoria_pai, $obj_movimentacao_tipo);
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $movimentacaoCat);
			return $this->objStatus;
		}
	
	}

	/**
	 * Insere o MovimentacaoCat no BD.
	 * Chaves resposta: msg, ent
	 *
	 * @param MovimentacaoCat $movimentacaoCat        	
	 * @return ObjStatus
	 */
	public function insere(MovimentacaoCat $movimentacaoCat){

		if (! is_a($movimentacaoCat, 'MovimentacaoCat')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Vari&aacute;vel n&atilde;o &eacute; Categoria de Movimenta&ccedil;&atilde;o');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaInsere($movimentacaoCat);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		// prepara SQL
		if (is_numeric($movimentacaoCat->getId())) {
			$sql = 'INSERT INTO tb_movimentacao_cat(mc_id, mc_usu_id, mc_descricao, mc_ativo, mc_id_pai, mc_mt_id)
    					VALUES (?, ?, ?, ?, ?, ?)';
			$this->objConn->setSQL($sql);
			$this->objConn->addParameter($movimentacaoCat->getId());
		}
		else {
			$sql = 'INSERT INTO tb_movimentacao_cat(mc_usu_id, mc_descricao, mc_ativo, mc_id_pai, mc_mt_id)
    					VALUES (?, ?, ?, ?, ?)';
			$this->objConn->setSQL($sql);
		}
		
		$this->objConn->addParameter($movimentacaoCat->getUsuario()->getId());
		$this->objConn->addParameter($movimentacaoCat->getDescricao());
		$this->objConn->addParameter($movimentacaoCat->getAtivo());
		$vIdPai = (is_a($movimentacaoCat->getCategoriaPai(), 'MovimentacaoCat')) ? $movimentacaoCat->getCategoriaPai()->getId(): null;
		$this->objConn->addParameter($vIdPai);
		$this->objConn->addParameter($movimentacaoCat->getMovimentacaoTipo()->getId());
		
		$returnField = $this->objConn->insert('mc_id');
		// -----------
		
		if (! $this->objConn->getError()) {
			// versao atual da Entidade
			$movimentacaoCat = new MovimentacaoCat();
			$objRet = $this->buscaPorId($returnField [0]);
			if ($objRet->isOk()) {
				$movimentacaoCat = $objRet->getRetByKey('ent');
			}
			// ------------------------
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $movimentacaoCat);
			$this->objStatus->addRet('msg', 'Categoria inclu&iacute;da com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao inserir Categoria.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 * Valida a insercao.
	 *
	 * @param MovimentacaoCat $movimentacaoCat        	
	 * @return ObjStatus
	 */
	private function validaInsere(MovimentacaoCat $movimentacaoCat){
		
		if($this->userLog != $movimentacaoCat->getUsuario()->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para inserir essa categoria!');
			return $this->objStatus;
		}

		if (!is_a($movimentacaoCat->getUsuario(), 'Usuario')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido!');
			return $this->objStatus;
		}
		
		if (strlen($movimentacaoCat->getDescricao()) <= 3) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Descri&ccedil;&atilde;o inv&aacute;lida!');
			return $this->objStatus;
		}
		
		if (!is_a($movimentacaoCat->getMovimentacaoTipo(), 'MovimentacaoTipo')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Tipo inv&aacute;lido!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 * faz a edicao do MovimentacaoCat
	 *
	 * @param MovimentacaoCat $movimentacaoCat        	
	 * @return ObjStatus
	 */
	public function edita(MovimentacaoCat $movimentacaoCat){

		if (! is_a($movimentacaoCat, 'MovimentacaoCat')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Vari&aacute;vel n&atilde;o &eacute; Categoria de Movimenta&ccedil;&atilde;o');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaEdita($movimentacaoCat);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		$sql = 'UPDATE tb_movimentacao_cat
   				SET mc_usu_id = ?, mc_descricao = ?, mc_ativo = ?, mc_id_pai = ?, mc_mt_id = ?
 				WHERE mc_id = ?';
		$this->objConn->setSQL($sql);
		
		$this->objConn->addParameter($movimentacaoCat->getUsuario()->getId());
		$this->objConn->addParameter($movimentacaoCat->getDescricao());
		$this->objConn->addParameter($movimentacaoCat->getAtivo());
		$vIdPai = (is_a($movimentacaoCat->getCategoriaPai(), 'MovimentacaoCat')) ? $movimentacaoCat->getCategoriaPai()->getId(): null;
		$this->objConn->addParameter($vIdPai);
		$this->objConn->addParameter($movimentacaoCat->getMovimentacaoTipo()->getId());
		$this->objConn->addParameter($movimentacaoCat->getId());
		
		$returnField = $this->objConn->update();
		// -----------
		
		if (! $this->objConn->getError()) {
			// versao atual da Entidade
			$movimentacaoCat = new MovimentacaoCat();
			$objRet = $this->buscaPorId($movimentacaoCat->getId());
			if ($objRet->isOk()) {
				$movimentacaoCat = $objRet->getRetByKey('ent');
			}
			// ------------------------
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $movimentacaoCat);
			$this->objStatus->addRet('msg', 'Categoria editada com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao editar Categoria de Movimenta&ccedil;&atilde;o.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 * Valida a edicao
	 *
	 * @param MovimentacaoCat $movimentacaoCat        	
	 * @return ObjStatus
	 */
	private function validaEdita(MovimentacaoCat $movimentacaoCat){
		
		if($this->userLog != $movimentacaoCat->getUsuario()->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para editar essa categoria!');
			return $this->objStatus;
		}

		if (! is_numeric($movimentacaoCat->getId())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'ID inv&aacute;lido!');
			return $this->objStatus;
		}
		
		if (!is_a($movimentacaoCat->getUsuario(), 'Usuario')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido!');
			return $this->objStatus;
		}
		
		if (strlen($movimentacaoCat->getDescricao()) <= 3) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Descri&ccedil;&atilde;o inv&aacute;lida!');
			return $this->objStatus;
		}
		
		if (!is_a($movimentacaoCat->getMovimentacaoTipo(), 'MovimentacaoTipo')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Tipo inv&aacute;lido!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 *
	 * @param MovimentacaoCat $movimentacaoCat        	
	 * @return ObjStatus
	 */
	public function deleta(MovimentacaoCat $movimentacaoCat){

		if (! is_a($movimentacaoCat, 'MovimentacaoCat')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Vari&aacute;vel n&atilde;o &eacute; Categoria de Movimenta&ccedil;&atilde;o');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaDeleta($movimentacaoCat);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		$sql = 'DELETE FROM tb_movimentacao_cat
 				WHERE mc_id = ?';
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($movimentacaoCat->getId());
		
		$qtLinhas = $this->objConn->delete();
		// -----------
		
		if (! $this->objConn->getError()) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $movimentacaoCat);
			$this->objStatus->addRet('msg', 'Categoria deletada com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao deletar Categoria de Movimenta&ccedil;&atilde;o.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 *
	 * @param MovimentacaoCat $movimentacaoCat        	
	 * @return ObjStatus
	 */
	private function validaDeleta(MovimentacaoCat $movimentacaoCat){
		
		if($this->userLog != $movimentacaoCat->getUsuario()->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para deletar essa categoria!');
			return $this->objStatus;
		}

		if (! is_numeric($movimentacaoCat->getId())) {
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
	public function pegaTodosPais($userId){
		if(!is_numeric($userId)){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Id do usu&aacute;rio inv&aacute;lido.');
			return $this->objStatus;
		}
		
		if($this->userLog != $userId){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para buscar categoria!');
			return $this->objStatus;
		}
	
		$sql = "WITH subcat_count(v_mc_id, v_count) AS (
				  SELECT a.mc_id
				         ,COALESCE(COUNT(b.*), 0)
				  FROM tb_movimentacao_cat a
				  LEFT JOIN tb_movimentacao_cat b ON b.mc_id_pai = a.mc_id
				  WHERE b.mc_ativo = TRUE
				  GROUP BY a.mc_id
				)
				
				SELECT tb_movimentacao_cat.*, COALESCE(v_count, 0) AS qt_subcat
				FROM tb_movimentacao_cat
				LEFT JOIN subcat_count ON v_mc_id = mc_id
				WHERE mc_usu_id = ?
				AND mc_ativo = TRUE
				AND mc_id_pai IS NULL
				ORDER BY mc_descricao";
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($userId);
		$resp = $this->objConn->executeSQL("ARRAY");
	
		if($this->objConn->getError()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao buscar categorias.');
			return $this->objStatus;
		}
		else{
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('arr_categorias', $resp);
			return $this->objStatus;
		}
	}
	
	/**
	 * 
	 * @param MovimentacaoCat $MovimentacaoCat
	 * @return ObjStatus
	 */
	public function pegaTodosFilhos(MovimentacaoCat $MovimentacaoCat){	
		$sql = "SELECT tb_movimentacao_cat.*
						FROM tb_movimentacao_cat
						WHERE mc_ativo = TRUE
						AND mc_id_pai = ?
						AND mc_usu_id = ?
						ORDER BY mc_descricao";
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($MovimentacaoCat->getId());
		$this->objConn->addParameter($this->userLog);
		$resp = $this->objConn->executeSQL("ARRAY");
	
		if($this->objConn->getError()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao buscar subcategorias.');
			return $this->objStatus;
		}
		else{
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('arr_subcategorias', $resp);
			return $this->objStatus;
		}
	}

	/**
	 * 
	 * @param Usuario $Usuario
	 * @param string $nome
	 * @param string $valor
	 * @return ObjStatus
	 */
	public function getHtmlCategoria(Usuario $Usuario, $nome, $valor, MovimentacaoTipo $MovimentacaoTipo){
		if($this->userLog != $Usuario->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para buscar HTML da categoria!');
			return $this->objStatus;
		}
		
		$sql = "SELECT  mc_id
								,mc_descricao
						FROM tb_movimentacao_cat
						WHERE mc_ativo = true
						AND mc_id_pai IS NULL
						AND mc_usu_id = ?
						AND mc_mt_id = ?
						ORDER BY mc_descricao";
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($Usuario->getId());
		$this->objConn->addParameter($MovimentacaoTipo->getId());
		$rs = $this->objConn->select();
		
		if(count($rs) > 0){
			$html = "";
			$html .= "<select class='form-control' name='$nome' id='$nome'>
						<option value=''></option>";
			foreach($rs as $conta){
				$mc_id = $conta["mc_id"];
				$mc_nome = $conta["mc_descricao"];
				$selctd = ($mc_id == $valor) ? " selected ": "";
		
				$html .= "<option $selctd value='$mc_id'>$mc_nome</option>";
			}
				
			$html .= "</select>";
				
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('msg', '');
			$this->objStatus->addRet('html_cb_categoria', $html);
			return $this->objStatus;
		}
		else{
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Nenhuma categoria encontrada');
			$this->objStatus->addRet('html_cb_categoria', '');
			return $this->objStatus;
		}
	}
	
	/**
	 * 
	 * @param MovimentacaoCat $MovimentacaoCatPai
	 * @param Usuario $Usuario
	 * @param text $nome
	 * @param text $valor
	 * @return ObjStatus
	 */
	public function getHtmlCategoriaSub(MovimentacaoCat $MovimentacaoCatPai, Usuario $Usuario, $nome, $valor){
		if($this->userLog != $Usuario->getId()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Usu&aacute;rio inv&aacute;lido para buscar HTML da categoria!');
			return $this->objStatus;
		}
		
		$sql = "SELECT  mc_id
								,mc_descricao
						FROM tb_movimentacao_cat
						WHERE mc_ativo = true
						AND mc_id_pai = ?
						AND mc_usu_id = ?
						ORDER BY mc_descricao";
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($MovimentacaoCatPai->getId());
		$this->objConn->addParameter($Usuario->getId());
		$rs = $this->objConn->select();
		
		if(count($rs) > 0){
			$html = "";
			$html .= "<select class='form-control' name='$nome' id='$nome'>
			<option value=''></option>";
			foreach($rs as $conta){
				$mc_id = $conta["mc_id"];
				$mc_nome = $conta["mc_descricao"];
				$selctd = ($mc_id == $valor) ? " selected ": "";
		
				$html .= "<option $selctd value='$mc_id'>$mc_nome</option>";
			}
		
			$html .= "</select>";
		
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('msg', '');
			$this->objStatus->addRet('html_cb_subcategoria', $html);
			return $this->objStatus;
		}
		else{
			$html = "<select class='form-control' name='$nome' id='$nome'>
						<option value=''></option>
					 </select>";
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Nenhuma subcategoria encontrada');
			$this->objStatus->addRet('html_cb_subcategoria', $html);
			return $this->objStatus;
		}
	}
}
?>