<?php

class Entity_MunicipioCentral extends Zend_Db_Table_Abstract {
	protected $_name = 'municipio';
	protected $_primary = 'id';
	protected $_rowClass = 'Row_Municipio';
	
	protected $_dependentTables = array(
			'Entity_Pessoa');
	
	protected $_referenceMap = array(
			'Estado' => array(
					self::COLUMNS => 'id_estado',
					
					self::REF_TABLE_CLASS => 'Entity_Estado',
					self::REF_COLUMNS =>'id'));
}