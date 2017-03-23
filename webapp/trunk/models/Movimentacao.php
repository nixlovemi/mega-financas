<?php

class MovimentacaoModel extends BaseModel{
	
	public function indexReceitas() {
		return $this->viewModel;
	}
	
	public function indexTransferencias(){
		return $this->viewModel;
	}
	
	public function incluir() {
		return $this->viewModel;
	}
	
	public function incluirDespesa() {
		return $this->viewModel;
	}
	
	public function incluirTransferencia() {
		return $this->viewModel;
	}
	
	public function editar() {
		return $this->viewModel;
	}
	
	public function editarTransferencia() {
		return $this->viewModel;
	}
	
	public function pegaContaUsuario(Usuario $Usuario, $idConta){
		// Conta do Usuario ==================
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/Conta.service.php";
		
		$ContaServ = new ContaService();
		$objResp = $ContaServ->pegaTodos( $Usuario->getId() );
		if( $objResp->isErro() ){
			$erro_msg = "Nenhuma conta cadastrada. Tente novamente em breve!";
		}
		$arrContas = $objResp->getRetByKey("arr_contas");
		
		if(!is_numeric($idConta)){
			$idConta = $arrContas[0]["con_id"];
		}
		
		$objResp = $ContaServ->buscaPorId($idConta);
		if( $objResp->isErro() ){
			$erro_msg = "Erro ao buscar conta. Tente novamente em breve!";
		}
		$Conta = $objResp->getRetByKey("ent");
		// ===================================
		
		return array(
			$Conta
			,$arrContas
		);
	}
	
	/**
	 * retorna o HTML da combo de contas
	 * 
	 * @param Usuario $Usuario
	 * @param text $nome
	 * @param text $valor
	 * @return string|VARIANT
	 */
	public function pegaHtmlContas(Usuario $Usuario, $nome, $valor){
		require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/Conta.service.php';
		$ContaServ = new ContaService();
		
		$objRet = $ContaServ->getHtmlCbContas($Usuario, $nome, $valor);
		if($objRet->isErro()){
			return "";
		}
		else{
			$html = $objRet->getRetByKey("html_cb_contas");
			return $html;
		}
	}
	
	/**
	 * retorna o HTML da combo de categorias
	 * 
	 * @param Usuario $Usuario
	 * @param text $nome
	 * @param text $valor
	 * @return ObjStatus|string|VARIANT
	 */
	public function pegaHtmlCat(Usuario $Usuario, $nome, $valor, MovimentacaoTipo $MovimentacaoTipo=NULL){
		require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/MovimentacaoCat.service.php';
		$MovimentacaoCatServ = new MovimentacaoCatService();
		
		// Movimentacao tipo ===========================
		if(is_a($MovimentacaoTipo, "MovimentacaoTipo")){
			$MovimentacaoTp = $MovimentacaoTipo;
		}
		else{
			require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/MovimentacaoTipo.service.php';
			$MovimentacaoTpServ = new MovimentacaoTipoService();
			$objResp = $MovimentacaoTpServ->pegaReceita();
			if($objResp->isErro()){
				return $objResp;
			}
			$MovimentacaoTp = $objResp->getRetByKey("ent");
		}
		// =============================================
		
		$objRet = $MovimentacaoCatServ->getHtmlCategoria($Usuario, $nome, $valor, $MovimentacaoTp);
		if($objRet->isErro()){
			return "";
		}
		else{
			$html = $objRet->getRetByKey("html_cb_categoria");
			return $html;
		}
	}

	/**
	 * retorna o HTML da combo de subcategorias
	 * 
	 * @param Usuario $Usuario
	 * @param text $nome
	 * @param text $valor
	 * @return ObjStatus|string|VARIANT
	 */
	public function pegaHtmlSubCat(Usuario $Usuario, MovimentacaoCat $MovCatPai, $nome, $valor){
		require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/MovimentacaoCat.service.php';
		$MovimentacaoCatServ = new MovimentacaoCatService();
		$objRet = $MovimentacaoCatServ->getHtmlCategoriaSub($MovCatPai, $Usuario, $nome, $valor);
		
		$html = $objRet->getRetByKey("html_cb_subcategoria");
		return $html;
	}
	
	/**
	 * Passando a entidade da movimentacao cat retorna o ID do pai e filho
	 * 
	 * @param MovimentacaoCat $MovimentacaoCat
	 * @return array
	 */
	public function pegaCatSubcat(MovimentacaoCat $MovimentacaoCat){
		if(is_a($MovimentacaoCat->getCategoriaPai(), "MovimentacaoCat")){
			$V_MC_ID_PAI = $MovimentacaoCat->getCategoriaPai()->getId();
			$V_MC_ID_FILHO = $MovimentacaoCat->getId();
		}
		else{
			$V_MC_ID_PAI = $MovimentacaoCat->getId();
			$V_MC_ID_FILHO = NULL;
		}
		
		return array($V_MC_ID_PAI, $V_MC_ID_FILHO);
	}

	public function mostraParcelas(){
		return $this->viewModel;
	}

	public function editarCompetencia(){
		return $this->viewModel;
	}
}

?>
