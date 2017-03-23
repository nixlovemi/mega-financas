<?php

/**
 Entity for table tb_cartao_credito
 */
class CartaoCredito{
	
	// VARS
	/**
	 *
	 * @var cc_id - integer - PK
	 */
	private $id;

	/**
	 *
	 * @var cc_descricao - varchar(40)
	 */
	private $descricao;

	/**
	 *
	 * @var cc_usu_id - entity
	 */
	private $usuario;

	/**
	 *
	 * @var cc_bc_id - entity
	 */
	private $bandeira_cartao;

	/**
	 *
	 * @var cc_limite - float
	 */
	private $limite;

	/**
	 *
	 * @var cc_dia_fechamento - integer
	 */
	private $dia_fechamento;
	
	/**
	 *
	 * @var cc_dia_pagamento - integer
	 */
	private $dia_pagamento;
	
	/**
	 *
	 * @var cc_deletado - boolean
	 */
	private $deletado;
	// ====
	
	// GETTER
	public function getId(){

		return $this->id;
	
	}

	public function getDescricao(){

		return $this->descricao;
	
	}

	public function getUsuario(){

		return $this->usuario;
	
	}

	public function getBandeiraCartao(){

		return $this->bandeira_cartao;
	
	}

	public function getLimite(){

		return $this->limite;
	
	}

	public function getDiaFechamento(){

		return $this->dia_fechamento;
	
	}
	
	public function getDiaPagamento(){
	
		return $this->dia_pagamento;
	
	}
	
	public function getDeletado(){
	
		return $this->deletado;
	
	}
	// ======
	
	// SETTER
	public function setId($id){

		$this->id = $id;
		$this->checkCampoNull($this, "id");
	
	}

	public function setDescricao($descricao){

		$this->descricao = $descricao;
		$this->checkCampoNull($this, "descricao");
	
	}

	public function setUsuario(Usuario $usuario=null){

		$this->usuario = $usuario;
		$this->checkCampoNull($this, "usuario");
	
	}

	public function setBandeiraCartao(BandeiraCartao $bandeira_cartao=null){

		$this->bandeira_cartao = $bandeira_cartao;
		$this->checkCampoNull($this, "bandeira_cartao");
	
	}

	public function setLimite($limite){

		$this->limite = $limite;
		$this->checkCampoNull($this, "limite");
	
	}

	public function setDiaFechamento($dia_fechamento){

		$this->dia_fechamento = $dia_fechamento;
		$this->checkCampoNull($this, "dia_fechamento");
	
	}
	
	public function setDiaPagamento($dia_pagamento){
	
		$this->dia_pagamento = $dia_pagamento;
		$this->checkCampoNull($this, "dia_pagamento");
	
	}
	
	public function setDeletado($deletado){
	
		$this->deletado = $deletado;
		$this->checkCampoNull($this, "deletado");
	
	}
	// ======
	
	// CONSTRUCT
	function __construct(){

		$qtdParametros = ( int ) func_num_args();
		
		if ($qtdParametros > 0) {
			$param = func_get_args();
			
			if ($qtdParametros == 1 && $param [0] instanceof CartaoCredito) {
				$this->criarCartaoCredito($param [0]);
			}
			else {
				$this->criarComVars($param [0], $param [1], $param [2], $param [3], $param [4], $param [5], $param [6], $param [7]);
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

	public function criarCartaoCredito(CartaoCredito $cartaoCredito){

		$this->setId($cartaoCredito->getId());
		$this->setDescricao($cartaoCredito->getDescricao());
		$this->setUsuario($cartaoCredito->getUsuario());
		$this->setBandeiraCartao($cartaoCredito->getBandeiraCartao());
		$this->setLimite($cartaoCredito->getLimite());
		$this->setDiaFechamento($cartaoCredito->getDiaFechamento());
		$this->setDiaPagamento($cartaoCredito->getDiaPagamento());
		$this->setDeletado($cartaoCredito->getDeletado());
	
	}

	public function criarComVars($id, $descricao, Usuario $usuario=null, BandeiraCartao $bandeira_cartao=null, $limite, $dia_fechamento, $dia_pagamento, $deletado){

		$this->setId($id);
		$this->setDescricao($descricao);
		$this->setUsuario($usuario);
		$this->setBandeiraCartao($bandeira_cartao);
		$this->setLimite($limite);
		$this->setDiaFechamento($dia_fechamento);
		$this->setDiaPagamento($dia_pagamento);
		$this->setDeletado($deletado);
	
	}
	// =========
}
?>