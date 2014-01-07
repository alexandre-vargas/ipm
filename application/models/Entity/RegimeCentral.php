<?php

class Entity_RegimeCentral extends Zend_Db_Table_Abstract {
	protected $_name = 'regime';
	protected $_primary = 'id';
	protected $_rowClass = 'Row_Regime';
	
	protected $_dependentTables = array(
			'Entity_Pessoa');
}