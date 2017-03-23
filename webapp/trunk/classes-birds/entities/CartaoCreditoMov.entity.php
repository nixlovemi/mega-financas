<?php

/**
 Entity for table tb_cartao_credito_mov
 */
class CartaoCreditoMov{
	
	// VARS
	/**
	 *
	 * @var ccm_id - integer - PK
	 */
	private $id;

	/**
	 *
	 * @var ccm_ccf_id - entity
	 */
	private $CartaoCreditoFat;

	/**
	 *
	 * @var ccm_descricao - varchar(80)
	 */
	private $descricao;

	/**
	 *
	 * @var ccm_valor - float
	 */
	private $valor;

	/**
	 *
	 * @var ccm_mc_id - entity
	 */
	private $MovimentacaoCat;

	/**
	 *
	 * @var ccm_id_parcelado - integer
	 */
	private $idParcelado;
	
	/**
	 *
	 * @var ccm_parcela - integer
	 */
	private $parcela;
	
	/**
	 *
	 * @var ccm_deletado - bool
	 */
	private $deletado = FALSE;
	
	/**
	 *
	 * @var ccm_pro_id - entity
	 */
	private $Projeto;
	
	/**
	 *
	 * @var ccm_data - date
	 */
	private $data;
	
	/**
	 *
	 * @var integer
	 */
	private $qtde_parcelas;
	// ====
	
	// GETTER
	public function getId(){

		return $this->id;
	
	}

	public function getCartaoCreditoFat(){

		return $this->CartaoCreditoFat;
	
	}

	public function getDescricao(){

		return $this->descricao;
	
	}

	public function getValor(){

		return $this->valor;
	
	}

	public function getMovimentacaoCat(){

		return $this->MovimentacaoCat;
	
	}

	public function getIdParcelado(){

		return $this->idParcelado;
	
	}
	
	public function getParcela(){
	
		return $this->parcela;
	
	}
	
	public function getDeletado(){
	
		return $this->deletado;
	
	}
	
	public function getProjeto(){
	
		return $this->Projeto;
	
	}
	
	public function getData(){
	
		return $this->data;
	
	}
	
	public function getQtdeParcelas(){
	
		return $this->qtde_parcelas;
	
	}
	// ======
	
	// SETTER
	public function setId($id){

		$this->id = $id;
		$this->checkCampoNull($this, "id");
	
	}

	public function setCartaoCreditoFat(CartaoCreditoFat $CartaoCreditoFat=null){

		$this->CartaoCreditoFat = $CartaoCreditoFat;
		$this->checkCampoNull($this, "CartaoCreditoFat");
	
	}

	public function setDescricao($descricao){

		$this->descricao = $descricao;
		$this->checkCampoNull($this, "descricao");
	
	}

	public function setValor($valor){

		$this->valor = $valor;
		$this->checkCampoNull($this, "valor");
	
	}

	public function setMovimentacaoCat(MovimentacaoCat $MovimentacaoCat=null){

		$this->MovimentacaoCat = $MovimentacaoCat;
		$this->checkCampoNull($this, "MovimentacaoCat");
	
	}

	public function setIdParcelado($idParcelado){

		$this->idParcelado = $idParcelado;
		$this->checkCampoNull($this, "idParcelado");
	
	}
	
	public function setParcela($parcela){
	
		$this->parcela = $parcela;
		$this->checkCampoNull($this, "parcela");
	
	}
	
	public function setDeletado($deletado){
	
		$this->deletado = $deletado;
		$this->checkCampoNull($this, "deletado");
	
	}
	
	public function setProjeto(Projeto $Projeto=null){
	
		$this->Projeto = $Projeto;
		$this->checkCampoNull($this, "Projeto");
	
	}
	
	public function setData($data){
	
		$this->data = $data;
		$this->checkCampoNull($this, "data");
	
	}
	
	public function setQtdeParcelas($qtdeParcelas){
	
		$this->qtde_parcelas = $qtdeParcelas;
		$this->checkCampoNull($this, "qtde_parcelas");
	
	}
	// ======
	
	// CONSTRUCT
	function __construct(){

		$qtdParametros = ( int ) func_num_args();
		
		if ($qtdParametros > 0) {
			$param = func_get_args();
			
			if ($qtdParametros == 1 && $param [0] instanceof CartaoCreditoMov) {
				$this->criarCartaoCreditoMov($param [0]);
			}
			else {
				$this->criarComVars($param [0], $param [1], $param [2], $param [3], $param [4], $param [5], $param [6], $param [7], $param [8], $param [9], $param [10]);
			}
		}
	
	}
	
	private function setNull($class, $campo){
		$value = null;
		$class->$campo = $value;
	}
	
	private function checkCampoNull($class, $campo){
		if (!is_object($class->$campo) && trim($class->$campo) == "") {
			$this->setNull($class, $campo);
		}
	}
	
	private function initCamposNull($class){
		foreach ( $class as $key => $value ) {
			if (!is_object($value) && trim($value) == "") {
				$this->setNull($class, $key);
			}
		}
	}

	public function criarCartaoCreditoMov(CartaoCreditoMov $cartaoCreditoMov){

		$this->setId($cartaoCreditoMov->getId());
		$this->setCartaoCreditoFat($cartaoCreditoMov->getCartaoCreditoFat());
		$this->setDescricao($cartaoCreditoMov->getDescricao());
		$this->setValor($cartaoCreditoMov->getValor());
		$this->setMovimentacaoCat($cartaoCreditoMov->getMovimentacaoCat());
		$this->setIdParcelado($cartaoCreditoMov->getIdParcelado());
		$this->setParcela($cartaoCreditoMov->getParcela());
		$this->setDeletado($cartaoCreditoMov->getDeletado());
		$this->setProjeto($cartaoCreditoMov->getProjeto());
		$this->setData($cartaoCreditoMov->getData());
		$this->setQtdeParcelas($cartaoCreditoMov->getQtdeParcelas());
	
	}

	public function criarComVars($id, CartaoCreditoFat $CartaoCreditoFat=null, $descricao, $valor, MovimentacaoCat $MovimentacaoCat=null, $idParcelado, $parcela, $deletado, Projeto $Projeto=null, $data, $qtdeParcelas){

		$this->setId($id);
		$this->setCartaoCreditoFat($CartaoCreditoFat);
		$this->setDescricao($descricao);
		$this->setValor($valor);
		$this->setMovimentacaoCat($MovimentacaoCat);
		$this->setIdParcelado($idParcelado);
		$this->setParcela($parcela);
		$this->setDeletado($deletado);
		$this->setProjeto($Projeto);
		$this->setData($data);
		$this->setQtdeParcelas($qtdeParcelas);
	
	}
	// =========
}
?>