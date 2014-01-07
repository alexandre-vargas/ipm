<?php

class InicialCentral extends Zend_Controller_Action {
	
	public function indexAction() {
		$this->getResponse()->setRedirect($this->view->url(array('controller' => 'inicial', 'action' => 'visualizar'), null, true))->sendResponse();
	}

	public function visualizarAction() {
		$strControllerName = strtolower($this->getRequest()->getControllerName());
		$strActionName = $this->getRequest()->getActionName();
		$this->view->headScript()->appendFile ('js/' . $strControllerName . '/' . $strActionName . '.js');
		$this->view->headLink()->appendStylesheet('css' . '/' . $strControllerName . '/' . $strActionName . '.css');
	}

}