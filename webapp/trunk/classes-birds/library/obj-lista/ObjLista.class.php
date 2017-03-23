<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

// se vir alguma requisicao de AJAX
if (isset($_REQUEST ['tipo']) && ($_REQUEST ['tipo'] == 'OrdenarLista' || $_REQUEST ['tipo'] == 'FiltrarLista')) {
	function strFiltrosToArray($filtro){		
		$arrFiltro = array ();
		
		$arrLinhas = explode('(@@)', $filtro);
		foreach ( $arrLinhas as $k => $v ) {
			$arrCampo = explode('(@)', $v);
		
			if ($arrCampo [0] != '' && $arrCampo [1] != '' && $arrCampo [2] != '') {
				array_push($arrFiltro, array ('slc_campos_filtro_tb' => utf8_decode($arrCampo [0]),'slc_tipo_filtro_tb' => utf8_decode($arrCampo [1]),'txt_campos_filtro_tb' => utf8_decode($arrCampo [2]) ));
			}
		}
		
		return $arrFiltro;
	}
	
	require_once $_SERVER['BIRDS_HOME'] . 'classes-general/EncryptString.class.php';
	$crypt = new objEncrypt();
	
	$sql = utf8_decode($crypt->decrypt($_REQUEST ['sql']));
	$vars = $crypt->decrypt($_REQUEST ['vars']);
	$campo_ordenacao = utf8_decode($_REQUEST ['campo_ord']);
	
	$arrVars = explode("[#]", $vars);
	$aVars = array ();
	foreach ( $arrVars as $k => $v ) {
		$arrTemp = explode("=", $v);
		$aVars [$arrTemp [0]] = $arrTemp [1];
	}
	
	require_once $_SERVER['BIRDS_HOME'] . 'classes-birds/library/obj-lista/ObjLista.class.php';
	$lista = new ObjLista();
	$lista->setSql($sql);
	$lista->setControllerNome($_REQUEST ['controller_nome']);
	
	// header size
	if ($aVars ['header_sizes'] != '') {
		$aVars ['header_sizes'] = explode('-@-', $aVars ['header_sizes']);
	}
	else {
		$aVars ['header_sizes'] = array ();
	}
	$lista->setHeaderSize($aVars ['header_sizes']);
	// -----------
	
	// header align
	if ($aVars ['header_align'] != '') {
		$aVars ['header_align'] = explode('-@-', $aVars ['header_align']);
	}
	else {
		$aVars ['header_align'] = array ();
	}
	$lista->setHeaderAlign($aVars ['header_align']);
	// ------------
	
	// filters
	if ($aVars ['arrayFilters'] != '') {
		$aVars ['arrayFilters'] = explode('-@-', $aVars ['arrayFilters']);
	}
	else {
		$aVars ['arrayFilters'] = array ();
	}
	$lista->setFilterFields($aVars ['arrayFilters']);
	// -------
	
	// btn detalhar, alterar, excluir
	$lista->showBtnDetalhar($aVars ['show_btn_detalhar'], $aVars ['action_btn_detalhar']);
	$lista->showBtnAlterar($aVars ['show_btn_alterar'], $aVars ['action_btn_alterar']);
	$lista->showBtnDeletar($aVars ['show_btn_excluir'], $aVars ['action_btn_excluir']);
	// ------------------------------
	
	$lista->setListaTitulo($aVars ['tb_title']);
	$aVars ['tipo_ordenacao'] = $_REQUEST ['tipo_ordenacao'];
	$aVars ['pagina_atual'] = $_REQUEST ['pagina_atual'];
	// PK tabela
	$lista->setPrimaryKeyTabela($aVars ['pk_tabela']);
	// ---------
	
	// FILTRO
	if($_REQUEST ['tipo'] == 'FiltrarLista'){
		$filtro = (isset($_REQUEST ['filtro']) ) ? $_REQUEST ['filtro']: '';
		$arrFiltro = strFiltrosToArray($filtro);
		
		$aVars['filtros'] = $arrFiltro;
	}
	else{
		$aVars['filtros'] = strFiltrosToArray($aVars['filtros']);
	}
	// ------
	
	echo $lista->getHtmlListaSql($aVars ['tem_ordenacao'], $campo_ordenacao, $aVars ['tipo_ordenacao'], $aVars ['detalhar_html'], $aVars ['alterar_html'], $aVars ['deletar_html'], $aVars ['tem_paginacao'], $aVars ['pagina_atual'], $aVars['filtros']);
	return;
}
else if (isset($_REQUEST ['tipo']) && $_REQUEST ['tipo'] == 'GeraStrComboTpFiltro') {
	
	require_once $_SERVER['BIRDS_HOME'] . 'classes-birds/library/obj-lista/ObjLista.class.php';
	require_once $_SERVER['BIRDS_HOME'] . 'classes-birds/library/html-inputs/HtmlInputs.class.php';
	
	$id_name_campo = 'txt_campos_filtro_tb';
	
	$tipo_campo = $_REQUEST ['tipo_campo'];
	list ( $arrValue, $arrText ) = ObjLista::getArraysSelectOpcFiltro($tipo_campo);
	
	$form = new HtmlInputs();
	echo $form->createSelect('slc_tipo_filtro_tb', 'slc_tipo_filtro_tb', $arrValue, $arrText) . '##@@##';
	
	switch ($tipo_campo) {
		case 'C' : // C: Character fields that should be shown in a <input type="text"> tag.
		case 'X' : // X: Clob (character large objects), or large text fields that should be shown in a <textarea>
			echo $form->getInputText($id_name_campo, $id_name_campo, '', false, false);
			break;
		
		case 'I' : // I: Integer field.
		case 'R' : // R: Counter or Autoincrement field. Must be numeric.
			echo $form->createOnlyNumbersInput($id_name_campo, $id_name_campo, '');
			break;
		
		case 'N' : // N: Numeric field. Includes decimal, numeric, floating point, and real.
			echo $form->createMoneyInput($id_name_campo, $id_name_campo, '', '', '.', '');
			break;
		
		case 'D' : // D: Date field
		case 'T' : // T: Timestamp field
			echo $form->createDateInput($id_name_campo, $id_name_campo, '');
			break;
		
		case 'L' : // L: Logical field (boolean or bit-field)
			echo $form->createSelect($id_name_campo, $id_name_campo, array (0,1 
			), array ('NãO','SIM' 
			));
			break;
		
		default :
			echo $form->createInputText($id_name_campo, $id_name_campo, '', false, false);
			break;
	}
	
	return;
}
?>



