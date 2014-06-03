<?php
class Entity_DeclaracaoGiaGiaCentral extends Zend_Db_Table_Abstract {
	protected $_name = 'declaracao_gia_gia';
	protected $_primary = 'id';
	protected $_rowClass = 'Row_DeclaracaoGiaGia';
	
	public function getCount($arrWhere = null) {
		$objZendDbAdapterPdoMysql = $this->getAdapter();
		$objZendDbStatementPdo = $objZendDbAdapterPdoMysql->query('select count(*) as count from ' . $this->_name . General::convertWhere($arrWhere));
		$arrResult = $objZendDbStatementPdo->fetchAll();

		return $arrResult[0]['count'];
	}
	
}