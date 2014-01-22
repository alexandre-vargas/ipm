<?php

class Entity_EstadoCentral extends Zend_Db_Table_Abstract {
	protected $_name = 'estado';
	protected $_primary = 'id';
	protected $_rowClass = 'Row_Estado';
	
	protected $_dependentMap = array(
			'Entity_Municipio');
	
}