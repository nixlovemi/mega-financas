<?php

/**
 Entity for table tb_conta
 */
class Conta{
	
	// VARS
	/**
	 *
	 * @var con_id - integer - PK
	 */
	private $id;

	/**
	 *
	 * @var con_usu_id - entity
	 */
	private $usuario;

	/**
	 *
	 * @var con_nome - varchar(40)
	 */
	private $nome;

	/**
	 *
	 * @var con_saldo_inicial - float
	 */
	private $saldo_inicial;

	/**
	 *
	 * @var con_cor - varchar(6)
	 */
	private $cor;

	/**
	 *
	 * @var con_ativo - bool
	 */
	private $ativo;
	// ====
	
	// GETTER
	public function getId(){

		return $this->id;
	
	}

	public function getUsuario(){

		return $this->usuario;
	
	}

	public function getNome(){

		return $this->nome;
	
	}

	public function getSaldoInicial(){

		return $this->saldo_inicial;
	
	}

	public function getCor(){

		return $this->cor;
	
	}

	public function getAtivo(){

		return $this->ativo;
	
	}
	// ======
	
	// SETTER
	public function setId($id){

		$this->id = $id;
		$this->checkCampoNull($this, "id");
	
	}

	public function setUsuario(Usuario $usuario=null){

		$this->usuario = $usuario;
		$this->checkCampoNull($this, "usuario");
	
	}

	public function setNome($nome){

		$this->nome = $nome;
		$this->checkCampoNull($this, "nome");
	
	}

	public function setSaldoInicial($saldoInicial){

		$this->saldo_inicial = $saldoInicial;
		$this->checkCampoNull($this, "saldo_inicial");
	
	}

	public function setCor($cor){

		$this->cor = str_replace("#", "", $cor);
		$this->checkCampoNull($this, "cor");
	
	}

	public function setAtivo($ativo){

		$this->ativo = $ativo;
		$this->checkCampoNull($this, "ativo");
	
	}
	// ======
	
	// CONSTRUCT
	function __construct(){

		$qtdParametros = ( int ) func_num_args();
		
		if ($qtdParametros > 0) {
			$param = func_get_args();
			
			if ($qtdParametros == 1 && $param [0] instanceof Conta) {
				$this->criarConta($param [0]);
			}
			else {
				$this->criarComVars($param [0], $param [1], $param [2], $param [3], $param [4], $param [5]);
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

	public function criarConta(Conta $conta){

		$this->setId($conta->getId());
		$this->setUsuario($conta->getUsuario());
		$this->setNome($conta->getNome());
		$this->setSaldoInicial($conta->getSaldoInicial());
		$this->setCor($conta->getCor());
		$this->setAtivo($conta->getAtivo());
	
	}

	public function criarComVars($id, Usuario $usuario=null, $nome, $saldoInicial, $cor, $ativo){
		
		$this->setId($id);
		$this->setUsuario($usuario);
		$this->setNome($nome);
		$this->setSaldoInicial($saldoInicial);
		$this->setCor($cor);
		$this->setAtivo($ativo);
	
	}
	// =========
}
?>