<?php

/**
 Entity for table tb_atalho
 */
class Atalho{
	
	// VARS
	/**
	 *
	 * @var ata_id - integer - PK
	 */
	private $id;

	/**
	 *
	 * @var ata_nome - varchar(20)
	 */
	private $nome;

	/**
	 *
	 * @var ata_fa_icone - varchar(25)
	 */
	private $fa_icone;

	/**
	 *
	 * @var ata_controller - varchar(60)
	 */
	private $controller;

	/**
	 *
	 * @var ata_action - varchar(60)
	 */
	private $action;

	/**
	 *
	 * @var ata_ativo - bool
	 */
	private $ativo;
	
	// ====
	
	// GETTER
	public function getId(){

		return $this->id;
	
	}

	public function getNome(){

		return $this->nome;
	
	}

	public function getIcone(){

		return $this->fa_icone;
	
	}

	public function getController(){

		return $this->controller;
	
	}

	public function getAction(){

		return $this->action;
	
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

	public function setNome($nome){

		$this->nome = $nome;
		$this->checkCampoNull($this, "nome");
	
	}

	public function setIcone($icone){

		$this->fa_icone = $icone;
		$this->checkCampoNull($this, "fa_icone");
	
	}

	public function setController($controller){

		$this->controller = $controller;
		$this->checkCampoNull($this, "controller");
	
	}

	public function setAction($action){

		$this->action = $action;
		$this->checkCampoNull($this, "action");
	
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
			
			if ($qtdParametros == 1 && $param [0] instanceof Atalho) {
				$this->criarAtalho($param [0]);
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

	public function criarAtalho(Atalho $atalho){

		$this->setId($atalho->getId());
		$this->setNome($atalho->getNome());
		$this->setIcone($atalho->getIcone());
		$this->setController($atalho->getController());
		$this->setAction($atalho->getAction());
		$this->setAtivo($atalho->getAtivo());
	
	}

	public function criarComVars($id, $nome, $icone, $controller, $action, $ativo){

		$this->setId($id);
		$this->setNome($nome);
		$this->setIcone($icone);
		$this->setController($controller);
		$this->setAction($action);
		$this->setAtivo($ativo);
	
	}
	// =========
}
?>