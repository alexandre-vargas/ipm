<?php

class Entity_EmailCentral extends Zend_Db_Table_Abstract {
	protected $_name = 'email';
	protected $_primary = 'id';
	protected $_rowClass = 'Row_Email';
}