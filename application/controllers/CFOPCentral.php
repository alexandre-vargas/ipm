<?php
class CFOPCentral extends Zend_Controller_Action {
	
	public function indexAction() {
		$this->getResponse()->setRedirect($this->view->url(array('controller' => 'cfop', 'action' => 'inserir'), null, true))->sendResponse();
	}
	
	public function inserirAction() {
	}
	
}