<?php

class UploadCentral extends Zend_Controller_Action {
	
	private $_objZendSessionNamespace;
	
	public function init() {
		Zend_Layout::getMvcInstance()->setLayout('logout');
		$this->_objZendSessionNamespace = new Zend_Session_Namespace(strtolower($this->getRequest()->getControllerName()), true);
	}
	
	public function indexAction() {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_objZendSessionNamespace->unsetAll();

        $this->view->arrSucesso = $this->_helper->FlashMessenger->getMessages();
        $this->view->arrParams = $this->getRequest()->getParams();
	}
	
    public function uploadAction() {
        // Valida ação do usuário.
        if(!$this->getRequest()->isPost())
            throw new Zend_Controller_Request_Exception('Acesso não permitido através deste metodo.');

        // Desabilita layout e view.
        Zend_Layout::getMvcInstance()->disableLayout();
        $this->getFrontController()->setParam('noViewRenderer', true);

        /*
        if(!file_exists($strDirDestino))
            mkdir($strDirDestino);
        */

        $strPathArquivoTmp = $_FILES['declaracao']['tmp_name'];

        $arrPathArquivoTmp = explode('/', $strPathArquivoTmp);
        $arrPathArquivoTmpInvertido = array_reverse($arrPathArquivoTmp);
        $strArquivoTmp = $arrPathArquivoTmpInvertido[0];
        $strPathArquivoDestino = Zend_Registry::get('config')->path->gia->processo_2 . $strArquivoTmp;

        move_uploaded_file($strPathArquivoTmp, $strPathArquivoDestino);

        $this->_helper->FlashMessenger->addMessage('Declaração enviada com sucesso. Caso exista algum problema com o arquivo entraremos em contato. Obrigado.');
        $this->getResponse()->setRedirect($this->view->url(array('controller' => 'upload', 'action' => 'index'), null, true))->sendResponse();
    }

}