<?php
require_once $_SERVER['BIRDS_HOME'] . 'classes-birds/library/connection/Connection.class.php';
require_once $_SERVER['BIRDS_HOME'] . 'classes-general/EncryptString.class.php';
require_once $_SERVER['BIRDS_HOME'] . 'classes-birds/library/html-inputs/HtmlInputs.class.php';

class ObjLista{

	private $arrayItemLista = array ();

	private $arrayItemHeader = array ();

	private $arrayHeaderSize = array ();

	private $arrayHeaderAlign = array ();

	private $arrayColumnTypes = array ();

	private $arrayFilters = array ();

	private $qtdeColunas = 0;

	private $conn;

	private $form;

	private $sql;

	private $campos;

	private $limit = 20;

	private $offset = 0;

	private $tb_title = 'Lista';
	
	private $controllerNome = "";
	
	private $btn_detalhar = Array ('status' => false, 'action' => 'visualizarTela');
	
	private $btn_alterar = Array ('status' => false, 'action' => 'editarTela');
	
	private $btn_excluir = Array ('status' => false, 'action' => 'remover');
	
	private $pk_tabela = '';

	const TABLE_BUTTON_TYPE_SUCCESS = 'btn-success';

	const TABLE_BUTTON_TYPE_INFO = 'btn-info';

	const TABLE_BUTTON_TYPE_WARNING = 'btn-warning';

	const TABLE_BUTTON_TYPE_PRIMARY = 'btn-primary';

	const TABLE_BUTTON_TYPE_DANGER = 'btn-danger';

	const TABLE_BUTTON_SIZE_SMALL = 'btn-xs';

	const TABLE_BUTTON_SIZE_NORMAL = '';

	const TABLE_ALIGN_LEFT = 'left';

	const TABLE_ALIGN_CENTER = 'center';

	const TABLE_ALIGN_RIGHT = 'right';

	const TABLE_ALIGN_INVISIBLE = 'invisible';

	const QTDE_LINHA_POR_TAB = 20;

	const QTDE_BTN_PAGINACAO = 2; // a partir da pagina atual, 2 botoes pra esq e 2 pra dir
	
	/**
	 * para instanciar, precisa de uma conexão vãlida
	 */
	function __construct(Connection $conn = null){

		if (is_null($conn)) {
			require_once $_SERVER['BIRDS_HOME'] . 'classes-birds/library/connection/Connection.class.php';
			$this->conn = new Connection();
		}
		else {
			$this->conn = $conn;
		}
		
		require_once $_SERVER['BIRDS_HOME'] . 'classes-birds/library/html-inputs/HtmlInputs.class.php';
		$this->form = new HtmlInputs();
	
	}

	/**
	 * define a sql da tabela.
	 */
	public function setSql($sql){

		$this->sql = $this->acertaSql($sql);
		$this->campos = $this->extraiCampos($this->sql);
	
	}

	/**
	 * define o tamanho de cada uma das colunas
	 * ex: array_size('40px', '60%', '');
	 */
	public function setHeaderSize($arraySizes){

		// $this->arrayHeaderSize = $arraySizes;
		$this->arrayHeaderSize = array(); //por enquanto desativei pq tava zuando o posicionamento das colunas
	
	}

	/**
	 * alinha cada uma das colunas.
	 * use as contantes TABLE_ALIGN
	 * ex: array(ObjLista::TABLE_ALIGN_LEFT, ObjLista::TABLE_ALIGN_LEFT, ObjLista::TABLE_ALIGN_CENTER)
	 */
	public function setHeaderAlign($arrayAlign){

		$this->arrayHeaderAlign = $arrayAlign;
	
	}

	/**
	 * adiciona o titulo da lista (pode ser vazio)
	 */
	public function setListaTitulo($titulo){

		$this->tb_title = $titulo;
	
	}

	private function acertaSql($sql){

		return $sql;
		
		// $idxLimit = strrpos($sql, 'ORDER BY');
		// if($idxLimit === false){ return $sql; }
		// else{ return substr($sql, 0, $idxLimit - 1); }
	}

