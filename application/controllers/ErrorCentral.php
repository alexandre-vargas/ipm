<?php
class ErrorCentral extends Zend_Controller_Action
{
	public function errorAction() {
		// Zend_Layout::getMvcInstance()->disableLayout();
		$errors = $this->_getParam('error_handler');
print '<pre>';
print_r($errors->exception);
die;
		switch ($errors->type) {
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
				//404 error -- controller or action not found
				$this->getResponse()->setHttpResponseCode(404);
				$this->view->HeadTitle('404 Página não encontrada');
				$this->view->message = 'Página não encontrada.';
				break;

			default:
				//application error
				//$this->getResponse()->setHttpResponseCode(500);
				$this->view->HeadTitle('Ops!');
				$this->view->message = 'Página não encontrada ou serviço indisponível.';
				break;
		}

		$this->view->env = dirname(dirname(__FILE__));
		$this->view->exception = $errors->exception;
		$this->view->request = $errors->request;

		$this->view->objRegistry = Zend_Registry::get('config');
	}
}