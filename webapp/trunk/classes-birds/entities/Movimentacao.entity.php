<?php

/**
 Entity for table tb_movimentacao
 */
class Movimentacao{
	
	// VARS
	/**
	 *
	 * @var mov_id - integer - PK
	 */
	private $id;

	/**
	 *
	 * @var mov_pro_id - entity
	 */
	private $projeto;

	/**
	 *
	 * @var mov_con_id - entity
	 */
	private $conta;

	/**
	 *
	 * @var mov_usu_id - entity
	 */
	private $usuario;

	/**
	 *
	 * @var mov_mc_id - entity
	 */
	private $categoria;

	/**
	 *
	 * @var mov_descricao - varchar(80)
	 */
	private $descricao;
	
	/**
	 *
	 * @var mov_observacao - text
	 */
	private $observacao;
	
	/**
	 *
	 * @var mov_dt_competencia - date
	 */
	private $dt_competencia;
	
	/**
	 *
	 * @var mov_dt_vencimento - date
	 */
	private $dt_vencimento;
	
	/**
	 *
	 * @var mov_valor - float
	 */
	private $valor;
	
	/**
	 *
	 * @var mov_dt_pagamento - date
	 */
	private $dt_pagamento;
	
	/**
	 *
	 * @var mov_valor_pago - float
	 */
	private $valor_pago;
	
	/**
	 *
	 * @var mov_id_parcelado - integer
	 */
	private $id_parcelado;
	
	/**
	 *
	 * @var mov_parcela - integer
	 */
	private $parcela;
	
	/**
	 *
	 * @var mov_deletado - boolean
	 */
	private $deletado;
	
	/**
	 *
	 * @var mov_transferencia_id - integer
	 */
	private $transferencia_id;
	
	/**
	 *
	 * @var mov_transferencia_tipo - entity
	 */
	private $transferencia_tipo;
	
	/**
	 * 
	 * @var integer
	 */
	private $qtde_parcelas;
	// ====
	
	// GETTER
	public function getId(){

		return $this->id;
	
	}

	public function getProjeto(){

		return $this->projeto;
	
	}

	public function getConta(){

		return $this->conta;
	
	}

	public function getUsuario(){

		return $this->usuario;
	
	}

	public function getCategoria(){

		return $this->categoria;
	
	}

	public function getDescricao(){

		return $this->descricao;
	
	}
	
	public function getObservacao(){
	
		return $this->observacao;
	
	}
	
	public function getDtCompetencia(){
	
		return $this->dt_competencia;
	
	}
	
	public function getDtVencimento(){
	
		return $this->dt_vencimento;
	
	}
	
	public function getValor(){
	
		return $this->valor;
	
	}
	
	public function getDtPagamento(){
	
		return $this->dt_pagamento;
	
	}
	
	public function getValorPago(){
	
		return $this->valor_pago;
	
	}
	
	public function getIdParcelado(){
	
		return $this->id_parcelado;
	
	}
	
	public function getParcela(){
	
		return $this->parcela;
	
	}
	
	public function getDeletado(){
	
		return $this->deletado;
	
	}
	
	public function getTransferenciaId(){
	
		return $this->transferencia_id;
	
	}
	
	public function getTransferenciaTipo(){
	
		return $this->transferencia_tipo;
	
	}
	
	public function getQtdeParcelas(){
		return $this->qtde_parcelas;
	}
	// ======
	
	// SETTER
	public function setId($id){

		$this->id = $id;
		$this->checkCampoNull($this, "id");
	
	}

	public function setProjeto(Projeto $projeto=null){

		$this->projeto = $projeto;
		$this->checkCampoNull($this, "projeto");
	
	}

	public function setConta(Conta $conta=null){

		$this->conta = $conta;
		$this->checkCampoNull($this, "conta");
	
	}

	public function setUsuario(Usuario $usuario=null){

		$this->usuario = $usuario;
		$this->checkCampoNull($this, "usuario");
	
	}

	public function setCategoria(MovimentacaoCat $categoria=null){

		$this->categoria = $categoria;
		$this->checkCampoNull($this, "categoria");
	
	}

	public function setDescricao($descricao){

		$this->descricao = $descricao;
		$this->checkCampoNull($this, "descricao");
	
	}
	
	public function setObservacao($observacao){
	
		$this->observacao = $observacao;
		$this->checkCampoNull($this, "observacao");
	
	}
	
	public function setDtCompetencia($dt_competencia){
	
		$this->dt_competencia = $dt_competencia;
		$this->checkCampoNull($this, "dt_competencia");
	
	}
	
