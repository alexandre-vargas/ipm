<?php

class EmailCentral extends Zend_Controller_Action {
	
	const STATUS_INATIVO = 0;
	const STATUS_ATIVO = 1;
	
	public function init() {
		$this->getFrontController()->setParam('noViewRenderer', true);
	}
	
	public function indexAction() {
		$this->getResponse()->setRedirect($this->view->url(array('controller' => 'email', 'action' => 'processar'), null, true))->sendResponse();
	}
	
	public function processarAction() {
		$objZendDbTableRowsetEmail = Entity_Email::getInstance()->fetchAll(array('status =?' => self::STATUS_INATIVO));
		foreach($objZendDbTableRowsetEmail as $objZendDbTableRowEmail) {
			try {
				$objZendConfig = Zend_Registry::get('config');
	
				$objZendMailTransportSmtp = new Zend_Mail_Transport_Smtp($objZendConfig->email->smtp, $objZendConfig->email->auth->toArray());
				$objZendMail = new Zend_Mail('ISO-8859-1');
				$objZendMail->setFrom($objZendConfig->email->descri, $objZendConfig->prefeitura->descri);
				$objZendMail->addTo($objZendDbTableRowEmail->destino, 'Teste');
				$objZendMail->setSubject($objZendDbTableRowEmail->assunto);
				$objZendMail->setBodyHtml($objZendDbTableRowEmail->conteudo);
				$objZendMail->send($objZendMailTransportSmtp);
				
				$objZendDbTableRowEmail->status = self::STATUS_ATIVO;
				$objZendDbTableRowEmail->save();
			} catch(Exception $e) {
			}
		}
	}
	
	
}
