<?php


interface ConnectionInterface {
	
	/**
	 * M�todo utilizado para executar selects no banco.
	 *
	 * @param
	 *
	 * @return Array com os valores da select executada.
	 */
	public function select();
	
	/**
	 * M�todo utilizado para executar inserts no banco.
	 *
	 * @param Nome do campo referente ao id da tabela onde ser� feita a inser��o.
	 *
	 * @return Id's dos registros inseridos.
	 */
	public function insert($campo);
	
	/**
	 * M�todo utilizado para executar updates no banco.
	 *
	 * @param
	 *
	 * @return N�mero de linhas afetadas.
	 */
	public function update();
	
	/**
	 * M�todo utilizado para executar deletes no banco.
	 *
	 * @param
	 *
	 * @return N�mero de linhas afetadas.
	 */
	public function delete();	
	
	/**
	 * M�todo utilizado para atribuir a query sql a ser executada.
	 *
	 * @param Query a ser executada.
	 *
	 * @return
	 */
	public function setSQL($sql);
	
	/**
	 * M�todo utilizado para obter a query a ser executada.
	 *
	 * @param
	 *
	 * @return Query a ser executada.
	 */
	public function getSQL();
	
	/**
	 * M�todo utilizado executar comandos sql.
	 *
	 * @param
	 *
	 * @return Retorna um objeto derivado da classe ADORecordSet. Se ocorrer erro, retorna FALSE.
	 */
	public function executeSQL();
}