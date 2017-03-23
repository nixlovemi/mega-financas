<?php

/**

Project: Birds

Purpose: Class to generate HTML inputs (not HTML5, yet). Only for the Birds template.

Author: Leandro Nix
  
*/
class HtmlInputs{

	Const INPT_SIZE_BLOCK = "0";

	Const INPT_SIZE_SMALL = "2";

	Const INPT_SIZE_MEDIUM = "4";

	Const INPT_SIZE_LARGE = "7";

	Const INPT_BTN_DEFAULT = "btn-default";

	Const INPT_BTN_PRIMARY = "btn-primary";

	Const INPT_BTN_SUCCESS = "btn-success";

	Const INPT_BTN_INFO = "btn-info";

	Const INPT_BTN_WARNING = "btn-warning";

	Const INPT_BTN_DANGER = "btn-danger";

	Const INPT_BTN_SUBMIT = "submit";

	/**
	 * Gera o html do input[text]
	 * --------
	 * disabled / readonly: true or false
	 * placeholder: texto auxiliar "dentro" do input
	 * style: array de estilos especificos. Ex: array('width:10px', 'color:white');
	 * class: array de classes especificas. Ex: array('first', 'btnTeste');
	 * customAttr: array de atributos especificos: Ex: array('attr-cor="#00FF00"', 'aNomePai="field1"');
	 * inptSize: usar as constantes INPT_SIZE_BLOCK, INPT_SIZE_SMALL ...
	 */
	public function getInputText($name, $id, $value, $placeholder = "", $maxlength = '', $inptSize = "0", $password = false, $disabled = false, $readonly = false, $style = array(), $class = array(), $java = "", $customAttr = array()){

		$type = ($password) ? 'password' : 'text';
		$html = '';
		$sizeBef = "";
		$sizeAft = "";
		
		if ($inptSize == $this::INPT_SIZE_SMALL || $inptSize == $this::INPT_SIZE_MEDIUM || $inptSize == $this::INPT_SIZE_LARGE) {
			$sizeBef = "<div class='col-md-" . $inptSize . " form-group'>";
			$sizeAft = "</div>";
		}
		
		$html .= $sizeBef;
		$html .= '<input type="' . $type . '" name="' . $name . '" id="' . $id . '" value="' . $value . '" ';
		
		if ($disabled == true) {
			$html .= ' disabled="disabled" ';
		}
		
		if ($readonly == true) {
			$html .= ' READONLY ';
		}
		
		if ($placeholder != '') {
			$html .= ' placeholder="' . $placeholder . '" ';
		}
		
		if (count($style) > 0) {
			$html .= ' style="' . implode(';', $style) . '" ';
		}
		
		if (count($customAttr) > 0) {
			$html .= ' ' . implode(' ', $customAttr) . ' ';
		}
		
		array_push($class, 'form-control');
		$html .= ' class="' . implode(' ', $class) . '" ';
		
		if ($maxlength != '') {
			$html .= ' maxlength="' . $maxlength . '" ';
		}
		
		if ($java != '') {
			$html .= ' ' . $java . ' ';
		}
		
		$html .= ' />';
		$html .= $sizeAft;
		
		return $html;
	
	}

	/**
	 * Gera o html do input[text] com um texto/icone/html na frente
	 */
	public function createPrependInput($prependHtml, $name, $id, $value, $placeholder = "", $maxlength = '', $inptSize = "0", $password = false, $disabled = false, $readonly = false, $style = array(), $class = array(), $java = "", $customAttr = array()){

		$html = ' <div class="input-group">';
		$html .= '  <span class="input-group-addon">' . $prependHtml . '</span>';
		// $this->isPrepend = true;
		$html .= $this->getInputText($name, $id, $value, $placeholder, $maxlength, $inptSize, $password, $disabled, $readonly, $style, $class, $java, $customAttr);
		$html .= '</div>';
		
		// $this->isPrepend = false;
		return $html;
	
	}

	/**
	 * Gera o html do input[text] com máscara para Moedas
	 */
	public function createOnlyNumbersInput($name, $id, $value, $placeholder = "", $maxlength = '', $inptSize = "0", $password = false, $disabled = false, $readonly = false, $style = array(), $class = array(), $java = "", $customAttr = array()){

		array_push($class, 'inpt_only_numbers');
		$password = false;
		
		return $this->getInputText($name, $id, $value, $placeholder, $maxlength, $inptSize, $password, $disabled, $readonly, $style, $class, $java, $customAttr);
	
	}

	/**
	 * Gera o html da select (combo) a partir de uma SQL.
	 * O primeiro campo da select sera o VALUE e o segundo campo da select sera o TEXT.
	 * O valor selecionado sera comparado com o primeiro campo da SQL.
	 */
	public function createSelectSql($name, $id, $sql, $value = '', $optVazia = '', $multiple = false, $class = array(), $style = array(), $disabled = false, $customAttr = array(), $java="", $tp_chosen=false){

		require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/connection/Connection.class.php';
		$conn = new Connection();
		
		if (is_array($sql)) {
			$arrResp = $sql;
		}
		else {
			$conn->setSQL($sql);
			$arrResp = $conn->select();
		}
		
		if (! $conn->getError()) {
			$arrValue = array ();
			$arrText = array ();
			foreach ( $arrResp as $linha ) {				
				$slice1 = array_slice($linha, 0, 1);
				$slice2 = array_slice($linha, 1, 1);
				
				$shift1 = array_shift($slice1);
				$shift2 = array_shift($slice2);
				
				array_push($arrValue, utf8_decode($shift1));
				array_push($arrText, utf8_decode($shift2));
			}
			
			return $this->createSelect($name, $id, $arrValue, $arrText, $value, $optVazia, $multiple, $class, $style, $disabled, $customAttr, $java, $tp_chosen);
		}
		else {
			die($conn->getErros());
			return;
		}
	
	}

