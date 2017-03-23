<?php

/**
 Entity for table tb_movimentacao_tipo
 */
class MovimentacaoTipo{
	
	// VARS
	/**
	 *
	 * @var mt_id - integer - PK
	 */
	private $id;

	/**
	 *
	 * @var mt_descricao - varchar(25)
	 */
	private $descricao;

	/**
	 *
	 * @var mt_ativo - boolean
	 */
	private $ativo;
	// ====
	
	// GETTER
	public function getId(){

		return $this->id;
	
	}

	public function getDescricao(){

		return $this->descricao;
	
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

	public function setDescricao($descricao){

		$this->descricao = $descricao;
		$this->checkCampoNull($this, "descricao");
	
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
			
			if ($qtdParametros == 1 && $param [0] instanceof MovimentacaoTipo) {
				$this->criarMovimentacaoTipo($param [0]);
			}
			else {
				$this->criarComVars($param [0], $param [1], $param [2]);
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

	public function criarMovimentacaoTipo(MovimentacaoTipo $movimentacaoTipo){

		$this->setId($movimentacaoTipo->getId());
		$this->setDescricao($movimentacaoTipo->getDescricao());
		$this->setAtivo($movimentacaoTipo->getAtivo());
	
	}

	public function criarComVars($id, $descricao, $ativo){

		$this->setId($id);
		$this->setDescricao($descricao);
		$this->setAtivo($ativo);
	
	}
	// =========
}
?>