	private function extraiCampos($sql){

		$idxInicio = strpos($sql, 'SELECT') + 6;
		$idxFim = strpos($sql, 'FROM') - 1;
		$campos = substr($sql, $idxInicio, ($idxFim - $idxInicio) + 1);
		return $campos;
	
	}

	private function getTotalLines($sql, $filtros){
		
		$sql = 'SELECT COUNT(*) as total FROM ('.$sql.') t ';
		
		// se tem filtro, adiciona
		if( count($filtros) > 0 ){
			$sql .= ' WHERE ' . $this->addFiltrosWhere($filtros);
		}
		// -----------------------
		
		$this->conn->setSQL($sql);
		$arrResp = $this->conn->select();
		
		if ($this->conn->getError()) {
			die($this->conn->getErros());
		}
		else {
			foreach ( $arrResp as $linha ) {
				return $linha ['total'];
			}
		}
	
	}

	/**
	 * indica quais campos vão ter filtro.
	 * os nomes tem que ser igual ao nome do campo OU alias
	 * se passar um array vazio indica-se que não vai ter filtro
	 *
	 * ex: array('id', 'Label')
	 */
	public function setFilterFields($arrFields){

		$this->arrayFilters = $arrFields;
	
	}
	
	/**
	 * define o campo PK pra passar nos botoes padrao
	 */
	public function setPrimaryKeyTabela($field_pk){
		$this->pk_tabela = $field_pk;
	}
	
	/**
	 * nome do controller para vincular com os botães
	 * percisa ser setado antes de chamar os botães
	 */
	public function setControllerNome($controllerNome){
		$this->controllerNome = $controllerNome;
	}
	
	/**
	 * mostra ou não btn padrão de detalhar. precisa setar a funãão setPrimaryKeyTabela.
	 * 
	 * $true_false = TRUE or FALSE
	 */
	public function showBtnDetalhar($true_false, $action = false){
		$this->btn_detalhar['status'] = $true_false;
		$this->btn_detalhar['action'] = ($action) ? ($action) : $this->btn_detalhar['action'];
	}
	
	/**
	 * mostra ou não btn padrão de alterar. precisa setar a funãão setPrimaryKeyTabela.
	 *
	 * $true_false = TRUE or FALSE
	 */
	public function showBtnAlterar($true_false, $action = false){
		$this->btn_alterar['status'] = $true_false;
		$this->btn_alterar['action'] = ($action) ? ($action) : $this->btn_alterar['action'];
	}
	
	/**
	 * mostra ou não btn padrão de excluir. precisa setar a funãão setPrimaryKeyTabela.
	 *
	 * $true_false = TRUE or FALSE
	 */
	public function showBtnDeletar($true_false, $action = false){
		$this->btn_excluir['status'] = $true_false;
		$this->btn_excluir['action'] = ($action) ? ($action) : $this->btn_excluir['action'];
	}

	public function setLimit($limit){

		$this->limit = $limit;
	
	}

	public function setOffset($offset){

		$this->offset = $offset;
	
	}

	public static function getArraysSelectOpcFiltro($tipoCampo = ""){
		// C: Character fields that should be shown in a <input type="text"> tag.
		// X: Clob (character large objects), or large text fields that should be shown in a <textarea>
		// D: Date field
		// T: Timestamp field
		// L: Logical field (boolean or bit-field)
		// N: Numeric field. Includes decimal, numeric, floating point, and real.
		// I: Integer field.
		// R: Counter or Autoincrement field. Must be numeric.
		// B: Blob, or binary large objects.
		$arrValue = array ();
		$arrText = array ();
		
		switch ($tipoCampo) {
			case 'C' :
			case 'X' :
				array_push($arrValue, 'Igual');
				array_push($arrText, 'Igual');
				
				array_push($arrValue, 'Inicia Com');
				array_push($arrText, 'Inicia Com');
				
				array_push($arrValue, 'Contem');
				array_push($arrText, 'Contem');
				break;
			
			case 'N' :
			case 'I' :
			case 'R' :
				array_push($arrValue, 'Igual');
				array_push($arrText, 'Igual');
				
				array_push($arrValue, 'Maior Que');
				array_push($arrText, 'Maior Que');
				
				array_push($arrValue, 'Maior Ou Igual');
				array_push($arrText, 'Maior Ou Igual');
				
				array_push($arrValue, 'Menor Que');
				array_push($arrText, 'Menor Que');
				
				array_push($arrValue, 'Menor Ou Igual');
				array_push($arrText, 'Menor Ou Igual');
				
				array_push($arrValue, 'Diferente');
				array_push($arrText, 'Diferente');
				break;
			
			case 'D' :
			case 'T' :
				array_push($arrValue, 'Igual');
				array_push($arrText, 'Igual');
				
				array_push($arrValue, 'Maior Que');
				array_push($arrText, 'Maior Que');
				
				array_push($arrValue, 'Maior Ou Igual');
				array_push($arrText, 'Maior Ou Igual');
				
				array_push($arrValue, 'Menor Que');
				array_push($arrText, 'Menor Que');
				
				array_push($arrValue, 'Menor Ou Igual');
				array_push($arrText, 'Menor Ou Igual');
				break;
			
			case 'L' :
				array_push($arrValue, 'Igual');
				array_push($arrText, 'Igual');
				break;
			
			default :
				array_push($arrValue, 'Igual');
				array_push($arrText, 'Igual');
				
				array_push($arrValue, 'Inicia Com');
				array_push($arrText, 'Inicia Com');
				
				array_push($arrValue, 'Contem');
				array_push($arrText, 'Contem');
				break;
		}
		
		return array ($arrValue,$arrText);
	
	}

