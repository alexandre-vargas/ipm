<?php

class Entity_LoteEstadoContribuinteCentral extends Zend_Db_Table_Abstract {
	protected $_name = 'lote_estado_contribuinte';
	protected $_primary = 'id';
	protected $_rowClass = 'Row_LoteEstadoContribuinte';

	protected $_referenceMap = array(
		'Lote' => array(
			self::COLUMNS => 'id_lote',
			
			self::REF_TABLE_CLASS => 'Entity_LoteEstado',
			self::REF_COLUMNS => 'id'),
		'Contribuinte' => array(
			self::COLUMNS => 'id_contribuinte',
			
			self::REF_TABLE_CLASS => 'Entity_Contribuinte',
			self::REF_COLUMNS => 'id'));
}