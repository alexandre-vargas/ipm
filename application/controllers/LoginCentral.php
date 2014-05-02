<?php

class LoginCentral extends Zend_Controller_Action {
	
	private $_objZendSessionNamespace;
	
	public function init() {
		Zend_Layout::getMvcInstance()->setLayout('logout');
		$this->_objZendSessionNamespace = new Zend_Session_Namespace(strtolower($this->getRequest()->getControllerName()), true);
	}
	
	public function indexAction() {
		$this->getResponse()->setRedirect($this->view->url(array('controller' => 'login', 'action' => 'login'), null, true))->sendResponse();
	}
	
	public function loginAction() {
		Zend_Auth::getInstance()->clearIdentity();
		$this->_objZendSessionNamespace->unsetAll();
		
		$this->view->arrSucesso = $this->_helper->FlashMessenger->getMessages();
		$this->view->arrParams = $this->getRequest()->getParams();
	}
	
	public function logarAction() {
		// Valida ação do usuário.
		if(!$this->getRequest()->isPost())
			throw new Zend_Controller_Request_Exception('Acesso não permitido através deste metodo.');

		// Recepciona.
		$arrParams = $this->getRequest()->getParams();

		// Filtra
		$arrParams = array_map('trim', $arrParams);

		// Validação 1
		if($arrParams['usuario'] == '' || $arrParams['senha'] == '') {
			$arrParams['erro'][] = 'Informe Usuáio e Senha.';
			$this->forward('login', null, null, $arrParams);
			return;
		}

		// Configura adaptador de Banco - Tabela (Zend_Auth_Adapter_DbTable), seta o nome da tabela e os campos usuário e senha (setTableName(), setIdentityColumn(), setCredentialColumn()), e seta os valores de cada campo.
		$objZendAuthAdapterDbTable = $this->_getAuthAdapter();
		$objZendAuthAdapterDbTable->setIdentity($arrParams['usuario']);
		$objZendAuthAdapterDbTable->setCredential(md5($arrParams['senha'])); // Criptografia de mão única 128 bits.
		
		// Zend_Auth -Aplica a autenticação com base no adaptador configurado (authenticate()) e valida esta autenticação (isValid()).
		$objZendAuth = Zend_Auth::getInstance();
		$objZendAuthResult = $objZendAuth->authenticate($objZendAuthAdapterDbTable);
		if(!$objZendAuthResult->isValid()) {
			$arrParams['erro'][] = 'Login inválido.';
			$this->_forward('login', null, null, $arrParams);
			return;
		}

		// Verifica se o usuário está ativo. 
		// Mesmo que usuário e senha estejam corretos, esta Action ainda deve verificar se o usuário está de fato ativo. 
		// Anteriormente, esta verificação era feita junto com a autenticação mas isto impossibilitava uma distinção mais precisa do real motivo de recusa no login. Por este motivo o código que faz esta verificação no metodo _getAdapter() foi comentado.
		$objZendDbTableRowUsuario = Entity_Usuario::getInstance()->fetchRow(array('descri =?' => $arrParams['usuario']));
		if(!(boolean)$objZendDbTableRowUsuario->status) {
			$arrParams['erro'][] = 'Usuário inativo.';
			$this->forward('login', null, null, $arrParams);
			return;
		}

		$this->_inicializa($objZendDbTableRowUsuario);

		$this->getResponse()->setRedirect($this->view->url(array('controller' => 'inicial', 'action' => 'index'), null, true))->sendResponse();
	}
	
	private function _getAuthAdapter() {
		$objZendAuthAdapterDbTable = new Zend_Auth_Adapter_DbTable(Zend_Registry::get('db'));
		$objZendAuthAdapterDbTable->setTableName('usuario')
			->setIdentityColumn('descri')
			->setCredentialColumn('senha')
			/* ->setCredentialTreatment('? AND status = ' . STATUS_ATIVO) */;

		return $objZendAuthAdapterDbTable;					
		
	}

	
	private function _inicializa($objZendDbTableRowUsuario) {
		$this->_objZendSessionNamespace->objZendDbTableRowUsuario = $objZendDbTableRowUsuario;
		
		$objZendDbTableRowPessoa = $objZendDbTableRowUsuario->findParentEntity_Pessoa();
		$this->_objZendSessionNamespace->objZendDbTableRowPessoa = $objZendDbTableRowPessoa;

		if((boolean)$objZendDbTableRowPessoa->findEntity_Contribuinte()->count())
			$this->_objZendSessionNamespace->objZendDbTableRowContribuinte = $objZendDbTableRowPessoa->findEntity_Contribuinte()->current();

		(boolean)$objZendDbTableRowPessoa->findEntity_Contador()->count() &&
			$this->_objZendSessionNamespace->objZendDbTableRowContador = $objZendDbTableRowPessoa->findEntity_Contador()->current();
		
		$objZendDbTableRowMunicipio = $objZendDbTableRowPessoa->findParentEntity_Municipio();
		$this->_objZendSessionNamespace->objZendDbTableRowMunicipio = $objZendDbTableRowMunicipio;
		
		$objZendDbTableRowEstado = $objZendDbTableRowMunicipio->findParentEntity_Estado();
		$this->_objZendSessionNamespace->objZendDbTableRowEstado = $objZendDbTableRowEstado;
	}
	
	
	
	
	
	public function esqueciMinhaSenhaAction() {
		$this->view->arrSucesso = $this->_helper->FlashMessenger->getMessages();
		$this->view->arrParams = $this->getRequest()->getParams();
	}
	
	public function reenviarAction() {
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
		if($arrParams['usuario'] == '' || !is_numeric($arrParams['usuario']))
			$arrParams['erro'][] = 'Inscricao Estadual inválida.';
		
		if($arrParams['email'] == '')
			$arrParams['erro'][] = 'Email inválido.';

		if(isset($arrParams['erro']) && count($arrParams['erro'])) {
			$this->forward('esqueci-minha-senha', null, null, $arrParams);
			return;
		}

		// Validação 2
		$objZendDbTableUsuario = Esntity_Usuario::getInstance();
		$objZendDbTableRowUsuario = $objZendDbTableUsuario->fetchRow(
				array('descri =?' => $arrParams['usuario']));
		if(!isset($objZendDbTableRowUsuario)) {
			$arrParams['erro'][] = 'CPF / CNPJ / Inscrição Estadual não encontrada.';
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
			
		
		
		
		
		
		
		$this->_helper->FlashMessenger->addMessage('Nova senha gerada com sucesso. Em alguns instantes você receberá um e-mail com o link de confirmação. Acesse seu e-mail e clique no link para finalizar o processo.');
		$this->getResponse()->setRedirect($this->view->url(array('controller' => 'login', 'action' => 'esqueci-minha-senha'), null, true))->sendResponse();
	}
	
	
}