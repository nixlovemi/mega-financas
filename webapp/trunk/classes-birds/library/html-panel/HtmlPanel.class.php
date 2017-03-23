<?php

class HtmlPanel{

	private $title;

	private $content;

	private $id;

	function __construct(){

		$this->title = "";
		$this->id = "panel_" . date('YmdHis');
	
	}

	public function setTitle($title){

		$this->title = $title;
	
	}

	public function setContent($content){

		$this->content = $content;
	
	}

	public function setId($id){

		$this->id = $id;
	
	}

	public function getPanelHtml($closed=false){

		$html = "";
		$html .= "<section class='panel' id='" . $this->id . "'>";
		
		if ($this->title != "") {
			$html .= "<header class='panel-heading'><a attr-id='" . $this->id . "' href='javascript:void(0);' class='lnk-title-obj-panel'>" . $this->title . "</a></header>";
		}
		
		$style = ($closed) ? 'display:none': '';
		
		$html .= "   <div style='".$style."' class='panel-body' id='pb_" . $this->id . "'>" . $this->content . "</div>";
		$html .= "</section>";
		
		return $html;
	
	}

}
?>