<?php

class UsuarioCentral extends Zend_Controller_Action {
	
	public function init() {
		$strControllerName = strtolower($this->getRequest()->getControllerName());
		$this->view->headLink()->appendStylesheet('css/' . $strControllerName . '.css');
	}
	
	
	public function indexAction() {
		$this->getResponse()->setRedirect($this->view->url(array('controller' => 'usuario', 'action' => 'inserir'), null, true))->sendResponse();
	}

	public function inserirAction() {
	}

}