<?php

class Entity_EsqueciMinhaSenhaCentral extends Zend_Db_Table_Abstract {
	protected $_name = 'esqueci_minha_senha';
	protected $_primary = 'id';
	protected $_rowClass = 'Row_EsqueciMinhaSenha';
	
	protected $_referenceMap = array(
			'Usuarios' => array(
					self::COLUMNS => 'id_usuario',
	
					self::REF_TABLE_CLASS => 'Entity_Usuario',
					self::REF_COLUMNS => 'id'
			)
	);
}