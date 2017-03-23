<?php

class MailTemplates{

	private $arr_templates = array();
	
	private $arr_corpo = array();
	
	private $template_escolhido;

	public function __construct($template_nr){
		switch($template_nr){
			case 0:
				$this->template_escolhido = $template_nr;
				break;
			default:
				throw new Exception("Template escolhido n&atilde;o confere: <strong>$template_nr</strong>");
				break;
		}
		
		$this->inicializa_templates();
	}
	
	public function setBgColor($bg_color){
		$this->arr_templates[$this->template_escolhido]['BG_COLOR'] = $bg_color;
	}
	
	public function emptyLogoTopo(){
		$this->arr_templates[$this->template_escolhido]['LOGO_TOPO'] = "";
	}
	
	public function setLogoTopo($src, $alt, $link="javascript:void(0)"){
		$str_certa = str_replace(array('{{LINK}}', '{{SRC}}', '{{ALT}}'), array($link, $src, $alt), $this->arr_templates[$this->template_escolhido]['LOGO_TOPO']);
		$this->arr_templates[$this->template_escolhido]['LOGO_TOPO'] = $str_certa;
	}
	
	public function emptyFraseTopo(){
		$this->arr_templates[$this->template_escolhido]['FRASE_TOPO'] = "";
	}
	
	public function setFraseTopo($frase){
		$this->arr_templates[$this->template_escolhido]['FRASE_TOPO'] = "<p style='margin:0 0 36px; text-align:center; font-size:14px; line-height:20px; text-transform:uppercase; color:#626658;'>
                                    									   $frase
                                										 </p>";
	}
	
	public function emptyHeaderImg(){
		$this->arr_templates[$this->template_escolhido]['HEADER_IMG'] = "";
	}
	
	public function setHeaderImg($src, $alt){
		$str_certa = str_replace(array('{{SRC}}', '{{ALT}}'), array($src, $alt), $this->arr_templates[$this->template_escolhido]['HEADER_IMG']);
		$this->arr_templates[$this->template_escolhido]['HEADER_IMG'] = $str_certa;
	}
	
	public function emptyTitulo(){
		$this->arr_templates[$this->template_escolhido]['TITULO'] = "";
	}
	
	public function setTitulo($titulo){
		$this->arr_templates[$this->template_escolhido]['TITULO'] = "<p style='margin:0 30px 33px;; text-align:center; text-transform:uppercase; font-size:24px; line-height:30px; font-weight:bold; color:#484a42;'>
					                               					   $titulo
					                             					 </p>";
	}
	
	public function addCorpo2Col($img_e, $alt_e, $title_e, $txt_e, $link_e="javascript:void(0);", $img_d, $alt_d, $title_d, $txt_d, $link_d="javascript:void(0);"){
		$str_certa = str_replace(array('{{LINK_E}}', '{{SRC_E}}', '{{ALT_E}}', '{{TITLE_E}}', '{{TXT_E}}', '{{LINK_D}}', '{{SRC_D}}', '{{ALT_D}}', '{{TITLE_D}}', '{{TXT_D}}'), array($link_e, $img_e, $alt_e, $title_e, $txt_e, $link_d, $img_d, $alt_d, $title_d, $txt_d), $this->arr_templates[$this->template_escolhido]['CORPO_2_COL']);
		array_push($this->arr_corpo, $str_certa);
	}
	
	public function addCorpo1Col($img_e, $alt_e, $title_e, $txt_e, $link_e="javascript:void(0);"){
		$str_certa = str_replace(array('{{LINK}}', '{{SRC}}', '{{ALT}}', '{{TITLE}}', '{{TXT}}'), array($link_e, $img_e, $alt_e, $title_e, $txt_e), $this->arr_templates[$this->template_escolhido]['CORPO_1_COL']);
		array_push($this->arr_corpo, $str_certa);
	}
	
	private function getCorpo(){
		return implode("", $this->arr_corpo);
	}
	
