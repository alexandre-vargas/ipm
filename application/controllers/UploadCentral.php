<?php

class UploadCentral extends Zend_Controller_Action {

    const FILE_SIZE = 30;

	private $_objZendSessionNamespace;
	
	public function init() {
		Zend_Layout::getMvcInstance()->setLayout('logout');
		$this->_objZendSessionNamespace = new Zend_Session_Namespace(strtolower($this->getRequest()->getControllerName()), true);

	}
	
	public function indexAction() {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_objZendSessionNamespace->unsetAll();

        $this->view->arrSucesso = $this->_helper->FlashMessenger->getMessages('sucesso');
        $this->view->arrErro = $this->_helper->FlashMessenger->getMessages('erro');
        $this->view->arrParams = $this->getRequest()->getParams();
	}
	
    public function uploadAction() {
        // Desabilita layout e view.
        Zend_Layout::getMvcInstance()->disableLayout();
        $this->getFrontController()->setParam('noViewRenderer', true);

        // Valida ação do usuário.
        if(!$this->getRequest()->isPost())
            throw new Zend_Controller_Request_Exception('Acesso não permitido através deste metodo.');

        /** TODO is_uploaded_file() */
        if(strlen($_FILES['declaracao']['name']) > self::FILE_SIZE) {
            $this->_helper->FlashMessenger->setNamespace('erro')->addMessage('O nome do arquivo não pode conter mais que ' . self::FILE_SIZE . ' caracteres');
            $this->getResponse()->setRedirect($this->view->url(array('controller' => 'upload', 'action' => 'index'), null, true))->sendResponse();
            die;
        }

        /** TODO http://br2.php.net/manual/pt_BR/features.file-upload.errors.php  -- Possíveis erros de upload */
        $strPathArquivoTmp = $_FILES['declaracao']['tmp_name'];

        $arrPathArquivoTmp = explode('/', $strPathArquivoTmp);
        $arrPathArquivoTmpInvertido = array_reverse($arrPathArquivoTmp);
        $strArquivoTmp = $arrPathArquivoTmpInvertido[0];
        $strPathArquivoDestino = Zend_Registry::get('config')->path->declaracao->contribuinte->pendente  . date('Y_m_d_H_i_s') . '_' .  $strArquivoTmp . '_' . $_FILES['declaracao']['name'];

        move_uploaded_file($strPathArquivoTmp, $strPathArquivoDestino);

        $this->_helper->FlashMessenger->setNamespace('sucesso')->addMessage('Declaração enviada com sucesso. Caso exista algum problema com o arquivo entraremos em contato via e-mail. Obrigado.');
        $this->getResponse()->setRedirect($this->view->url(array('controller' => 'upload', 'action' => 'index'), null, true))->sendResponse();
    }

}