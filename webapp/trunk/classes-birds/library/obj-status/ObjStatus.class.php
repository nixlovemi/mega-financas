<?php

class ObjStatus{

	const COD_STATUS_OK = 'OK';

	const COD_STATUS_ERRO = 'ERRO';

	private $status;

	private $arrRet;

	function __construct(){

		$this->arrRet = array ();
	
	}
	
	// SETS
	/**
	 * seta o status do objeto.
	 * usa-se as contantes COD_STATUS_OK | COD_STATUS_ERRO
	 *
	 * @param
	 *        	COD_STATUS_OK | COD_STATUS_ERRO -> $cod_status
	 */
	function setStatus($cod_status){

		$this->status = $cod_status;
	
	}

	/**
	 * adiciona uma linha de retorno.
	 *
	 * @param VARIANT $key        	
	 * @param VARIANT $value        	
	 */
	function addRet($key, $value){

		$this->arrRet [$key] = $value;
	
	}
	// ----
	
	// GETS
	/**
	 * retorna se o status do objeto eh OK
	 *
	 * @return boolean
	 */
	function isOk(){

		return $this->status == $this::COD_STATUS_OK;
	
	}

	/**
	 * retorna se o status do objeto eh ERRO
	 *
	 * @return boolean
	 */
	function isErro(){

		return $this->status == $this::COD_STATUS_ERRO;
	
	}

	/**
	 * pega o array de retorno (pode ser vazio)
	 *
	 * @return Array <VARIANT, VARIANT>
	 */
	function getArrRet(){

		return $this->arrRet;
	
	}

	/**
	 * pega um valor específico do array retorno
	 *
	 * @param VARIANTE $key        	
	 * @return VARIANT
	 */
	function getRetByKey($key){

		return (key_exists($key, $this->arrRet)) ? $this->arrRet [$key]: "";
	
	}
	// ----
}

?>