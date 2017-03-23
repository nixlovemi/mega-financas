<?php

/**
 Entity for table tb_movimentacao_anexo
 */
class MovimentacaoAnexo{
	
	// VARS
	/**
	 *
	 * @var ma_id - integer - PK
	 */
	private $id;

	/**
	 *
	 * @var ma_mov_id - entity
	 */
	private $movimentacao;

	/**
	 *
	 * @var ma_arquivo - varchar(100)
	 */
	private $arquivo;
	// ====
	
	// GETTER
	public function getId(){

		return $this->id;
	
	}

	public function getMovimentacao(){

		return $this->movimentacao;
	
	}

	public function getArquivo(){

		return $this->arquivo;
	
	}
	// ======
	
	// SETTER
	public function setId($id){

		$this->id = $id;
		$this->checkCampoNull($this, "id");
	
	}

	public function setMovimentacao(Movimentacao $movimentacao=null){

		$this->movimentacao = $movimentacao;
		$this->checkCampoNull($this, "movimentacao");
	
	}

	public function setArquivo($arquivo){

		$this->arquivo = $arquivo;
		$this->checkCampoNull($this, "arquivo");
	
	}
	// ======
	
	// CONSTRUCT
	function __construct(){

		$qtdParametros = ( int ) func_num_args();
		
		if ($qtdParametros > 0) {
			$param = func_get_args();
			
			if ($qtdParametros == 1 && $param [0] instanceof MovimentacaoAnexo) {
				$this->criarMovimentacaoAnexo($param [0]);
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

	public function criarMovimentacaoAnexo(MovimentacaoAnexo $movimentacaoAnexo){

		$this->setId($movimentacaoAnexo->getId());
		$this->setMovimentacao($movimentacaoAnexo->getMovimentacao());
		$this->setArquivo($movimentacaoAnexo->getArquivo());
	
	}

	public function criarComVars($id, Movimentacao $movimentacao=null, $arquivo){

		$this->setId($id);
		$this->setMovimentacao($movimentacao);
		$this->setArquivo($arquivo);
	
	}
	// =========
}
?>