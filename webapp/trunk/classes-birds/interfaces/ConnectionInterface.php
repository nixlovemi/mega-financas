<?php


interface ConnectionInterface {
	
	/**
	 * Mйtodo utilizado para executar selects no banco.
	 *
	 * @param
	 *
	 * @return Array com os valores da select executada.
	 */
	public function select();
	
	/**
	 * Mйtodo utilizado para executar inserts no banco.
	 *
	 * @param Nome do campo referente ao id da tabela onde serб feita a inserзгo.
	 *
	 * @return Id's dos registros inseridos.
	 */
	public function insert($campo);
	
	/**
	 * Mйtodo utilizado para executar updates no banco.
	 *
	 * @param
	 *
	 * @return Nъmero de linhas afetadas.
	 */
	public function update();
	
	/**
	 * Mйtodo utilizado para executar deletes no banco.
	 *
	 * @param
	 *
	 * @return Nъmero de linhas afetadas.
	 */
	public function delete();	
	
	/**
	 * Mйtodo utilizado para atribuir a query sql a ser executada.
	 *
	 * @param Query a ser executada.
	 *
	 * @return
	 */
	public function setSQL($sql);
	
	/**
	 * Mйtodo utilizado para obter a query a ser executada.
	 *
	 * @param
	 *
	 * @return Query a ser executada.
	 */
	public function getSQL();
	
	/**
	 * Mйtodo utilizado executar comandos sql.
	 *
	 * @param
	 *
	 * @return Retorna um objeto derivado da classe ADORecordSet. Se ocorrer erro, retorna FALSE.
	 */
	public function executeSQL();
}