	private function getSelectOpcFiltro($tipoCampo = ""){

		list ( $arrValue, $arrText ) = $this::getArraysSelectOpcFiltro($tipoCampo);
		
		return $this->form->createSelect('slc_tipo_filtro_tb', 'slc_tipo_filtro_tb', $arrValue, $arrText);
	
	}
	
	private function temBtnLista(){
		return $this->btn_detalhar['status'] || $this->btn_alterar['status'] || $this->btn_excluir['status'];
	}
	
	private function pegaWidthTrBtn(){
		$count = 0;
		
		if($this->btn_detalhar['status']){
			$count++;
		}
		
		if($this->btn_alterar['status']){
			$count++;
		}
		
		if($this->btn_excluir['status']){
			$count++;
		}
		
		return $count * 45;
	}
	
	private function createBtnLista($vlr_pk_tabela){
		$html = '';
		
		if($this->btn_detalhar['status']){
			$html .= '<button style="margin-right:3px;" attr-lista-td-id="'.$vlr_pk_tabela.'" controller="'.$this->controllerNome.'" action="'.$this->btn_detalhar['action'].'" class="btn btn-default btn-visualizar btn-sm" type="button">
	 				    <i class="fa fa-eye"></i>
	 				  </button>';
		}
		
		if($this->btn_alterar['status']){
			$html .= '<button style="margin-right:3px;" attr-lista-td-id="'.$vlr_pk_tabela.'" controller="'.$this->controllerNome.'" action="'.$this->btn_alterar['action'].'" class="btn btn-info btn-editar btn-sm" type="button">
	 				    <i class="fa fa-edit"></i>
	 				  </button>';
		}
		
		if($this->btn_excluir['status']){
			$html .= '<button style="margin-right:3px;" attr-lista-td-id="'.$vlr_pk_tabela.'" controller="'.$this->controllerNome.'" action="'.$this->btn_excluir['action'].'" class="btn btn-danger btn-deletar btn-sm" type="button">
	 				    <i class="fa fa-trash-o"></i>
	 				  </button>';
		}
		
		return $html;
	}
	
	private function addFiltrosWhere($filtros){
		$where = '';
		foreach ($filtros as $itemFiltro){
			$slc_campos_filtro_tb = $itemFiltro['slc_campos_filtro_tb'];
			$slc_tipo_filtro_tb = $itemFiltro['slc_tipo_filtro_tb'];
			$txt_campos_filtro_tb = $itemFiltro['txt_campos_filtro_tb'];
			
			$where .= ' (';
			$where .= 't."'.$slc_campos_filtro_tb.'"';
			
			switch($slc_tipo_filtro_tb){
				case 'Igual':
					$where .= " = '".$txt_campos_filtro_tb."' ";
					break;
					
				case 'Inicia Com':
					$where .= " ILIKE '".$txt_campos_filtro_tb."%' ";
					break;
					
				case 'Contem':
					$where .= " ILIKE '%".$txt_campos_filtro_tb."%' ";
					break;
					
				case 'Maior Que':
					$where .= " > '".$txt_campos_filtro_tb."' ";
					break;
					
				case 'Maior Ou Igual':
					$where .= " >= '".$txt_campos_filtro_tb."' ";
					break;
				
				case 'Menor Que':
					$where .= " < '".$txt_campos_filtro_tb."' ";
					break;
					
				case 'Menor Ou Igual':
					$where .= " <= '".$txt_campos_filtro_tb."' ";
					break;
				
				case 'Diferente':
					$where .= " <> '".$txt_campos_filtro_tb."' ";
					break;
			}
			
			$where .= ') ';
			$where .= ' AND ';
			
		}
		
		if($where <> ''){
			$where = substr($where, 0, strlen($where) - 5);
		}
		
		return $where;
	}
	
	private function isColunaVisivel($numCol){
		return ($numCol <= 1) ? '': ' class="visible-lg" ';
	}

