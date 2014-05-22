<?php
class Entity_GestorCentral extends Zend_Db_Table_Abstract {
	protected $_name = 'gestor';
	protected $_primary = 'id';
	protected $_rowClass = 'Row_Gestor';
	
	protected $_referenceMap = array(
			'Pessoa' => array(
					self::COLUMNS => 'id_pessoa',
						
					self::REF_TABLE_CLASS => 'Entity_Pessoa',
					self::REF_COLUMNS => 'id'));
}