	public function setDtVencimento($dt_vencimento){
	
		$this->dt_vencimento = $dt_vencimento;
		$this->checkCampoNull($this, "dt_vencimento");
	
	}
	
	public function setValor($valor){
	
		$this->valor = $valor;
		$this->checkCampoNull($this, "valor");
	
	}
	
	public function setDtPagamento($dt_pagamento){
	
		$this->dt_pagamento = $dt_pagamento;
		$this->checkCampoNull($this, "dt_pagamento");
	
	}
	
	public function setValorPago($valor_pago){
	
		$this->valor_pago = $valor_pago;
		$this->checkCampoNull($this, "valor_pago");
	
	}
	
	public function setIdParcelado($id_parcelado){
	
		$this->id_parcelado = $id_parcelado;
		$this->checkCampoNull($this, "id_parcelado");
	
	}
	
	public function setParcela($parcela){
	
		$this->parcela = $parcela;
		$this->checkCampoNull($this, "parcela");
	
	}
	
	public function setDeletado($deletado){
	
		$this->deletado = $deletado;
		$this->checkCampoNull($this, "deletado");
	
	}
	
	public function setTransferenciaId($transferencia_id){
	
		$this->transferencia_id = $transferencia_id;
		$this->checkCampoNull($this, "transferencia_id");
	
	}
	
	public function setTransferenciaTipo(MovimentacaoTipo $transferencia_tipo=null){
		$this->transferencia_tipo = $transferencia_tipo;
		$this->checkCampoNull($this, "transferencia_tipo");
	}
	
	public function setQtdeParcelas($qtdeParcelas){
		$this->qtde_parcelas = $qtdeParcelas;
		$this->checkCampoNull($this, "qtde_parcelas");
	}
	// ======
	
	// CONSTRUCT
	function __construct(){

		$qtdParametros = ( int ) func_num_args();
		
		if ($qtdParametros > 0) {
			$param = func_get_args();
			
			if ($qtdParametros == 1 && $param [0] instanceof Movimentacao) {
				$this->criarMovimentacao($param [0]);
			}
			else {
				$this->criarComVars($param [0], $param [1], $param [2], $param [3], $param [4], $param [5], $param [6], $param [7], $param [8], $param [9], $param [10], $param [11], $param [12], $param [13], $param [14], $param [15], $param [16], $param [17]);
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

	public function criarMovimentacao(Movimentacao $movimentacao){

		$this->setId($movimentacao->getId());
		$this->setProjeto($movimentacao->getProjeto());
		$this->setConta($movimentacao->getConta());
		$this->setUsuario($movimentacao->getUsuario());
		$this->setCategoria($movimentacao->getCategoria());
		$this->setDescricao($movimentacao->getDescricao());
		$this->setObservacao($movimentacao->getObservacao());
		$this->setDtCompetencia($movimentacao->getDtCompetencia());
		$this->setDtVencimento($movimentacao->getDtVencimento());
		$this->setValor($movimentacao->getValor());
		$this->setDtPagamento($movimentacao->getDtPagamento());
		$this->setValorPago($movimentacao->getValorPago());
		$this->setIdParcelado($movimentacao->getIdParcelado());
		$this->setParcela($movimentacao->getParcela());
		$this->setDeletado($movimentacao->getDeletado());
		$this->setTransferenciaId($movimentacao->getTransferenciaId());
		$this->setTransferenciaTipo($movimentacao->getTransferenciaTipo());
		$this->setQtdeParcelas($movimentacao->getQtdeParcelas());
	
	}

	public function criarComVars($id, Projeto $projeto=null, Conta $conta=null, Usuario $usuario=null, MovimentacaoCat $categoria=null, $descricao, $observacao, $dt_competencia, $dt_vencimento, $valor, $dt_pagamento, $valor_pago, $id_parcelado, $parcela, $deletado, $qtdeParcelas=null, $transferencia_id=null, MovimentacaoTipo $transferencia_tipo=null){

		$this->setId($id);
		$this->setProjeto($projeto);
		$this->setConta($conta);
		$this->setUsuario($usuario);
		$this->setCategoria($categoria);
		$this->setDescricao($descricao);
		$this->setObservacao($observacao);
		$this->setDtCompetencia($dt_competencia);
		$this->setDtVencimento($dt_vencimento);
		$this->setValor($valor);
		$this->setDtPagamento($dt_pagamento);
		$this->setValorPago($valor_pago);
		$this->setIdParcelado($id_parcelado);
		$this->setParcela($parcela);
		$this->setDeletado($deletado);
		$this->setTransferenciaId($transferencia_id);
		$this->setTransferenciaTipo($transferencia_tipo);
		$this->setQtdeParcelas($qtdeParcelas);
	
	}
	// =========
}
?>