	/*
	 * filtros (array) = Array ( [0] => Array ( [slc_campos_filtro_tb] => id [slc_tipo_filtro_tb] => maior_i [txt_campos_filtro_tb] => 1 ) 
	 *                           [1] => Array ( [slc_campos_filtro_tb] => id [slc_tipo_filtro_tb] => menor_i [txt_campos_filtro_tb] => 10 ) ) 
	 */
	public function getHtmlListaSql($tem_ordenacao = true, $campo_ordenacao = '', $tipo_ordenacao = 'ASC', $detalhar_html = '', $alterar_html = '', $deletar_html = '', $tem_paginacao = true, $pagina_atual = 1, $filtros = array()){
		// set default timezone
		date_default_timezone_set('America/Sao_Paulo');
		
		$totalLinhas = $this->getTotalLines($this->sql, $filtros);
		$sql = 'SELECT * FROM ('.$this->sql.') t ';
		
		// se tem filtro, adiciona
		if( count($filtros) > 0 ){
			$sql .= ' WHERE ' . $this->addFiltrosWhere($filtros);
		}
		// -----------------------
		
		if ($tem_ordenacao && $campo_ordenacao != '') {
			$sql .= ' ORDER BY t."' . $campo_ordenacao . '" ' . $tipo_ordenacao . ' ';
		}
		$sql_export_xls = $sql;
		
		// calcula o LIMIT e OFFSET
		$this->limit = $this::QTDE_LINHA_POR_TAB;
		$this->offset = ($pagina_atual - 1) * $this::QTDE_LINHA_POR_TAB;
		// ------------------------
		
		$sql .= ' LIMIT ' . $this->limit . ' OFFSET ' . $this->offset;
		
		$this->conn->setSQL($sql);
		$arrResp = $this->conn->select();
		$arrHeaders = $this->conn->getColumnNameArray();
		$this->setColumnHeader($arrHeaders);
		$this->arrayColumnTypes = $this->conn->getColumnTypeArray();
		
		$id = 'tbWis_' . md5($this->tb_title);
		
		// verifica se mostra ou nao o filtro
		$vIconeFiltro = '';
		$vHtmlFiltro = '';
		$vTrFiltroPadrao = '';
		
		if (count($this->arrayFilters) > 0) {
			// faz uma select hidden com os tipos dos campos do filtro
			$selectHiddenTpCampo = '';
			if (count($this->arrayColumnTypes) > 0) {
				$selectHiddenTpCampo = '<select id="slct_hidden_' . $id . '" style="display:none; width: 0px; height:0px;">';
				foreach ( $this->arrayColumnTypes as $colunaTipo ) {
					$selectHiddenTpCampo .= '<option value="' . $colunaTipo ['tipo'] . '">' . $colunaTipo ['campo'] . '</option>';
				}
				$selectHiddenTpCampo .= '</select>';
			}
			
			$vIconeFiltro = '<a data-trigger="hover" data-placement="top" data-content="Aplicar Filtro" attr-tb-id="' . $id . '" class="fa fa-search btn-slide-filter popovers" href="javascript:void(0);"></a>';
			
			// MONTA A TR PADRAO QDO ADD
			$vTrFiltroPadrao = '<tr class="linha-filtro-lista">
								 <td width="32">
								   <a style="position: relative; top: 1px;" class="btn btn-danger btn-sm btn-animate-demo btn-delete-linha-filtro" href="javascript:void(0);"><i class="fa fa-trash-o"></i></a>
				                 </td>
				                 <td style="padding-right:7px;">
								   ' . $this->form->createSelect('slc_campos_filtro_tb', 'slc_campos_filtro_tb', $this->arrayFilters, $this->arrayFilters, '', true, false, array ('cl_slc_campos_filtro_tb')) . '
				                 </td>
				                 <td style="padding-right:7px;">
								   ' . $this->form->createSelect('slc_tipo_filtro_tb', 'slc_tipo_filtro_tb', array (), array (), '', true) . '
							     </td>
				                 <td style="padding-right:7px;">
								   <input disabled="disabled" id="txt_campos_filtro_tb" class="abc form-control" type="text" style="max-width:300px;" value="" name="txt_campos_filtro_tb">
								 </td>
				               </tr>';
				
				
			$vTrFiltro = '';
			if( count($filtros) > 0 ){
				foreach ($filtros as $itemFiltro){
					$vTrFiltro .= '<tr class="linha-filtro-lista">
									 <td width="32">
									   <a style="position: relative; top: 1px;" class="btn btn-danger btn-sm btn-animate-demo btn-delete-linha-filtro" href="javascript:void(0);"><i class="fa fa-trash-o"></i></a>
					                 </td>
					                 <td style="padding-right:7px;">
									   '.$this->form->getInputText('slc_campos_filtro_tb', 'slc_campos_filtro_tb', $itemFiltro['slc_campos_filtro_tb'], "", "", "0", false, true, true).'
					                 </td>
					                 <td style="padding-right:7px;">
									   '.$this->form->getInputText('slc_tipo_filtro_tb', 'slc_tipo_filtro_tb', $itemFiltro['slc_tipo_filtro_tb'], "", "", "0", false, true, true).'
								     </td>
					                 <td style="padding-right:7px;">
									   '.$this->form->getInputText('txt_campos_filtro_tb', 'txt_campos_filtro_tb', utf8_encode($itemFiltro['txt_campos_filtro_tb']), "", "", "0", false, true, true).'
									 </td>
					               </tr>';
				}
			}
			// -------------------------
			
			$vHtmlFiltro = '<div id="filter_' . $id . '" class="panel-heading" style="background-color:#e6edf8; display:none;">
							 <input type="hidden" id="hidden_aux_tp_filtro_id" value="' . $id . '" />
				             ' . $selectHiddenTpCampo . '
				             <table class="table tb_filtro_lista">
					 		   <tr>
								 <td colspan="4">
				                   <a attr-tb-id="' . $id . '" class="btn btn-success btn-add-linha-filtro-lista" href="javascript:void(0);">
									 <i class="fa fa-plus"></i>
									 Adicionar Filtro
				                   </a>
						           &nbsp;
						           <a style="margin-left:0 !important;" attr-tb-id="' . $id . '" class="btn btn-primary btn-exec-filtro-lista" href="javascript:void(0);">
									 <i class="fa fa-filter"></i>
									 Filtrar
				                   </a>
								 </td>
				               </tr>
			                   ' . $vTrFiltro . '
							 </table>
				           </div>';
			
			$auxSlctOpcFil = (is_array($this->arrayColumnTypes) && count($this->arrayColumnTypes) > 0) ? $this->arrayColumnTypes [0] ['tipo'] : "";
		}
		// ----------------------------------
		
		$html = '<div id="div_' . $id . '">';
		$html .= ' <section class="panel">';
		$html .= '   <header class="panel-heading">
                		<a attr-id="tb_filtro_'.$id.'" href="javascript:void(0);" class="lnk-title-obj-panel">' . $this->tb_title . '</a>
                        <span class="tools pull-right">
						  <a data-trigger="hover" data-placement="top" data-content="Exportar Excel" attr-tb-id="' . $id . '" class="panel-export-xls fa fa-file-excel-o popovers" href="javascript:void(0);"></a>
	 					  ' . $vIconeFiltro . '
						  <!--<a href="javascript:;" class="fa fa-chevron-down minimize-table"></a>-->
                          <!--<a href="javascript:;" class="fa fa-times"></a>-->
                        </span>
            		 </header>';
		$html .= '   <div id="pb_tb_filtro_'.$id.'" class="panel-body" style="border-color:#dddddd !important;">';
		$html .= $vHtmlFiltro;
		$html .= '     <table id="' . $id . '" class="table table-bordered table-striped table-condensed cf">';
		$html .= '       <thead class="cf">';
		$html .= '         <tr>';
		
		$atualCampoOrder = '';
		$atualOrdenacao = 'ASC';
		
		$qtdeTrVisible = 0;
		for($i = 0; $i < $this->qtdeColunas; $i ++) {
			$size = '';
			if (count($this->arrayHeaderSize) > 0) {
				if ($this->arrayHeaderSize [$i] != '') {
					$size = ' width="' . $this->arrayHeaderSize [$i] . '" ';
				}
			}
			
			$align = '';
			if (count($this->arrayHeaderAlign) > 0) {
				if ($this->arrayHeaderAlign [$i] != '') {
					$align = ' style="text-align:' . $this->arrayHeaderAlign [$i] . ';" ';
				}
				else {
					$align = ' style="text-align:left;" ';
				}
			}
			
			$visible_lg = $this->isColunaVisivel($i);
			
			if($this->arrayHeaderAlign [$i] != $this::TABLE_ALIGN_INVISIBLE){
				$qtdeTrVisible++;
				
				$html .= '       <th '.$visible_lg.' ' . $size . ' ' . $align . '>';
				if ($tem_ordenacao) {
					$tp_ord = 'ASC';
					if ($tem_ordenacao) {
						if (utf8_decode($this->arrayItemHeader [$i]) == $campo_ordenacao) {
							$tp_ord = ($tipo_ordenacao == 'ASC') ? 'DESC' : 'ASC';
				
							$atualCampoOrder = $this->arrayItemHeader [$i];
							$atualOrdenacao = $tipo_ordenacao;
						}
					}
				
					$html .= '     <a href="javascript:void(0);" onclick="fncObjLista__order(\'' . $this->arrayItemHeader [$i] . '\', \'' . $id . '\', \'' . $tp_ord . '\', ' . $pagina_atual . ');">' . $this->arrayItemHeader [$i] . '</a>';
				}
				else {
					$html .= $this->arrayItemHeader [$i];
				}
					
				// seta da ordenaãão
				if ($tem_ordenacao) {
					if (utf8_decode($this->arrayItemHeader [$i]) == $campo_ordenacao) {
						if ($tipo_ordenacao == 'ASC') {
							$html .= '<img width="12" height="12" src="'.$_SERVER['BIRDS_HOME_URL'].'html/images_birds/lista-arrow-up.png" />';
						}
						else {
							$html .= '<img width="12" height="12" src="'.$_SERVER['BIRDS_HOME_URL'].'html/images_birds/lista-arrow-down.png" />';
						}
					}
				}
				// -----------------
					
				$html .= '       </th>';
			}
		}
		
		// se tiver botão
		if( $this->temBtnLista() ){
			$html .= '<th width="'.$this->pegaWidthTrBtn().'px" align="center">&nbsp;</th>';
		}
		// --------------
		
		$html .= '         </tr>';
		$html .= '       </thead>';
		
		if( $arrResp === null ) {
			$arrResp = Array();
		}
		
		foreach ( $arrResp as $linha ) {
			$array_linha = array ();
			for($i = 0; $i < $this->qtdeColunas; $i ++) {
				array_push($array_linha, $linha [$this->arrayItemHeader [$i]]);
			}
			$this->addLinha($array_linha);
		}
		
		$html .= '       <tbody>';
		$linha = 1;
		foreach ( $this->arrayItemLista as $itemLinha ) {
			$vlr_pk_tabela = '';
			
			if ($linha == 1) {
				$cor = '#fefefe';
				$linha = 2;
			}
			else {
				$cor = '#ffffff';
				$linha = 1;
			}
			
			$html .= '     <tr style="background-color: ' . $cor . ' !important;">';
			for($i = 0; $i < $this->qtdeColunas; $i ++) {
				$visible_lg = $this->isColunaVisivel($i);
				$vlr_pk_tabela = ( $vlr_pk_tabela == '' && $this->pk_tabela == $arrHeaders[$i] ) ? $itemLinha [$i]: $vlr_pk_tabela;
				
				$align = '';
				if (count($this->arrayHeaderAlign) > 0) {
					if ($this->arrayHeaderAlign [$i] != '') {
						$align = ' text-align:' . $this->arrayHeaderAlign [$i] . '; ';
					}
					else {
						$align = ' text-align:left; ';
					}
				}
				
				if( $this->arrayHeaderAlign [$i] != $this::TABLE_ALIGN_INVISIBLE ){
					// checa o tipo do campo para formatar
					$tipo_campo = $this->arrayColumnTypes[$i]["tipo"];
					switch($tipo_campo){
						case 'D':							
							$valor = ( $itemLinha [$i] != '' && is_numeric(substr($itemLinha[$i], 0, 4)) ) ? date('d/m/Y', strtotime($itemLinha [$i])): $itemLinha [$i];
							break;
						case 'T':
							$valor = ( $itemLinha [$i] != '' && is_numeric(substr($itemLinha[$i], 0, 4)) ) ? date('d/m/Y H:i:s', strtotime($itemLinha [$i])): $itemLinha [$i];
							break;
						case 'N':
							$valor = ( $itemLinha [$i] != '' ) ? number_format($itemLinha [$i], 2, ',', '.'): $itemLinha [$i];
							break;
						case 'L':
							$valor = '';
							
							if($itemLinha [$i] == 't'){
								$valor = '<i class="fa fa-check fa-15x"></i>';
							}
							else if($itemLinha [$i] == 'f'){
								$valor = '<i class="fa fa-times fa-15x"></i>';
							}
							break;
						default:
							$valor = $itemLinha [$i];
							break;
					}
					// ===================================
					
					$html .= '    <td '.$visible_lg.' style="' . $align . '">' . $valor . '</td>';
				}
			}
			
			// se tiver botão
			if( $this->temBtnLista() ){
				$html .= '<td width="'.$this->pegaWidthTrBtn().'px">';
				$html .= $this->createBtnLista($vlr_pk_tabela);
				$html .= '</td>';
			}
			// --------------
			
			$html .= '     </tr>';
		}
		$html .= '       </tbody>';
		
		if ($tem_paginacao) {
			$qtdePaginas = ceil(($totalLinhas / $this::QTDE_LINHA_POR_TAB));
			
			//$colspan = ( $this->temBtnLista() ) ? $this->qtdeColunas + 1: $this->qtdeColunas;
			$colspan = ( $this->temBtnLista() ) ? $qtdeTrVisible + 1: $qtdeTrVisible;
			
			$html .= '   <tfoot style="border-top:solid 1px #ddd;">';
			$html .= '     <tr>';
			$html .= '       <td colspan="'.$colspan.'">';
			$html .= '         <div style="float:left; display:inline-block;">';
			$html .= '          <a style="margin-left:4px;" onclick="fncObjLista__order(\'' . $atualCampoOrder . '\', \'' . $id . '\', \'' . $atualOrdenacao . '\', 1);" class="btn btn-default btn-sm btn-animate-demo" href="javascript:void(0);">Primeira</a>';
				
			$iInicio = (($pagina_atual - $this::QTDE_BTN_PAGINACAO) <= 0) ? 1 : ($pagina_atual - $this::QTDE_BTN_PAGINACAO);
			$iFinal = (($pagina_atual + $this::QTDE_BTN_PAGINACAO) > $qtdePaginas) ? $qtdePaginas : ($pagina_atual + $this::QTDE_BTN_PAGINACAO);
				
			for($i = $iInicio; $i <= $iFinal; $i ++) {
				$disabled = ($i == $pagina_atual) ? ' disabled="disabled" ' : '';
				$attr_current_btn = ($i == $pagina_atual) ? ' btn-lista-current="current" ' : '';
			
				$html .= '      <a style="margin-left:4px;" ' . $disabled . ' ' . $attr_current_btn . ' onclick="fncObjLista__order(\'' . $atualCampoOrder . '\', \'' . $id . '\', \'' . $atualOrdenacao . '\', ' . $i . ');" class="btn btn-default btn-sm btn-animate-demo" href="javascript:void(0);">' . $i . '</a>';
			}
				
			$html .= '          <a style="margin-left:4px;" onclick="fncObjLista__order(\'' . $atualCampoOrder . '\', \'' . $id . '\', \'' . $atualOrdenacao . '\', ' . $qtdePaginas . ');" class="btn btn-default btn-sm btn-animate-demo" href="javascript:void(0);">&Uacute;ltima</a>';
			$html .= '         </div>';
			$html .= '         <div style="float:right; display:inline-block;">';
			$html .= '           <small style="position:relative; top:10px; padding:2px;"><i>Total Registros: '.$totalLinhas.'</i></small>';
			$html .= '         </div>';
			$html .= '       </td>';
			$html .= '     </tr>';
			$html .= '   </tfoot>';
		}
		
		$html .= '     </table>
				     </div>
				   </section>';
		
		// inputs para fazer o ORDER / PAGINATION
		$crypt = new objEncrypt();
		$html .= '  <input type="hidden" name="' . $id . '__s" id="' . $id . '__s" value="' . $crypt->encrypt(utf8_encode($this->sql)) . '" />';
		$html .= '  <input type="hidden" name="' . $id . '__trp" id="' . $id . '__trp" value="' . htmlspecialchars($vTrFiltroPadrao) . '" />';
		$html .= '  <input type="hidden" name="' . $id . '__exp_xls" id="' . $id . '__exp_xls" value="' . $crypt->encrypt(utf8_encode($sql_export_xls)) . '" />';
		
		$vars = '';
		$vars .= 'header_sizes=' . implode('-@-', $this->arrayHeaderSize) . '[#]';
		$vars .= 'header_align=' . implode('-@-', $this->arrayHeaderAlign) . '[#]';
		$vars .= 'arrayFilters=' . implode('-@-', $this->arrayFilters) . '[#]';
		$vars .= 'tb_title=' . $this->tb_title . '[#]';
		$vars .= 'tem_ordenacao=' . $tem_ordenacao . '[#]';
		$vars .= 'campo_ordenacao=' . utf8_encode($campo_ordenacao) . '[#]';
		$vars .= 'detalhar_html=' . $detalhar_html . '[#]';
		$vars .= 'alterar_html=' . $alterar_html . '[#]';
		$vars .= 'tipo_ordenacao=' . $tipo_ordenacao . '[#]';
		$vars .= 'tem_paginacao=' . $tem_paginacao . '[#]';
		$vars .= 'pagina_atual=' . $pagina_atual . '[#]';
		$vars .= 'action_btn_detalhar=' . $this->btn_detalhar['action'] . '[#]';
		$vars .= 'show_btn_detalhar=' . $this->btn_detalhar['status'] . '[#]';
		$vars .= 'action_btn_alterar=' . $this->btn_alterar['action'] . '[#]';
		$vars .= 'show_btn_alterar=' . $this->btn_alterar['status'] . '[#]';
		$vars .= 'action_btn_excluir=' . $this->btn_excluir['action'] . '[#]';
		$vars .= 'show_btn_excluir=' . $this->btn_excluir['status'] . '[#]';
		$vars .= 'pk_tabela=' . $this->pk_tabela . '[#]';
		$vars .= 'filtros=' .utf8_encode( $this->filtroArrayToStr($filtros)) . '[#]';
		$vars .= 'deletar_html=' . $deletar_html;
		$html .= '<input type="hidden" name="' . $id . '__v" id="' . $id . '__v" value="' . $crypt->encrypt($vars) . '" />';
		
		$html .= '</div>';
		
		return $html;
	
	}
	
