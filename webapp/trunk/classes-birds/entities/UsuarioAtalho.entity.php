<?php

/**
 Entity for table tb_usuario_atalho
 */
class UsuarioAtalho{
	
	// VARS
	/**
	 *
	 * @var ua_id - integer - PK
	 */
	private $id;

	/**
	 *
	 * @var ua_ata_id - entity
	 */
	private $atalho;

	/**
	 *
	 * @var ua_usu_id - entity
	 */
	private $usuario;
	// ====
	
	// GETTER
	public function getId(){

		return $this->id;
	
	}

	public function getAtalho(){

		return $this->atalho;
	
	}

	public function getUsuario(){

		return $this->usuario;
	
	}
	// ======
	
	// SETTER
	public function setId($id){

		$this->id = $id;
		$this->checkCampoNull($this, "id");
	
	}

	public function setAtalho(Atalho $atalho){

		$this->atalho = $atalho;
		$this->checkCampoNull($this, "atalho");
	
	}

	public function setUsuario(Usuario $usuario=null){

		$this->usuario = $usuario;
		$this->checkCampoNull($this, "usuario");
	
	}
	// ======
	
	// CONSTRUCT
	function __construct(){

		$qtdParametros = ( int ) func_num_args();
		
		if ($qtdParametros > 0) {
			$param = func_get_args();
			
			if ($qtdParametros == 1 && $param [0] instanceof UsuarioAtalho) {
				$this->criarUsuarioAtalho($param [0]);
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

	public function criarUsuarioAtalho(UsuarioAtalho $usuarioAtalho){

		$this->setId($usuarioAtalho->getId());
		$this->setAtalho($usuarioAtalho->getAtalho());
		$this->setUsuario($usuarioAtalho->getUsuario());
	
	}

	public function criarComVars($id, Atalho $atalho=null, Usuario $usuario=null){

		$this->setId($id);
		$this->setAtalho($atalho);
		$this->setUsuario($usuario);
	
	}
	// =========
}
?>