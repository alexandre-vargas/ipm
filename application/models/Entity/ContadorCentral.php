<?php
class Entity_ContadorCentral extends Zend_Db_Table_Abstract {
	protected $_name = 'contador';
	protected $_primary = 'id';
	protected $_rowClass = 'Row_Contador';
	
	protected $_referenceMap = array(
			'Pessoa' => array(
					self::COLUMNS => 'id_pessoa',
						
					self::REF_TABLE_CLASS => 'Entity_Pessoa',
					self::REF_COLUMNS => 'id'));
					
	public function getCount($arrWhere = null) {
		$objZendDbAdapterPdoMysql = $this->getAdapter();
		$objZendDbStatementPdo = $objZendDbAdapterPdoMysql->query('select count(*) as count from ' . $this->_name . General::convertWhere($arrWhere));
		$arrResult = $objZendDbStatementPdo->fetchAll();

		return $arrResult[0]['count'];
	}
	
}