	/**
	 * Gera o html da select (combo)
	 */
	public function createSelect($name, $id, $arrValue, $arrText, $value = '', $optVazia = '', $multiple = false, $class = array(), $style = array(), $disabled = false, $customAttr = array(), $java="", $tp_chosen=false){

		$class = (! is_array($class)) ? array () : $class;
		$style = (! is_array($style)) ? array () : $style;
		$customAttr = (! is_array($customAttr)) ? array () : $customAttr;
		
		$txtMultiple = ($multiple == true) ? ' multiple ' : '';
		$txtDisabled = ($disabled) ? ' disabled="disabled" ' : '';
		array_push($class, 'form-control');
		
		if($tp_chosen){
			array_push($class, 'chosen-multiple');
		}
		
		if ($optVazia === true) {
			$optVazia = "Selecione";
		}
		(! empty($optVazia)) ? (array_push($class, 'emptySelect')) : ("");
		
		$html = '<select ' . $txtDisabled . ' ' . $txtMultiple . ' name="' . $name . '" id="' . $id . '" ';
		$html .= ' class="' . implode(' ', $class) . '" ';
		
		if (count($style) > 0) {
			$html .= ' style="' . implode(';', $style) . '" ';
		}
		
		if (count($customAttr) > 0) {
			$html .= ' ' . implode(' ', $customAttr) . ' ';
		}
		
		if( $java <> "" ){
			
			$html .= ' '.$java.' ';
			
		}
		
		$html .= '>';
		
		if (! empty($optVazia)) {
			$sel = ($value == '') ? 'selected="selected"' : '';
			
			$html .= '<option ' . $sel . ' value="">' . utf8_encode($optVazia) . '</option>';
		}
		
		for($i = 0; $i < count($arrValue); $i ++) {
			if (is_string($arrValue [$i]) && is_string($value)) {
				$arrValue [$i] = (strtolower($arrValue [$i]) == 'true') ? 't' : $arrValue [$i];
				$arrValue [$i] = (strtolower($arrValue [$i]) == 'false') ? 'f' : $arrValue [$i];
				
				$sel = (utf8_encode($arrValue [$i]) == utf8_encode($value)) ? 'selected="selected"' : '';
			}
			else {
				$sel = ($arrValue [$i] == $value) ? 'selected="selected"' : '';
			}
			
			$html .= '<option ' . $sel . ' value="' . utf8_encode($arrValue [$i]) . '">' . utf8_encode($arrText [$i]) . '</option>';
		}
		
		$html .= '</select>';
		
		return $html;
	
	}

	/**
	 * Gera o html do button
	 * --------
	 * btnType: usar as constantes INPT_BTN_DEFAULT, INPT_BTN_PRIMARY...
	 * onclick: funcao js OU constante INPT_BTN_SUBMIT
	 */
	public function getButton($name, $id, $value, $onclick = '', $btnType = "btn-default", $disabled = false, $style = array(), $class = array(), $java = "", $customAttr = array()){

		$html = "";
		
		$type = ($onclick == $this::INPT_BTN_SUBMIT) ? 'submit' : 'button';
		$html .= "<button type='" . $type . "' name='" . $name . "' id='" . $id . "' ";
		
		if ($onclick != "" && $onclick != $this::INPT_BTN_SUBMIT) {
			$html .= " onclick='" . $onclick . "' ";
		}
		
		if ($disabled) {
			$html .= " disabled ";
		}
		
		if (count($style) > 0) {
			$html .= ' style="' . implode(';', $style) . '" ';
		}
		
		array_push($class, 'btn');
		array_push($class, $btnType);
		$html .= ' class="' . implode(' ', $class) . '" ';
		
		if ($java != '') {
			$html .= ' ' . $java . ' ';
		}
		
		if (count($customAttr) > 0) {
			$html .= ' ' . implode(' ', $customAttr) . ' ';
		}
		
		$html .= ">" . $value . "</button>";
		return $html;
	
	}

	/**
	 * Gera o html do input[text] com DatePicker
	 */
	public function createDateInput($name, $id, $value, $placeholder = "", $disabled = false, $readonly = false, $style = array(), $class = array(), $java = "", $customAttr = array(), $size='2'){

		array_push($class, 'bs_datepicker');
		$prependHtml = '<i class="fa fa-calendar"></i>';
		$password = false;
		
		return $this->createPrependInput($prependHtml, $name, $id, $value, $placeholder, 10, $size, false, $disabled, $readonly, $style, $class, $java, $customAttr);
	
	}

}
?>