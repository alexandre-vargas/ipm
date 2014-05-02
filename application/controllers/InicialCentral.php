<?php

class InicialCentral extends Zend_Controller_Action {
	
	public function indexAction() {
		$this->getResponse()->setRedirect($this->view->url(array('controller' => 'inicial', 'action' => 'visualizar'), null, true))->sendResponse();
	}

	public function visualizarAction() {
	}

}