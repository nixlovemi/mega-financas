<?php

/**
 Entity for table tb_usuario_fcbk
 */
class UsuarioFcbk{
	
	// VARS
	/**
	 *
	 * @var uf_id - integer - PK
	 */
	private $id;

	/**
	 *
	 * @var uf_usu_id - entity
	 */
	private $usuario;

	/**
	 *
	 * @var uf_fb_usu_id - varchar(40)
	 */
	private $fb_usu_id;

	/**
	 *
	 * @var uf_fb_prim_nome - varchar(80)
	 */
	private $fb_prim_nome;

	/**
	 *
	 * @var uf_fb_sobrenome - varchar(80)
	 */
	private $fb_sobrenome;

	/**
	 *
	 * @var uf_fb_nomecompleto - varchar(160)
	 */
	private $fb_nomecompleto;
	
	/**
	 *
	 * @var uf_fb_email - varchar(100)
	 */
	private $fb_email;
	
	/**
	 *
	 * @var uf_fb_sexo - varchar(20)
	 */
	private $fb_sexo;
	
	/**
	 *
	 * @var uf_fb_foto - text
	 */
	private $fb_foto;
	// ====
	
	// GETTER
	public function getId(){

		return $this->id;
	
	}

	public function getUsuario(){

		return $this->usuario;
	
	}

	public function getFbUsuId(){

		return $this->fb_usu_id;
	
	}

	public function getFbPrimNome(){

		return $this->fb_prim_nome;
	
	}

	public function getFbSobrenome(){

		return $this->fb_sobrenome;
	
	}

	public function getFbNomecompleto(){

		return $this->fb_nomecompleto;
	
	}
	
	public function getFbEmail(){
	
		return $this->fb_email;
	
	}
	
	public function getFbSexo(){
	
		return $this->fb_sexo;
	
	}
	
	public function getFbFoto(){
	
		return $this->fb_foto;
	
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

	public function setFbUsuId($fbUsuId){

		$this->fb_usu_id = $fbUsuId;
		$this->checkCampoNull($this, "fb_usu_id");
	
	}

	public function setFbPrimNome($fbPrimNome){

		$this->fb_prim_nome = $fbPrimNome;
		$this->checkCampoNull($this, "fb_prim_nome");
	
	}

	public function setFbSobrenome($fbSobrenome){

		$this->fb_sobrenome = $fbSobrenome;
		$this->checkCampoNull($this, "fb_sobrenome");
	
	}

	public function setFbNomecompleto($fbNomecompleto){

		$this->fb_nomecompleto = $fbNomecompleto;
		$this->checkCampoNull($this, "fb_nomecompleto");
	
	}
	
	public function setFbEmail($fbEmail){
	
		$this->fb_email = $fbEmail;
		$this->checkCampoNull($this, "fb_email");
	
	}
	
	public function setFbSexo($fbSexo){
	
		$this->fb_sexo = $fbSexo;
		$this->checkCampoNull($this, "fb_sexo");
	
	}
	
	public function setFbFoto($fbFoto){
	
		$this->fb_foto = $fbFoto;
		$this->checkCampoNull($this, "fb_foto");
	
	}
	// ======
	
	// CONSTRUCT
	function __construct(){

		$qtdParametros = ( int ) func_num_args();
		
		if ($qtdParametros > 0) {
			$param = func_get_args();
			
			if ($qtdParametros == 1 && $param [0] instanceof UsuarioFcbk) {
				$this->criarUsuarioFcbk($param [0]);
			}
			else {
				$this->criarComVars($param [0], $param [1], $param [2], $param [3], $param [4], $param [5], $param [6], $param [7], $param [8]);
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

	public function criarUsuarioFcbk(UsuarioFcbk $usuarioFcbk){

		$this->setId($usuarioFcbk->getId());
		$this->setUsuario($usuarioFcbk->getUsuario());
		$this->setFbUsuId($usuarioFcbk->getFbUsuId());
		$this->setFbPrimNome($usuarioFcbk->getFbPrimNome());
		$this->setFbSobrenome($usuarioFcbk->getFbSobrenome());
		$this->setFbNomecompleto($usuarioFcbk->getFbNomecompleto());
		$this->setFbEmail($usuarioFcbk->getFbEmail());
		$this->setFbSexo($usuarioFcbk->getFbSexo());
		$this->setFbFoto($usuarioFcbk->getFbFoto());
	
	}

	public function criarComVars($id, Usuario $usuario=null, $fbUsuId, $fbPrimNome, $fbSobrenome, $fbNomeCompleto, $fbEmail, $fbSexo, $fbFoto){

		$this->setId($id);
		$this->setUsuario($usuario);
		$this->setFbUsuId($fbUsuId);
		$this->setFbPrimNome($fbPrimNome);
		$this->setFbSobrenome($fbSobrenome);
		$this->setFbNomecompleto($fbNomeCompleto);
		$this->setFbEmail($fbEmail);
		$this->setFbSexo($fbSexo);
		$this->setFbFoto($fbFoto);
	
	}
	// =========
}
?>