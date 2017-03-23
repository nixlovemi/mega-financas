<?php
require_once $_SERVER['BIRDS_HOME'] . 'classes-birds/interfaces/ConnectionInterface.php';

class Connection implements ConnectionInterface{

	private $sql;

	private $erros;

	private $error;

	private $linha;

	private $rs; // recordset da select
	public $parameters;

	public $retornoSQL;

	function __construct(){

		$this->setError(false);
		$this->parameters = Array ();
		$this->setLinha(1);
	
	}
	
	// ******* GETTERS AND SETTERS
	public function setSQL($sql){

		$this->sql = utf8_encode($sql);
	
	}

	public function getSQL(){

		return $this->sql;
	
	}

	public function setLinha($linha){

		$this->linha = $linha;
	
	}

	public function getLinha(){

		return $this->linha;
	
	}

	public function setErros($erros){

		$this->erros = $erros;
	
	}

	public function getErros(){

		return $this->erros . "<br>SQL: " . $this->getSQL();
	
	}

	public function setError($erro){

		$this->erro = $erro;
	
	}

	public function getError(){

		return $this->erro;
	
	}
	
	// ******* GETTERS AND SETTERS
	public function setRetornoSQL($resultSet){

		$retorno = ($resultSet->RecordCount() > 0);
		$this->retornoSQL = $retorno;
	
	}

	public function addParameter($valor){

		if (is_bool($valor)) {
			$valor = ($valor) ? 'TRUE' : 'FALSE';
		}
		
		// $valorUpper = ($valor === NULL) ? ($valor) : (utf8_encode(mb_strtoupper(utf8_decode($valor))));
		$valorUpper = ($valor === NULL) ? ($valor) : (utf8_encode(utf8_decode($valor)));
		
		//$linha = $this->getLinha();
		//$this->parameters [$linha] [] = $valorUpper;
		$this->parameters [] = $valorUpper;
	
	}

	public function addLinha(){

		$this->setLinha($this->getLinha() + 1);
	
	}

	public function getConnection(){

		require_once $_SERVER['BIRDS_HOME'] . 'classes-birds/library/adodb/adodb.inc.php';
		
		$conn = ADONewConnection('postgres');
		$conn->Connect($_SERVER["BIRDS_DBSERVER"], $_SERVER['BIRDS_DBUSER'], $_SERVER['BIRDS_DBPASS'], $_SERVER['BIRDS_DBNAME']);
		return $conn;
	
	}

	public function executeSQL($retorno="RS"){

		$sql = $this->getSQL();
		
		$conn = $this->getConnection();
		$resultSet = $conn->Prepare($sql);
		
		if (count($this->parameters) > 0) {
			$resultSet = $conn->Execute($sql, $this->parameters);
		}
		else {
			$resultSet = $conn->Execute($sql);
		}
		
		// armazena antes de zerar
		$arrParametros = $this->parameters; 
		$this->parameters = Array ();
		
		if ($resultSet) {
			$this->setRetornoSQL($resultSet);
			
			if($retorno == "RS"){
				return $resultSet;
			}
			else if($retorno == "ARRAY"){
				return $resultSet->getArray();	
			}
		}
		else {
			// deu erro, grava no log
			// FAZER QDO TIVER LOGIN DE USUARIO
			
			$this->retornoSQL = false;
			$this->setError(true);
			$this->setErros($conn->ErrorMsg());
			return false;
		}
	
	}

	public function formatErrosHtml(){

		$html = "";
		$erro = $this->getErros();
		$html = "<div class='erroSQL'><p>{$erro}</p></div>";
		
		return $html;
	
	}

	public function select(){

		$resultSet = $this->executeSQL();
		
		// deu problema, aborta
		if ($resultSet == false) return;
		
		// guardo para eu pegar os nomes das colunas
		$this->rs = $resultSet;
		
		$dados_array = Array ();
		
		foreach ( $resultSet as $key => $linha ) {
			$dados = Array ();
			
			foreach ( $linha as $k => $valor ) {
				if (! is_numeric($k)) $dados [$k] = $valor;
			}
			array_push($dados_array, $dados);
		}
		
		return $dados_array;
	
	}

	public function selectRow(){

		$resultSet = $this->executeSQL();
		
		// deu problema, aborta
		if ($resultSet == false) return;
		
		$row = Array ();
		
		foreach ( $resultSet as $key => $linha ) {
			
			foreach ( $linha as $k => $valor ) {
				if (! is_numeric($k)) $row [$k] = $valor;
			}
		}
		
		return $row;
	
	}

	public function insert($campo){

		$sql = "WITH inseridos AS (";
		$sql .= $this->getSQL();
		$sql .= " RETURNING " . $campo . ") ";
		$sql .= "SELECT " . $campo . " FROM inseridos";
		
		$this->setSQL($sql);
		$retorno = $this->executeSQL();
		
		// deu problema, aborta
		if ($retorno == false) return;
		
		$qtd_linhas = $this->getLinha();
		$ids_array = Array ();
		for($i = 0; $i < $qtd_linhas; $i ++)
			array_push($ids_array, $retorno->fields [$campo] - $i);
		
		return $ids_array;
	
	}

	public function delete(){

		$sql = "WITH rows AS (";
		$sql .= $this->getSQL();
		$sql .= " RETURNING 1) ";
		$sql .= "SELECT COUNT(*) AS affected_rows FROM rows";
		
		$this->setSQL($sql);
		$retorno = $this->executeSQL();
		
		// deu problema, aborta
		if ($retorno == false) return;
		
		return $retorno->fields ['affected_rows'];
	
	}

	public function update(){

		$sql = "WITH rows AS (";
		$sql .= $this->getSQL();
		$sql .= "RETURNING 1)";
		$sql .= "SELECT COUNT(*) AS affected_rows FROM rows";
		
		$this->setSQL($sql);
		$retorno = $this->executeSQL();
		
		// deu problema, aborta
		if ($retorno == false) return;
		
		return $retorno->fields ['affected_rows'];
	
	}

	public function getColumnNameArray(){

		$array = array ();
		
		for($i = 0; $i < $this->rs->numCols(); $i ++) {
			array_push($array, $this->rs->fetchField($i)->name);
		}
		
		return $array;
	
	}

	public function getColumnTypeArray(){

		$array = array ();
		
		for($i = 0; $i < $this->rs->numCols(); $i ++) {
			array_push($array, array ('campo' => $this->rs->fetchField($i)->name,'tipo' => $this->getFieldType($this->rs->fetchField($i))));
		}
		
		return $array;
	
	}

	private function getFieldType($field){
		// a variavel $fields tem qur vir da funcao FetchField(0)
		// C: Character fields that should be shown in a <input type="text"> tag.
		// X: Clob (character large objects), or large text fields that should be shown in a <textarea>
		// D: Date field
		// T: Timestamp field
		// L: Logical field (boolean or bit-field)
		// N: Numeric field. Includes decimal, numeric, floating point, and real.
		// I: Integer field.
		// R: Counter or Autoincrement field. Must be numeric.
		// B: Blob, or binary large objects.
		$conn = $this->getConnection();
		return $conn->MetaType($field->type);
	
	}

}