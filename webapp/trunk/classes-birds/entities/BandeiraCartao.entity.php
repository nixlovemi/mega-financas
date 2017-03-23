<?php

/**
 Entity for table tb_bandeira_cartao
 */
class BandeiraCartao{
	
	// VARS
	/**
	 *
	 * @var bc_id - integer - PK
	 */
	private $id;

	/**
	 *
	 * @var bc_descricao - varchar(30)
	 */
	private $descricao;

	/**
	 *
	 * @var bc_mini_imagem - varchar(100)
	 */
	private $mini_imagem;

	/**
	 *
	 * @var bc_ativo - boolean
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

	public function getMiniImagem(){

		return $this->mini_imagem;
	
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

	public function setMiniImagem($miniImagem){

		$this->mini_imagem = $miniImagem;
		$this->checkCampoNull($this, "mini_imagem");
	
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
			
			if ($qtdParametros == 1 && $param [0] instanceof BandeiraCartao) {
				$this->criarBandeiraCartao($param [0]);
			}
			else {
				$this->criarComVars($param [0], $param [1], $param [2], $param [3]);
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

	public function criarBandeiraCartao(BandeiraCartao $bandeiraCartao){

		$this->setId($bandeiraCartao->getId());
		$this->setDescricao($bandeiraCartao->getDescricao());
		$this->setMiniImagem($bandeiraCartao->getMiniImagem());
		$this->setAtivo($bandeiraCartao->getAtivo());
	
	}

	public function criarComVars($id, $descricao, $miniImagem, $ativo){

		$this->setId($id);
		$this->setDescricao($descricao);
		$this->setMiniImagem($miniImagem);
		$this->setAtivo($ativo);
	
	}
	// =========
}
?>