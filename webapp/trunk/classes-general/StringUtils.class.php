<?php

class StringUtils{
	
	/**
	 * 
	 * @param VARYANT $string
	 * @return boolean
	 */
	public function isStringOnlyText($string){
	
		return ctype_alpha($string);
	
	}
	
	/**
	 * Format a dd/mm/yyyy date to yyyy-mm-dd
	 * 
	 * @param string $date_str
	 * @return string
	 */
	public function strDateBrToBd($date_str){
		
		if( strpos($date_str, "/") > 0 ){
			
			$data_final = $date_str;
			$hora_final = "";
			
			if( strpos($data_final, ' ') > 0 ){
				$arr_hora = explode(' ', $data_final);
				$data_final = $arr_hora[0];
				$hora_final = ' ' . $arr_hora[1];
			}
			
			$arr_date = explode("/", $data_final);
			
			return $arr_date[2] . '-' . $arr_date[1] . '-' . $arr_date[0] . $hora_final;
			
		}
		else{
			
			return $date_str;
			
		}
		
	}
	
	/**
	 * formata string de valor BR para BD. Ex: 1.250,00 -> 1250.00
	 * 
	 * @param string $vlr_str
	 * @return string
	 */
	public function strVlrBrToBd($vlr_str){
		
		$vlr_str = str_replace("R$", "", $vlr_str);
		$vlr_str = str_replace(" ", "", $vlr_str);
		$vlr_str = str_replace(',', '.', str_replace('.', '', $vlr_str));
		
		return $vlr_str;
	
	}
	
	public function removeAcents($string){
		
		$string = utf8_decode($string);
		
		$string = preg_replace("/[באגדה]/", "a", $string);
	    $string = preg_replace("/[ÁÀÂÃÄ]/", "A", $string);
	    $string = preg_replace("/[יטך]/", "e", $string);
	    $string = preg_replace("/[ÉÈÊ]/", "E", $string);
	    $string = preg_replace("/[םל]/", "i", $string);
	    $string = preg_replace("/[ÍÌ]/", "I", $string);
	    $string = preg_replace("/[ףעפץצ]/", "o", $string);
	    $string = preg_replace("/[ÓÒÔÕÖ]/", "O", $string);
	    $string = preg_replace("/[תשü]/", "u", $string);
	    $string = preg_replace("/[ÚÙÜ]/", "U", $string);
	    $string = preg_replace("/ח/", "c", $string);
	    $string = preg_replace("/Ç/", "C", $string);
	    $string = preg_replace("/[][><}{)(:;,!?*%~^`&#@]/", "", $string);
	    $string = preg_replace("/ /", "_", $string);
	    
	    return $string;
		
	}

}
?>