<?php

class MovimentacaoCatModel extends BaseModel{
	
	//@todo ver um jeito de deixar readonly
	private $MovTpEntrada = 1;
	private $MovTpSaida = 2;
	
	public function index() {
		return $this->viewModel;
	}
	
	/**
	 * 
	 * @param array $arrCat
	 * @return multitype:multitype:
	 */
	public function separaArrayCat($arrCat){
		$arrEntrada = array();
		$arrSaida = array();
		
		foreach($arrCat as $linha){
			$MovTipo = $linha["mc_mt_id"];
			
			if($MovTipo == $this->MovTpEntrada){
				array_push($arrEntrada, $linha);
			}
			else if($MovTipo == $this->MovTpSaida){
				array_push($arrSaida, $linha);
			}
		}
		
		return array($arrEntrada, $arrSaida);
	}
	
	public function incluir() {
		return $this->viewModel;
	}
	
	public function editar() {
		return $this->viewModel;
	}
	
	public function getHtmlSubcat($arr_subcat){
		$html = "";
		
		if(count($arr_subcat) > 0){
			$html .= "<table class='table mb0'>";
			$html .= "  <tbody>";
			
			foreach($arr_subcat as $linha){
				$mc_id = $linha["mc_id"];
				$mc_descricao = $linha["mc_descricao"];
				
				$html .= "<tr>";
				$html .= "<td class='col-md-11'>$mc_descricao</td>";
				$html .= "<td class='col-md-1'>
						    <a id='lnk-delete-subcategorias' href='javascript:void(0)' data-id='$mc_id'>
							  <i style='font-size: 16px;' class='fa fa-trash'></i>
							</a>
						  </td>";
				$html .= "</tr>";
			}
			
			$html .= "  </tbody>";
			$html .= "</table>";
		}
		
		return $html;
	}

}

?>
