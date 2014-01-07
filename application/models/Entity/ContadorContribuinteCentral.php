<?php
class Entity_ContadorContribuinteCentral extends Zend_Db_Table_Abstract {
	protected $_name = 'contador_contribuinte';
	protected $_primary = array('id_contador', 'id_contribuinte');
	protected $_rowClass = 'Row_ContadorContribuinte';
	
	protected $_referenceMap = array(
			'Contador' => array(
					self::COLUMNS => 'id_contador',
						
					self::REF_TABLE_CLASS => 'Entity_Contador',
					self::REF_COLUMNS => 'id'),
			'Contribuinte' => array(
					self::COLUMNS => 'id_contribuinte',
						
					self::REF_TABLE_CLASS => 'Entity_Contribuinte',
					self::REF_COLUMNS => 'id'));

}