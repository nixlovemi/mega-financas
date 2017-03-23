<?php

/**
 Entity for table tb_projeto
 */
class Projeto{
	
	// VARS
	/**
	 *
	 * @var pro_id - integer - PK
	 */
	private $id;

	/**
	 *
	 * @var pro_descricao - varchar(60)
	 */
	private $descricao;

	/**
	 *
	 * @var pro_usu_id - entity
	 */
	private $usuario;

	/**
	 *
	 * @var pro_finalizado - boolean
	 */
	private $finalizado;

	/**
	 *
	 * @var pro_observacao - text
	 */
	private $observacao;

	/**
	 *
	 * @var pro_deletado - bool
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

	public function getFinalizado(){

		return $this->finalizado;
	
	}

	public function getObservacao(){

		return $this->observacao;
	
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
	
	public function setFinalizado($finalizado){
	
		$this->finalizado = $finalizado;
		$this->checkCampoNull($this, "finalizado");
	
	}
	
	public function setObservacao($observacao){
	
		$this->observacao = $observacao;
		$this->checkCampoNull($this, "observacao");
	
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
			
			if ($qtdParametros == 1 && $param [0] instanceof Projeto) {
				$this->criarProjeto($param [0]);
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

	public function criarProjeto(Projeto $projeto){

		$this->setId($projeto->getId());
		$this->setDescricao($projeto->getDescricao());
		$this->setUsuario($projeto->getUsuario());
		$this->setFinalizado($projeto->getFinalizado());
		$this->setObservacao($projeto->getObservacao());
		$this->setDeletado($projeto->getDeletado());
	
	}

	public function criarComVars($id, $descricao, Usuario $usuario=null, $finalizado, $observacao, $deletado){

		$this->setId($id);
		$this->setDescricao($descricao);
		$this->setUsuario($usuario);
		$this->setFinalizado($finalizado);
		$this->setObservacao($observacao);
		$this->setDeletado($deletado);
	
	}
	// =========
}
?>