	public function getTemplateHtml(){
		// template 0
		if($this->template_escolhido == 0){
			$LOGO_TOPO = $this->arr_templates[$this->template_escolhido]['LOGO_TOPO'];
			$FRASE_TOPO = $this->arr_templates[$this->template_escolhido]['FRASE_TOPO'];
			$IMG_HEADER = $this->arr_templates[$this->template_escolhido]['HEADER_IMG'];
			$TITULO = $this->arr_templates[$this->template_escolhido]['TITULO'];
			$CORPO = $this->getCorpo();
			
			$html = "<!doctype html>
					 <html>
					   <head>
					     <meta charset='utf-8'>
					     <title></title>
					   </head>
					   <body style='font-family:Gotham, 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color:#f0f2ea; margin:0; padding:0; color:#333333;'>
					     <table width='100%' bgcolor='#f0f2ea' cellpadding='0' cellspacing='0' border='0'>
					       <tbody>
					         <tr>
					           <td style='padding:40px 0;'>
					             <!-- begin main block -->
					             <table cellpadding='0' cellspacing='0' width='608' border='0' align='center'>
					               <tbody>
					                 <tr>
					                   <td>
					                     $LOGO_TOPO
					                     $FRASE_TOPO
					                     <!-- begin wrapper -->
					                     <table cellpadding='0' cellspacing='0' border='0' width='100%'>
					                       <tbody>
					                         <tr>
					                           <td width='8' height='4' colspan='2' style='background:url(http://demo.artlance.ru/email/shadow-top-left.png) no-repeat 100% 100%;'><p style='margin:0; font-size:1px; line-height:1px;'>&nbsp;</p></td>
					                           <td height='4' style='background:url(http://demo.artlance.ru/email/shadow-top-center.png) repeat-x 0 100%;'><p style='margin:0; font-size:1px; line-height:1px;'>&nbsp;</p></td>
					                           <td width='8' height='4' colspan='2' style='background:url(http://demo.artlance.ru/email/shadow-top-right.png) no-repeat 0 100%;'><p style='margin:0; font-size:1px; line-height:1px;'>&nbsp;</p></td>
					                         </tr>
					                         <tr>
					                           <td width='4' height='4' style='background:url(http://demo.artlance.ru/email/shadow-left-top.png) no-repeat 100% 0;'><p style='margin:0; font-size:1px; line-height:1px;'>&nbsp;</p></td>
					                           <td colspan='3' rowspan='3' bgcolor='#FFFFFF' style='padding:0 0 30px;'>
					                             <!-- begin content -->
					                             $IMG_HEADER
					                             $TITULO
					                             <!-- begin articles -->
					                             $CORPO
					                             <!-- /end articles -->
					                             <p style='margin:0; border-top:2px solid #e5e5e5; font-size:5px; line-height:5px; margin:0 30px 29px;'>&nbsp;</p>
					                             <table cellpadding='0' cellspacing='0' border='0' width='100%'>
					                               <tbody>
					                                 <tr valign='top'>
					                                   <td width='30'><p style='margin:0; font-size:1px; line-height:1px;'>&nbsp;</p></td>
					                                   <td>
					                                     <p style='margin:0 0 4px; font-weight:bold; color:#333333; font-size:14px; line-height:22px;'>Mega Finan&ccedil;as</p>
					                                     <p style='margin:0; color:#333333; font-size:11px; line-height:18px;'>
					                                       Website: <a href='http://megafinancas.com.br/' style='color:#6d7e44; text-decoration:none; font-weight:bold;'>www.megafinancas.com.br</a>
					                                     </p>
					                                   </td>
					                                   <td width='30'><p style='margin:0; font-size:1px; line-height:1px;'>&nbsp;</p></td>
					                                   <td width='120'>
					                                     <a href='https://www.facebook.com/megafinancas' style='float:left; width:24px; height:24px; margin:6px 8px 10px 0;'>
					                                       <img src='http://demo.artlance.ru/email/facebook.png' width='24' height='24' alt='facebook' style='display:block; margin:0; border:0; background:#eeeeee;'>
					                                     </a>
					                                     <a href='https://twitter.com/PixelBuddha' style='float:left; width:24px; height:24px; margin:6px 8px 10px 0;'>
					                                       <img src='http://demo.artlance.ru/email/twitter.png' width='24' height='24' alt='twitter' style='display:block; margin:0; border:0; background:#eeeeee;'>
					                                     </a>
					                                     <a href='http://blog.pixelbuddha.net/' style='float:left; width:24px; height:24px; margin:6px 8px 10px 0;;'>
					                                       <img src='http://demo.artlance.ru/email/tumblr.png' width='24' height='24' alt='tumblr' style='display:block; margin:0; border:0; background:#eeeeee;'>
					                                     </a>
					                                     <a href='http://pixelbuddha.net/rss' style='float:left; width:24px; height:24px; margin:6px 0 10px 0;'>
					                                       <img src='http://demo.artlance.ru/email/rss.png' width='24' height='24' alt='rss' style='display:block; margin:0; border:0; background:#eeeeee;'>
					                                     </a>
					                                                                <p style='margin:0; font-weight:bold; clear:both; font-size:12px; line-height:22px;'>
					                                                                    <a href='http://pixelbuddha.net/' style='color:#6d7e44; text-decoration:none;'>Visit website</a><br>
					                                                                    <a href='http://pixelbuddha.net/' style='color:#6d7e44; text-decoration:none;'>Mobile version</a>
					                                                                </p>
					                                                            </td>
					                                                            <td width='30'><p style='margin:0; font-size:1px; line-height:1px;'>&nbsp;</p></td>
					                                                        </tr>
					                                                    </tbody>
					                                                </table>
					                                                <!-- end content --> 
					                                            </td>
					                                            <td width='4' height='4' style='background:url(http://demo.artlance.ru/email/shadow-right-top.png) no-repeat 0 0;'><p style='margin:0; font-size:1px; line-height:1px;'>&nbsp;</p></td>
					                                        </tr>
					                                        
					                                        
					                                        <tr>
					                                            <td width='4' style='background:url(http://demo.artlance.ru/email/shadow-left-center.png) repeat-y 100% 0;'><p style='margin:0; font-size:1px; line-height:1px;'>&nbsp;</p></td>
					                                            <td width='4' style='background:url(http://demo.artlance.ru/email/shadow-right-center.png) repeat-y 0 0;'><p style='margin:0; font-size:1px; line-height:1px;'>&nbsp;</p></td>
					                                        </tr>
					                                        
					                                        <tr> 
					                                            <td width='4' height='4' style='background:url(http://demo.artlance.ru/email/shadow-left-bottom.png) repeat-y 100% 100%;'><p style='margin:0; font-size:1px; line-height:1px;'>&nbsp;</p></td>
					                                            <td width='4' height='4' style='background:url(http://demo.artlance.ru/email/shadow-right-bottom.png) repeat-y 0 100%;'><p style='margin:0; font-size:1px; line-height:1px;'>&nbsp;</p></td>
					                                        </tr>
					                                 
					                                        <tr>
					                                            <td width='4' height='4' style='background:url(http://demo.artlance.ru/email/shadow-bottom-corner-left.png) no-repeat 100% 0;'><p style='margin:0; font-size:1px; line-height:1px;'>&nbsp;</p></td>
					                                            <td width='4' height='4' style='background:url(http://demo.artlance.ru/email/shadow-bottom-left.png) no-repeat 100% 0;'><p style='margin:0; font-size:1px; line-height:1px;'>&nbsp;</p></td>
					                                            <td height='4' style='background:url(http://demo.artlance.ru/email/shadow-bottom-center.png) repeat-x 0 0;'><p style='margin:0; font-size:1px; line-height:1px;'>&nbsp;</p></td>
					                                            <td width='4' height='4' style='background:url(http://demo.artlance.ru/email/shadow-bottom-right.png) no-repeat 0 0;'><p style='margin:0; font-size:1px; line-height:1px;'>&nbsp;</p></td>
					                                            <td width='4' height='4' style='background:url(http://demo.artlance.ru/email/shadow-bottom-corner-right.png) no-repeat 0 0;'><p style='margin:0; font-size:1px; line-height:1px;'>&nbsp;</p></td>
					                                        </tr>
					                                    </tbody>
					                                </table>
					                                <!-- end wrapper-->
					                                <p style='margin:0; padding:34px 0 0; text-align:center; font-size:11px; line-height:13px; color:#333333;'>
					                                    Don‘t want to recieve further emails? You can unsibscribe <a href='http://pixelbuddha.net/' style='color:#333333; text-decoration:underline;'>here</a>
					                                </p>
					                            </td>
					                        </tr>
					                    </tbody>
					                </table>
					                <!-- end main block -->
					            </td>
					        </tr>
					    </tbody>
					</table>
					</body>
					</html>";
		}
		// ----------
	}
	
