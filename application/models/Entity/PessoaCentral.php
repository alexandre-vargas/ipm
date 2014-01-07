<?php

class Entity_PessoaCentral extends Zend_Db_Table_Abstract {
	protected $_name = 'pessoa';
	protected $_primary = 'id';
	protected $_rowClass = 'Row_Pessoa';

	protected $_referenceMap = array(
			'Municipio' => array(
					self::COLUMNS => 'id_municipio',
					
					self::REF_TABLE_CLASS => 'Entity_Municipio',
					self::REF_COLUMNS => 'id'));
	
	protected $_dependentTables = array(
			'Entity_Usuario',
			'Entity_Contribuinte');

	
	
	public function getCount($arrWhere = null) {
		$objZendDbAdapterPdoMysql = $this->getAdapter();
		$objZendDbStatementPdo = $objZendDbAdapterPdoMysql->query('select count(*) as count from ' . $this->_name . General::convertWhere($arrWhere));
		$arrResult = $objZendDbStatementPdo->fetchAll();

		return $arrResult[0]['count'];
	}
}