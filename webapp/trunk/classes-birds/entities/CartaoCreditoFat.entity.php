<?php

/**
 Entity for table tb_cartao_credito_fat
 */
class CartaoCreditoFat{
	
	// VARS
	/**
	 *
	 * @var ccf_id - integer - PK
	 */
	private $id;

	/**
	 *
	 * @var ccf_cc_id - entity
	 */
	private $CartaoCredito;

	/**
	 *
	 * @var ccf_mes - integer
	 */
	private $mes;

	/**
	 *
	 * @var ccf_ano - integer
	 */
	private $ano;

	/**
	 *
	 * @var ccf_total - float
	 */
	private $total = 0;

	/**
	 *
	 * @var ccf_mov_id - entity
	 */
	private $Movimentacao;
	
	/**
	 *
	 * @var ccf_fechado - boolean
	 */
	private $fechado = FALSE;
	// ====
	
	// GETTER
	public function getId(){

		return $this->id;
	
	}

	public function getCartaoCredito(){

		return $this->CartaoCredito;
	
	}

	public function getMes(){

		return $this->mes;
	
	}

	public function getAno(){

		return $this->ano;
	
	}

	public function getTotal(){

		return $this->total;
	
	}

	public function getMovimentacao(){

		return $this->Movimentacao;
	
	}
	
	public function getFechado(){

		return $this->fechado;
	
	}
	// ======
	
	// SETTER
	public function setId($id){

		$this->id = $id;
		$this->checkCampoNull($this, "id");
	
	}

	public function setCartaoCredito(CartaoCredito $CartaoCredito=null){

		$this->CartaoCredito = $CartaoCredito;
		$this->checkCampoNull($this, "CartaoCredito");
	
	}

	public function setMes($mes){

		$this->mes = $mes;
		$this->checkCampoNull($this, "mes");
	
	}

	public function setAno($ano){

		$this->ano = $ano;
		$this->checkCampoNull($this, "ano");
	
	}

	public function setTotal($total){

		$this->total = $total;
		$this->checkCampoNull($this, "total");
	
	}

	public function setMovimentacao(Movimentacao $Movimentacao=null){

		$this->Movimentacao = $Movimentacao;
		$this->checkCampoNull($this, "Movimentacao");
	
	}
	
	public function setFechado($fechado){

		$this->fechado = $fechado;
		$this->checkCampoNull($this, "fechado");
	
	}
	// ======
	
	// CONSTRUCT
	function __construct(){

		$qtdParametros = ( int ) func_num_args();
		
		if ($qtdParametros > 0) {
			$param = func_get_args();
			
			if ($qtdParametros == 1 && $param [0] instanceof CartaoCreditoFat) {
				$this->criarCartaoCreditoFat($param [0]);
			}
			else {
				$this->criarComVars($param [0], $param [1], $param [2], $param [3], $param [4], $param [5], $param [6]);
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

	public function criarCartaoCreditoFat(CartaoCreditoFat $CartaoCreditoFat){

		$this->setId($CartaoCreditoFat->getId());
		$this->setCartaoCredito($CartaoCreditoFat->getCartaoCredito());
		$this->setMes($CartaoCreditoFat->getMes());
		$this->setAno($CartaoCreditoFat->getAno());
		$this->setTotal($CartaoCreditoFat->getTotal());
		$this->setMovimentacao($CartaoCreditoFat->getMovimentacao());
		$this->setFechado($CartaoCreditoFat->getFechado());
	
	}

	public function criarComVars($id, CartaoCredito $CartaoCredito=null, $mes, $ano, $total, Movimentacao $Movimentacao=null, $fechado){

		$this->setId($id);
		$this->setCartaoCredito($CartaoCredito);
		$this->setMes($mes);
		$this->setAno($ano);
		$this->setTotal($total);
		$this->setMovimentacao($Movimentacao);
		$this->setFechado($fechado);
	
	}
	// =========
}
?>