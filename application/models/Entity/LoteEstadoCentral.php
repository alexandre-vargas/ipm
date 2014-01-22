<?php

class Entity_LoteEstadoCentral extends Zend_Db_Table_Abstract {
	protected $_name = 'lote_estado';
	protected $_primary = 'id';
	protected $_rowClass = 'Row_LoteEstado';
	
	protected $_dependentTables = array(
			'Entity_LoteEstadoContribuinte');
	
}