	private function filtroArrayToStr($filtro){
		$str = '';
		
		foreach($filtro as $itemFiltro){
          $str .= $itemFiltro['slc_campos_filtro_tb'] . '(@)' . $itemFiltro['slc_tipo_filtro_tb'] . '(@)' . $itemFiltro['txt_campos_filtro_tb'] . '(@@)';
		}
		
		if($str != ''){
			$str = substr($str, 0, strlen($str) - 4);
		}
		return $str;

	}

	
	// TENTATIVA DE FAZER TABELAS Sã POR MEIO DE UM ARRAY
	/**
	 * adiciona a linha header da tabela.
	 * cada coluna ã uma linha do array.
	 * ex: array('id', 'Nome', 'Email')
	 *
	 * ex: array_size('40px', '60%', '');
	 */
	public function setColumnHeader($arrayH){

		$this->arrayItemHeader = $arrayH;
		$this->qtdeColunas = count($this->arrayItemHeader);
	
	}

	/**
	 * adiciona uma linha na tabela.
	 * cada valor ã uma linha do array.
	 * ex: array('1', 'Joao', 'joao@gmail.com')
	 */
	public function addLinha($arrayL){

		array_push($this->arrayItemLista, $arrayL);
	
	}

	public function htmlTableButton($content, $type, $size, $onclick){

		return '<button class="btn ' . $type . ' ' . $size . '" onclick="' . $onclick . '" type="button">' . $content . '</button>';
	
	}

