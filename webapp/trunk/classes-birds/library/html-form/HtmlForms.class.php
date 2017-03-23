<?php

class HtmlForms{

	private $name;

	private $id;

	private $action;

	private $labelColumnSize;

	private $lines;

	private $buttons;
	
	// construct
	function __construct(){

		$this->lines = array ();
		$this->buttons = array ();
		$this->labelColumnSize = 2;
	
	}
	// =========
	
	// SETS
	public function setName($name){

		$this->name = $name;
	
	}

	public function setId($id){

		$this->id = $id;
	
	}

	public function setAction($action){

		$this->action = $action;
	
	}

	public function setLabelColSize($labelColumnSize){

		$this->labelColumnSize = $labelColumnSize;
	
	}
	// ====
	
	// GETS
	public function getName(){

		return $this->name;
	
	}

	public function getId(){

		return $this->id;
	
	}

	public function getAction(){

		return $this->action;
	
	}

	/**
	 * Tamanho da coluna LABEL.
	 * Valores aceitos: 1 a 10.
	 * --------
	 */
	public function getLabelColSize(){

		return $this->labelColumnSize;
	
	}
	// ====
	public function addLine($label, $htmlInput, $full = false){

		array_push($this->lines, array ('label'=>$label, 'input'=>$htmlInput, 'full'=>$full));
	
	}

	public function addButton($htmlButton){

		array_push($this->buttons, $htmlButton);
	
	}

	public function clearLines(){

		$this->lines = array ();
	
	}

	public function getTable(){

		$html = "";
		
		// lines
		foreach ( $this->lines as $key => $line ) {
			
			if ($line ['full']) {
				
				$html .= "<div class='form-group'>";
				$html .= "  <div class='col-sm-12'>";
				$html .= "  " . $line ['input'];
				$html .= "  </div>";
				$html .= "</div>";
				
			}
			else {
				
				$html .= "<div class='form-group'>";
				$html .= "  <label class='col-sm-" . $this->labelColumnSize . " col-sm-" . $this->labelColumnSize . " control-label'>" . $line ['label'] . "</label>";
				$html .= "  <div class='col-sm-" . (12 - $this->labelColumnSize) . "'>";
				$html .= "  " . $line ['input'];
				$html .= "  </div>";
				$html .= "</div>";
				
			}
		}
		// =====
		
		return $html;
	
	}

	public function getHtmlForm($html_content = ""){

		$html = "";
		$html .= "<form class='form-horizontal adminex-form ajax-form-birds' method='post' name='" . $this->name . "' id='" . $this->id . "' action='" . $this->action . "'>";
		
		if ($html_content == "") {
			$html .= $this->getTable();
		}
		else {
			$html .= $html_content;
		}
		
		// buttons
		if (count($this->buttons) > 0) {
			$html .= "<div class='form-group'>";
			$html .= "  <div class='col-lg-offset-" . $this->labelColumnSize . " col-lg-10'>";
			
			foreach ( $this->buttons as $key => $value ) {
				$html .= $value;
			}
			
			$html .= "  </div>";
			$html .= "</div>";
		}
		
		$html .= "</form>";
		return $html;
	
	}

}
?>