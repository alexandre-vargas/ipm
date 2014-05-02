<?php

class IndexCentral extends Zend_Controller_Action {
	
	public function indexAction() {
        $this->getResponse()->setRedirect($this->view->url(array('controller' => 'login', 'action' => 'index'), null, true))->sendResponse();
	}

}