	/**
	 * retorna o HTML da tabela OU false se tiver
	 * algo errado.
	 */
	public function getHtmlTable(){

		if ($this->qtdeColunas <= 0) {
			return false;
		}
		
		$html = '<table class="table users-table table-condensed table-hover">';
		
		$html .= ' <thead>';
		$html .= '   <tr>';
		for($i = 0; $i < $this->qtdeColunas; $i ++) {
			$size = '';
			if (count($this->arrayHeaderSize) > 0) {
				if ($this->arrayHeaderSize [$i] != '') {
					$size = ' width="' . $this->arrayHeaderSize [$i] . '" ';
				}
			}
			
			$html .= '  <th ' . $size . '>' . $this->arrayItemHeader [$i] . '</th>';
		}
		$html .= '   </tr>';
		$html .= ' </thead>';
		
		$html .= ' <tbody>';
		$linha = 1;
		foreach ( $this->arrayItemLista as $itemLinha ) {
			if ($linha == 1) {
				$cor = '#fefefe';
				$linha = 2;
			}
			else {
				$cor = '#ffffff';
				$linha = 1;
			}
			
			$html .= '   <tr style="background-color: ' . $cor . ' !important;">';
			for($i = 0; $i < $this->qtdeColunas; $i ++) {
				$html .= '  <td>' . $itemLinha [$i] . '</td>';
			}
			$html .= '   </tr>';
		}
		$html .= ' </tbody>';
		
		$html .= '</table>';
		return $html;
	
	}

}
?>