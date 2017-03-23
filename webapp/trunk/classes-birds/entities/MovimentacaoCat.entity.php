<?php

/**
 Entity for table tb_movimentacao_cat
 */
class MovimentacaoCat{
	
	// VARS
	/**
	 *
	 * @var mc_id - integer - PK
	 */
	private $id;

	/**
	 *
	 * @var mc_usu_id - entity
	 */
	private $usuario;

	/**
	 *
	 * @var mc_descricao - varchar(50)
	 */
	private $descricao;

	/**
	 *
	 * @var mc_ativo - boolean
	 */
	private $ativo;

	/**
	 *
	 * @var mc_id_pai - entity
	 */
	private $categoria_pai;

	/**
	 *
	 * @var mc_mt_id - entity
	 */
	private $movimentacao_tipo;
	
	// ====
	
	// GETTER
	public function getId(){

		return $this->id;
	
	}

	public function getUsuario(){

		return $this->usuario;
	
	}

	public function getDescricao(){

		return $this->descricao;
	
	}

	public function getAtivo(){

		return $this->ativo;
	
	}

	public function getCategoriaPai(){

		return $this->categoria_pai;
	
	}

	public function getMovimentacaoTipo(){

		return $this->movimentacao_tipo;
	
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

	public function setDescricao($descricao){

		$this->descricao = $descricao;
		$this->checkCampoNull($this, "descricao");
	
	}

	public function setAtivo($ativo){

		$this->ativo = $ativo;
		$this->checkCampoNull($this, "ativo");
	
	}

	public function setCategoriaPai(MovimentacaoCat $categoria_pai=null){

		$this->categoria_pai = $categoria_pai;
		$this->checkCampoNull($this, "categoria_pai");
	
	}

	public function setMovimentacaoTipo(MovimentacaoTipo $movimentacao_tipo=null){

		$this->movimentacao_tipo = $movimentacao_tipo;
		$this->checkCampoNull($this, "movimentacao_tipo");
	
	}
	// ======
	
	// CONSTRUCT
	function __construct(){

		$qtdParametros = ( int ) func_num_args();
		
		if ($qtdParametros > 0) {
			$param = func_get_args();
			
			if ($qtdParametros == 1 && $param [0] instanceof MovimentacaoCat) {
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

	public function criarAtalho(MovimentacaoCat $movimentacaoCat){

		$this->setId($movimentacaoCat->getId());
		$this->setUsuario($movimentacaoCat->getUsuario());
		$this->setDescricao($movimentacaoCat->getDescricao());
		$this->setAtivo($movimentacaoCat->getAtivo());
		$this->setCategoriaPai($movimentacaoCat->getCategoriaPai());
		$this->setMovimentacaoTipo($movimentacaoCat->getMovimentacaoTipo());
	
	}

	public function criarComVars($id, Usuario $usuario=null, $descricao, $ativo, MovimentacaoCat $categoria_pai=null, MovimentacaoTipo $movimentacao_tipo=null){

		$this->setId($id);
		$this->setUsuario($usuario);
		$this->setDescricao($descricao);
		$this->setAtivo($ativo);
		$this->setCategoriaPai($categoria_pai);
		$this->setMovimentacaoTipo($movimentacao_tipo);
	
	}
	// =========
}
?>