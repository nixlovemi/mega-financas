<?php

/**
 * @desc Classe para controlar as respostas dos forms padrao Birds
 * @author Leandro
 *
 */
class HtmlFormResp{
	
	// VARS
	const TP_RESP_ALERT = "alert";

	const TP_RESP_FUNCTION = "function";

	const TP_RESP_SELECTOR = "selector";

	const TP_RESP_NOTY = "noty";
	
	const TP_RESP_VALUE = "value";

	const NOTY_TYPE_SUCCESS = "sucesso";

	const NOTY_TYPE_NORMAL = "normal";

	const NOTY_TYPE_ERROR = "erro";

	const NOTY_TYPE_ATENTION = "atencao";

	const NOTY_TYPE_INFO = "info";

	private $arrItens;

	private $arrReturn;
	// ====
	function __construct(){

		$this->arrItens = array ();
		$this->arrReturn = array ();
	
	}

	/**
	 * Adiciona um item de resposta ao form
	 *
	 * $tp_resp : usar constante TP_RESP_ALERT, TP_RESP_FUNCTION...
	 *
	 * $content : se for alert eh a msg do alert; se for function eh a str da funcao etc...
	 *
	 * $key : quando o tipo for selector, esse eh o seletor jquery; qdo for noty as opcoes sao NOTY_TYPE_SUCCESS, NOTY_TYPE_NORMAL ...
	 */
	public function addRespItem($tp_resp, $content, $key = ""){
		
		// Força o encoding
		// $content = utf8_encode($content);

		if ($tp_resp == $this::TP_RESP_ALERT || $tp_resp == $this::TP_RESP_FUNCTION) {
			array_push($this->arrItens, array ('tp_resp' => $tp_resp,'content' => $content));
		}
		else if (($tp_resp == $this::TP_RESP_SELECTOR || $tp_resp == $this::TP_RESP_NOTY || $tp_resp == $this::TP_RESP_VALUE) && $key != "") {
			array_push($this->arrItens, array ('tp_resp' => $tp_resp,'key' => $key,'content' => $content));
		}
	
	}

	/**
	 * Retorna os itens adicionados duma forma que o JS processe
	 */
	public function returnJsonResp(){

		array_push($this->arrReturn, $this->arrItens);
		return json_encode($this->arrReturn);
	
	}
	
	/**
	 * Limpa todos os dados armazenados
	 */
	public function resetAll(){
		$this->arrItens = array ();
		$this->arrReturn = array ();
	}

}
?>