	private function inicializa_templates(){
		// template 0
		$this->arr_templates[0] = array();
		$this->arr_templates[0]['BG_COLOR'] = "#F0F2EA";
		$this->arr_templates[0]['LOGO_TOPO'] = "<a href='{{LINK}}' style='display:block; width:407px; height:100px; margin:0 auto 30px;'>
                                    				<img src='{{SRC}}' width='407' height='100' alt='{{ALT}}' style='display:block; border:0; margin:0;'>
                                		  		</a>";
		$this->arr_templates[0]['FRASE_TOPO'] = "";
		$this->arr_templates[0]['HEADER_IMG'] = "<img src='{{SRC}}' width='600' height='400' alt='{{ALT}}' style='display:block; border:0; margin:0 0 44px; background:#eeeeee;'>";
		$this->arr_templates[0]['TITULO'] = "";
		$this->arr_templates[0]['CORPO_2_COL'] = "<table cellpadding='0' cellspacing='0' border='0' width='100%'>
                                                    <tbody>
                                                        <tr valign='top'>
                                                            <td width='30'><p style='margin:0; font-size:1px; line-height:1px;'>&nbsp;</p></td>
                                                            <td>
                                                                <a style='display:block; margin:0 0 14px;' href='{{LINK_E}}'><img src='{{SRC_E}}' width='255' height='150' alt='{{ALT_E}}' style='display:block; margin:0; border:0; background:#eeeeee;'></a>
                                                                <p style='font-size:14px; line-height:22px; font-weight:bold; color:#333333; margin:0 0 5px;'><a href='{{LINK_E}}' style='color:#6c7e44; text-decoration:none;'>{{TITLE_E}}</a></p>
                                                                <p style='margin:0 0 35px; font-size:12px; line-height:18px; color:#333333;'>{{TXT_E}}</p>
                                                            </td>
                                                            <td width='30'><p style='margin:0; font-size:1px; line-height:1px;'>&nbsp;</p></td>
                                                            <td>
                                                                <a style='display:block; margin:0 0 14px;' href='{{LINK_E}}'><img src='{{SRC_D}}' width='255' height='150' alt='{{ALT_D}}' style='display:block; margin:0; border:0; background:#eeeeee;'></a>
                                                                <p style='font-size:14px; line-height:22px; font-weight:bold; color:#333333; margin:0 0 5px;'><a href='{{LINK_D}}' style='color:#6c7e44; text-decoration:none;'>{{TITLE_D}}</a></p>
                                                                <p style='margin:0 0 35px; font-size:12px; line-height:18px; color:#333333;'>{{TXT_D}}</p>
                                                            </td>
                                                            <td width='30'><p style='margin:0; font-size:1px; line-height:1px;'>&nbsp;</p></td>
                                                        </tr>
                                                    </tbody>
                                                  </table>";
		$this->arr_templates[0]['CORPO_1_COL'] = "<table cellpadding='0' cellspacing='0' border='0' width='100%'>
                                                    <tbody>
                                                        <tr valign='top'>
                                                            <td width='30'><p style='margin:0; font-size:1px; line-height:1px;'>&nbsp;</p></td>
                                                            <td colspan='3'>
                                                                <a style='display:block; margin:0 0 14px;' href='{{LINK}}'><img src='{{SRC}}' width='540' height='220' alt='{{ALT}}' style='display:block; margin:0; border:0; background:#eeeeee;'></a>
                                                                <p style='font-size:14px; line-height:22px; font-weight:bold; color:#333333; margin:0 0 5px;'><a href='{{LINK}}' style='color:#6c7e44; text-decoration:none;'>{{TITLE}}</a></p>
                                                                <p style='margin:0 0 35px; font-size:12px; line-height:18px; color:#333333;'>{{TXT}}</p>
                                                            </td>
                                                            <td width='30'><p style='margin:0; font-size:1px; line-height:1px;'>&nbsp;</p></td>
                                                        </tr>
                                                    </tbody>
                                                </table>";
		// ----------
	}

}

?>