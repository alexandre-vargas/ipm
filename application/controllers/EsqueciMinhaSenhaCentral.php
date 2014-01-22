<?php

class EsqueciMinhaSenhaCentral extends Zend_Controller_Action {
	
	const EMAIL_ASSUNTO = 'SIGICMS - Esqueci minha senha.';
	
	public function init() {
		Zend_Layout::getMvcInstance()->setLayout('logout');
	}
	
	public function indexAction() {
		$this->getResponse()->setRedirect($this->view->url(array('controller' => 'esqueci-minha-senha', 'action' => 'esqueci-minha-senha'), null, true))->sendResponse();
	}
	
	public function esqueciMinhaSenhaAction() {
		$this->view->arrSucesso = $this->_helper->FlashMessenger->getMessages();
		$this->view->arrParams = $this->getRequest()->getParams();
	}
	
	public function salvarAction() {
		// Valida metodo.
		if(!$this->getRequest()->isPost())
			throw new Zend_Controller_Request_Exception('Acesso não permitido através deste metodo.');
				
		
		// Recepciona POST e retira os espaços.
		$arrParams = $this->getRequest()->getParams();
		$arrParams = array_map('trim', $arrParams);

		// Retira máscaras.
		$objFilter = new Filter_Transliterate();
		$arrParams['usuario'] = $objFilter->filter($arrParams['usuario']);

		// Validação 1
		if($arrParams['usuario'] == '')
			$arrParams['erro'][] = 'Usuário inválido.';
		
		if($arrParams['email'] == '')
			$arrParams['erro'][] = 'Email inválido.';

		if(isset($arrParams['erro']) && count($arrParams['erro'])) {
			$this->forward('esqueci-minha-senha', null, null, $arrParams);
			return;
		}

		// Validação 2
		$objZendDbTableUsuarios = Entity_Usuario::getInstance();
		$objZendDbTableRowUsuario = $objZendDbTableUsuarios->fetchRow(
				array('descri =?' => $arrParams['usuario']));
		if(!isset($objZendDbTableRowUsuario)) {
			$arrParams['erro'][] = 'Usuário não encontrado.';
			$this->forward('esqueci-minha-senha', null, null, $arrParams);
			return;
		}

		// Validação 3
		$objZendDbTableRowPessoa = $objZendDbTableRowUsuario->findParentEntity_Pessoa();
		if($arrParams['email'] != $objZendDbTableRowPessoa->email) {
			$arrParams['erro'][] = 'Email não encontrado.';
			$this->forward('esqueci-minha-senha', null, null, $arrParams);
			return;
		}

		// Grava solicitação de nova senha.
		$objZendDbTableEsqueciMinhaSenha = Entity_EsqueciMinhaSenha::getInstance();
		$objZendDbTableEsqueciMinhaSenhaRow = $objZendDbTableEsqueciMinhaSenha->fetchRow(array('id_usuario =?' => $objZendDbTableRowUsuario->id));
		!isset($objZendDbTableEsqueciMinhaSenhaRow) &&
			$objZendDbTableEsqueciMinhaSenhaRow = $objZendDbTableEsqueciMinhaSenha->createRow();
		$objZendDbTableEsqueciMinhaSenhaRow->id_usuario = $objZendDbTableRowUsuario->id;
		$strSenha = rand(10000000, 99999999);
		$objZendDbTableEsqueciMinhaSenhaRow->senha = $strSenha;
		$objZendDbTableEsqueciMinhaSenhaRow->save();

		$objZendView = new Zend_View();
		$objZendView->setScriptPath(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'scripts');
		$objZendView->objZendDbTableRowUsuario = $objZendDbTableRowUsuario;
		$objZendView->strSenha = $strSenha;
		$objZendView->strSenhaMd5 = md5($strSenha);
		$objZendView->objZendDbTableRowPessoa = $objZendDbTableRowPessoa;
		$strBody = $objZendView->render('email/esqueci-minha-senha.phtml');
		
		$objZendDbTableRowEmail = Entity_Email::getInstance()->createRow();
		$objZendDbTableRowEmail->conteudo = $strBody;
		$objZendDbTableRowEmail->assunto = self::EMAIL_ASSUNTO;
		$objZendDbTableRowEmail->destino = $arrParams['email'];
		$objZendDbTableRowEmail->log_data = new Zend_Db_Expr("NOW()");
		$objZendDbTableRowEmail->save();
		
		$this->_helper->FlashMessenger->addMessage('Nova senha gerada com sucesso. Em alguns instantes você receberá um e-mail com o link de confirmação. Por favor, acesse seu e-mail e clique no link para finalizar o processo.');
		$this->getResponse()->setRedirect($this->view->url(array('controller' => 'esqueci-minha-senha', 'action' => 'esqueci-minha-senha'), null, true))->sendResponse();
		return;
	}
	
	
	public function redefinirAction() {
		// Valida metodo.
		if(!$this->getRequest()->isGet())
			throw new Zend_Controller_Request_Exception('Acesso não permitido através deste metodo.');
				

		// Recepciona POST e retira os espaços.
		$arrParams = $this->getRequest()->getParams();
		$arrParams = array_map('trim', $arrParams);
		
		$objZendDbTableEsqueciMinhaSenha = Entity_EsqueciMinhaSenha::getInstance();
		$objZendDbTableEsqueciMinhaSenhaRow = $objZendDbTableEsqueciMinhaSenha->fetchRow(array('id_usuario =?' => $arrParams['usuario']));
		if(md5($objZendDbTableEsqueciMinhaSenhaRow->senha) != $arrParams['passe'])
			throw new Zend_Controller_Request_Exception('Acesso não permitido através deste metodo.');		
			
		
		$objZendDbTableRowUsuario = $objZendDbTableEsqueciMinhaSenhaRow->findParentEntity_Usuario();
		$objZendDbTableRowUsuario->senha = md5($objZendDbTableEsqueciMinhaSenhaRow->senha);
		$objZendDbTableRowUsuario->log_data = new Zend_Db_Expr("NOW()");
		$objZendDbTableRowUsuario->log_usuario = Zend_Auth::getInstance()->getIdentity();
		$objZendDbTableRowUsuario->save();

		$objZendHttpClient = new Zend_Http_Client();
		$objZendHttpClient->setConfig(array('timeout' => 120)); // Tempo definido em segundos.
		$objZendHttpClient->setUri( (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http') . '://' .$_SERVER['HTTP_HOST'] . '/login/logar');
		$objZendHttpClient->setParameterPost(
			array('usuario' => $objZendDbTableRowUsuario->descri, 
				'senha' => $objZendDbTableEsqueciMinhaSenhaRow->senha));

		$objZendHttpClient->request('POST');
		$this->getResponse()->setRedirect($this->view->url(array('controller' => 'inicial', 'action' => 'visualizar'), null, true))->sendResponse();
	}
	
}