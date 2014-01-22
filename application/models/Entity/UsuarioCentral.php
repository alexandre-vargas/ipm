<?php

class Entity_UsuarioCentral extends Zend_Db_Table_Abstract {
	protected $_name = 'usuario';
	protected $_primary = 'id';
	protected $_rowClass = 'Row_Usuario';
	
	protected $_referenceMap = array(
			'Pessoa' => array(
					self::COLUMNS => 'id_pessoa',
	
					self::REF_TABLE_CLASS => 'Entity_Pessoa',
					self::REF_COLUMNS => 'id'
			)
	);
}