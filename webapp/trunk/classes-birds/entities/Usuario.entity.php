<?php

/**
 Entity for table tb_usuario
 */
class Usuario{
	
	// VARS
	/**
	 *
	 * @var usu_id - integer - PK
	 */
	private $id;

	/**
	 *
	 * @var usu_nome - varchar(80)
	 */
	private $nome;

	/**
	 *
	 * @var usu_sobrenome - varchar(80)
	 */
	private $sobrenome;

	/**
	 *
	 * @var usu_email - varchar(100)
	 */
	private $email;

	/**
	 *
	 * @var usu_senha - varchar(40)
	 */
	private $senha;

	/**
	 *
	 * @var usu_salt - varchar(16)
	 */
	private $salt;

	/**
	 *
	 * @var usu_cad_liberado - boolean
	 */
	private $cadLiberado;
	
	// ====
	
	// GETTER
	public function getId(){

		return $this->id;
	
	}

	public function getNome(){

		return $this->nome;
	
	}

	public function getSobrenome(){

		return $this->sobrenome;
	
	}

	public function getEmail(){

		return $this->email;
	
	}

	public function getSenha(){

		return $this->senha;
	
	}

	public function getSalt(){

		return $this->salt;
	
	}

	public function getCadLiberado(){

		return $this->cadLiberado;
	
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

	public function setSobrenome($sobrenome){

		$this->sobrenome = $sobrenome;
		$this->checkCampoNull($this, "sobrenome");
	
	}

	public function setEmail($email){

		$this->email = $email;
		$this->checkCampoNull($this, "email");
	
	}

	public function setSenha($senha){

		$this->senha = $senha;
		$this->checkCampoNull($this, "senha");
	
	}

	public function setSalt($salt){

		$this->salt = $salt;
		$this->checkCampoNull($this, "salt");
	
	}

	public function setCadLiberado($cadLiberado){

		$this->cadLiberado = $cadLiberado;
		$this->checkCampoNull($this, "cadLiberado");
	
	}
	// ======
	
	// CONSTRUCT
	function __construct(){

		$qtdParametros = ( int ) func_num_args();
		
		if ($qtdParametros > 0) {
			$param = func_get_args();
			
			if ($qtdParametros == 1 && $param [0] instanceof Usuario) {
				$this->criarUsuario($param [0]);
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

	public function criarUsuario(Usuario $usuario){

		$this->setId($usuario->getId());
		$this->setNome($usuario->getNome());
		$this->setSobrenome($usuario->getSobrenome());
		$this->setEmail($usuario->getEmail());
		$this->setSenha($usuario->getSenha());
		$this->setSalt($usuario->getSalt());
		$this->setCadLiberado($usuario->getCadLiberado());
	
	}

	public function criarComVars($id, $nome, $sobrenome, $email, $senha, $salt, $cadLiberado){

		$this->setId($id);
		$this->setNome($nome);
		$this->setSobrenome($sobrenome);
		$this->setEmail($email);
		$this->setSenha($senha);
		$this->setSalt($salt);
		$this->setCadLiberado($cadLiberado);
	
	}
	// =========
}
?>