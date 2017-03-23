<?php

class CartaoCreditoModel extends BaseModel{
	
	public function index() {
		return $this->viewModel;
	}
	
	public function incluir(){
		return $this->viewModel;
	}
	
	public function editar(){
		return $this->viewModel;
	}
	
	public function indexLancamentos(){
		return $this->viewModel;
	}

	/**
	 * 
	 * @param Usuario $Usuario
	 * @param string $idCartao
	 * @return CartaoCredito|arrCartao
	 */
	public function pegaCartoesUsuario(Usuario $Usuario, $idCartao=""){
		require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/entities/CartaoCredito.entity.php';
		require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/CartaoCredito.service.php';
		$CartaoCreditoServ = new CartaoCreditoService();
		
		$objRet = $CartaoCreditoServ->pegaListaCartoes($Usuario);
		if($objRet->isErro()){
			return array();
		}
		
		$rs = $objRet->getRetByKey("arr_lista_cartao");
		$arrCartao = array();
		
		foreach($rs as $cartao){
			$arrCartao[] = array(
				"cc_id"=>$cartao["cc_id"],
				"cc_descricao"=>$cartao["cc_descricao"],
			);
		}
		
		if($idCartao==""){
			$idCartao = $arrCartao[0]["cc_id"];
		}
		
		$CartaoCredito = new CartaoCredito();
		$objRet = $CartaoCreditoServ->buscaPorId($idCartao);
		if($objRet->isOk()){
			$CartaoCredito = $objRet->getRetByKey("ent");
		}
		
		return array(
				$CartaoCredito,
				$arrCartao,
		);
	}

	/**
	 * 
	 * @param CartaoCredito $CartaoCredito
	 * @return array
	 */
	public function pegaMesesFaturaCartao(CartaoCredito $CartaoCredito){
		require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/CartaoCreditoFat.service.php';
		$CartaoCreditoFatServ = new CartaoCreditoFatService();
		
		$objRet = $CartaoCreditoFatServ->pegaMesesFatura($CartaoCredito);
		if($objRet->isErro()){
			return array();
		}
		
		$rs = $objRet->getRetByKey("arr_lista_meses_fatura");
		$arrMes = array();
		
		if( count($rs) > 0 ){
			foreach ($rs as $mes){
				$v_mes = date("Y-m-d", strtotime($mes["mes_ano"]));
				$strMes = utf8_encode(strftime("%B - %Y", strtotime($v_mes)));
					
				$arrMes[$v_mes] = ucfirst($strMes);
			}
		}
		else{
			$mes_passado = date("Y-m-j", strtotime("first day of previous month"));
			$str_mes_passado = utf8_encode(strftime("%B - %Y", strtotime($mes_passado)));
			$arrMes[$mes_passado] = ucfirst($str_mes_passado);
			
			$mes_atual = date("Y-m-") . '01';
			$str_mes_atual = utf8_encode(strftime("%B - %Y", strtotime($mes_atual)));
			$arrMes[$mes_atual] = ucfirst($str_mes_atual);
			
			$mes_proximo = date("Y-m-j", strtotime("first day of next month"));
			$str_mes_proximo = utf8_encode(strftime("%B - %Y", strtotime($mes_proximo)));
			$arrMes[$mes_proximo] = ucfirst($str_mes_proximo);
		}
		
		return $arrMes;
	}

	public function incluirLctoCartao(){
		return $this->viewModel;
	}
	
	public function editarLctoCartao(){
		return $this->viewModel;
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
	
	public function mostraParcelas(){
		return $this->viewModel;
	}
}

?>
