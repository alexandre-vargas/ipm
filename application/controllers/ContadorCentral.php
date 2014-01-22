<?php

class ContadorCentral extends Zend_Controller_Action {
	
	const TIPO_CONTROLE_DEFAULT = 1;
	const TIPO_CONTROLE_WORKFLOW = 2;
	
	protected $objZendSessionNamespace = null;
	
	public function init() {
		$this->objZendSessionNamespace = new Zend_Session_Namespace(strtolower($this->getRequest()->getControllerName()));
	}
	
	public function indexAction() {
		$this->getResponse()->setRedirect($this->view->url(array('controller' => 'contador', 'action' => 'listagem'), null, true))->sendResponse();
	}
	
	public function listagemAction() {
		$objZendDbTableContador = new Entity_Contador();
	
		$intQtdTotal = $objZendDbTableContador->getCount();
		$intQtdPerPage = $this->getParam('qtdPerPage', 5);
	
		$intQtdPages = ceil($intQtdTotal / $intQtdPerPage);
		$intPage = $this->getParam('page', 1);
	
		if((boolean) $intQtdPages && $intPage > $intQtdPages)
			$this->getResponse()->setRedirect($this->view->url(array('controlle' => 'contador', 'action' => 'listagem', 'qtdPerPage' => $intQtdPerPage, 'page' => 1)))->sendResponse();

		$intOffset = $intQtdPerPage * ($intPage - 1);
		$arrOrder = null;
	
		$objZendDbTableRowsetContador = $objZendDbTableContador->fetchAll(null, $arrOrder, $intQtdPerPage, $intOffset);
	
		$this->view->intQtdPages = $intQtdPages;
		$this->view->intPage = $intPage;
		$this->view->intQtdPerPage = $intQtdPerPage;
		$this->view->intQtdTotal = $intQtdTotal;

		$this->view->objZendDbTableRowsetContador = $objZendDbTableRowsetContador;
		
		$this->view->arrSucesso = $this->_helper->FlashMessenger->getMessages();
		$this->view->arrParams = $this->getRequest()->getParams();
		
	}
	
	public function inserirAction() {
		if(!Zend_Auth::getInstance()->hasIdentity())
			Zend_Layout::getMvcInstance()->setLayout('logout');
		$this->_helper->viewRenderer->setRender('form');
		
		$this->objZendSessionNamespace->strAction = strtolower($this->getRequest()->getActionName());
		
		$objZendDbTableEstado = Entity_Estado::getInstance();
		$objZendDbTableRowsetEstado = $objZendDbTableEstado->fetchAll();
		$this->view->objZendDbTableRowsetEstado = $objZendDbTableRowsetEstado;
		
		$arrParams = $this->getRequest()->getParams();
		if(!Zend_Auth::getInstance()->hasIdentity()) {
			$objZendSessionNamespaceCadastro = new Zend_Session_Namespace('cadastro');
			$arrParams['crc'] = $objZendSessionNamespaceCadastro->intCrc;
		}
		$this->view->arrParams = $arrParams;
			
		$this->view->boolCrcReadOnly = false;
		!Zend_Auth::getInstance()->hasIdentity() &&
			$this->view->boolCrcReadOnly = true;
		
		$this->view->intTipoControle = TIPO_CONTROLE_FORM_DEFAULT;
		!Zend_Auth::getInstance()->hasIdentity() &&
			$this->view->intTipoControle = TIPO_CONTROLE_FORM_WORKFLOW;
		
	}
	
	public function alterarAction() {
		if(!Zend_Auth::getInstance()->hasIdentity())
			Zend_Layout::getMvcInstance()->setLayout('logout');
		$this->_helper->viewRenderer->setRender('form');
		
		$this->objZendSessionNamespace->strAction = strtolower($this->getRequest()->getActionName());
		
		$arrParams = $this->getRequest()->getParams();
		if(!Zend_Auth::getInstance()->hasIdentity()) {
			$objZendSessionNamespaceCadastro = new Zend_Session_Namespace('cadastro');
			$arrParams['crc'] = $objZendSessionNamespaceCadastro->intCrc;
		}
			
		if(isset($arrParams['id'])) {
			$objZendDbTableContador = Entity_Contador::getInstance();
			$objZendDbTableRowContador = $objZendDbTableContador->find(base64_decode($arrParams['id']))->current();
			if(!isset($objZendDbTableRowContador)) {
				throw new Exception('Contador não encontrado');
			}
			$objZendDbTableRowPessoa = $objZendDbTableRowContador->findParentEntity_Pessoa();
			$arrParams = array_merge($objZendDbTableRowPessoa->toArray(), $objZendDbTableRowContador->toArray());
		}
		$this->view->arrParams = $arrParams;
		
		$objZendDbTableEstado = Entity_Estado::getInstance();
		$objZendDbTableRowsetEstado = $objZendDbTableEstado->fetchAll();
		$this->view->objZendDbTableRowsetEstado = $objZendDbTableRowsetEstado;
		
		$this->view->boolCrcReadOnly = false;
		!Zend_Auth::getInstance()->hasIdentity() &&
			$this->view->boolCrcReadOnly = true;
		
		$this->view->intTipoControle = TIPO_CONTROLE_FORM_DEFAULT;
		!Zend_Auth::getInstance()->hasIdentity() &&
			$this->view->intTipoControle = TIPO_CONTROLE_FORM_WORKFLOW;
	}
	
	public function salvarAction() {
		// Valida metodo.
		if(!$this->getRequest()->isPost()) 
			throw new Zend_Controller_Request_Exception('Acesso não permitido através deste metodo.');
	
		// Recepciona.
		$arrParams = $this->getRequest()->getParams();
		if(!Zend_Auth::getInstance()->hasIdentity()) {
			$objZendSessionNamespaceCadastro = new Zend_Session_Namespace('cadastro');
			$arrParams['crc'] = $objZendSessionNamespaceCadastro->intCrc;
		}
		
		// Filtra e formata os dados.
		$arrParams = array_map('trim', $arrParams);
		$arrParams = array_map('strtoupper', $arrParams);
		$arrParams['cep'] = Filtro::removeMascara($arrParams['cep']);
		$arrParams['email'] = strtolower($arrParams['email']);
		
		// Valida.
		if(!isset($arrParams['cpf_cnpj']) || trim($arrParams['cpf_cnpj']) == '' || !is_numeric($arrParams['cpf_cnpj']))
			$arrParams['erro'][] = 'CPF/CNPJ inválido.';

		if(!isset($arrParams['crc']) || trim($arrParams['crc']) == '')
			$arrParams['erro'][] = 'CRC inválido.';
		
		$arrDataCRC = explode('/', $arrParams['data_crc']);
		if(!isset($arrParams['data_crc']) || trim($arrParams['data_crc']) == '' || !checkdate ($arrDataCRC[1], $arrDataCRC[0], $arrDataCRC[2]))
			$arrParams['erro'][] = 'Data CRC inválida.';

		if(!isset($arrParams['descri']) || trim($arrParams['descri']) == '')
			$arrParams['erro'][] = 'Razão social inválida.';

		if(!isset($arrParams['cep']) || trim($arrParams['cep']) == '' || !is_numeric($arrParams['cep']))
			$arrParams['erro'][] = 'CEP inválido.';
	
		if(!isset($arrParams['tipo_logradouro']) || trim($arrParams['tipo_logradouro']) == '')
			$arrParams['erro'][] = 'Tipo Logradouro inválido.';
	
		if(!isset($arrParams['logradouro']) || trim($arrParams['logradouro']) == '')
			$arrParams['erro'][] = 'Logradouro inválido.';
	
		if(trim($arrParams['numero']) != '' && !is_numeric($arrParams['numero']))
			$arrParams['erro'][] = 'Número inválido.';
	
		if(trim($arrParams['numero']) == '' && trim($arrParams['complemento']) == '')
			$arrParams['erro'][] = 'Número ou complemento devem ser preenchidos.';
	
		if(!isset($arrParams['bairro']) || trim($arrParams['bairro']) == '')
			$arrParams['erro'][] = 'Bairro inválido.';

		if(!isset($arrParams['municipio']) || trim($arrParams['municipio']) == '')
			$arrParams['erro'][] = 'Município inválido.';
			
		if(!isset($arrParams['estado']) || trim($arrParams['estado']) == '')
			$arrParams['erro'][] = 'Estado inválido.';
			
		if(trim($arrParams['municipio']) != '' && trim($arrParams['estado']) != '') {
			$objZendDbTableRowMunicipio = Entity_Municipio::getInstance()->fetchRow(array('descri =?' => $arrParams['municipio']));
			if(!isset($objZendDbTableRowMunicipio)) {
				$arrParams['erro'][] = 'Município não encontrado.';
			} else {
				$objZendDbTableRowEstado = $objZendDbTableRowMunicipio->findParentEntity_Estado();
				if(trim($objZendDbTableRowEstado->sigla) != trim($arrParams['estado'])) {
					$objZendDbTableRowEstado = Entity_Estado::getInstance()->fetchRow(array('sigla =?' => trim($arrParams['estado'])));
					$arrParams['erro'][] = 'Município de ' . $objZendDbTableRowMunicipio->descri . ' não pertence ao estado ' . $objZendDbTableRowEstado->pronome . ' ' . $objZendDbTableRowEstado->descri . '.';
				}
			}
		}

		if(!isset($arrParams['email']) || trim($arrParams['email']) == '')
			$arrParams['erro'][] = 'Email inválido.';

		if(isset($arrParams['erro']) && count($arrParams['erro'])) {
			$this->forward($this->objZendSessionNamespace->strAction, null, null, $arrParams);
			return;
		}

		// Abre transação
		$objZendDbAdapterPDOMysql  = Zend_Registry::get('db');
		$objZendDbAdapterPDOMysql->beginTransaction();
		
		// Altera ou insere contador na base.
		$objZendDbTableContador = Entity_Contador::getInstance();
		$objZendDbTableRowContador = $objZendDbTableContador->fetchRow(array('crc =?' => $arrParams['crc']));
		if(isset($objZendDbTableRowContador)) {
			$strAcao = 'alterar';
			$objZendDbTableRowPessoa = $objZendDbTableRowContador->findParentEntity_Pessoa();
		} else {
			$strAcao = 'inserir';
			$objZendDbTableRowContador = $objZendDbTableContador->createRow();
			$objZendDbTablePessoa = Entity_Pessoa::getInstance();
			$objZendDbTableRowPessoa = $objZendDbTablePessoa->createRow();
		}

		$objZendDbTableRowPessoa->descri = $arrParams['descri'];
		$objZendDbTableRowPessoa->cpf_cnpj = $arrParams['cpf_cnpj'];
		$objZendDbTableRowPessoa->cep = $arrParams['cep'];
		$objZendDbTableRowPessoa->tipo_logradouro = $arrParams['tipo_logradouro'];
		$objZendDbTableRowPessoa->logradouro = $arrParams['logradouro'];
		$objZendDbTableRowPessoa->numero = $arrParams['numero'];
		$objZendDbTableRowPessoa->complemento = $arrParams['complemento'];
		$objZendDbTableRowPessoa->bairro = $arrParams['bairro'];
		$objZendDbTableRowPessoa->id_municipio = $objZendDbTableRowMunicipio->id;
		$objZendDbTableRowPessoa->municipio = $arrParams['municipio'];
		$objZendDbTableRowPessoa->estado = $arrParams['estado'];
		$objZendDbTableRowPessoa->email = $arrParams['email'];
		$objZendDbTableRowPessoa->log_usuario = (Zend_Auth::getInstance()->hasIdentity() ? Zend_Auth::getInstance()->getIdentity() : '');
		$objZendDbTableRowPessoa->save();

		$objZendDbTableRowContador->id_pessoa = $objZendDbTableRowPessoa->id;
		$objZendDbTableRowContador->crc = $arrParams['crc'];
		$objZendDbTableRowContador->data_crc = $arrDataCRC[2] . '-' . $arrDataCRC[1] . '-' . $arrDataCRC[0];
		$objZendDbTableRowContador->log_data = new Zend_Db_Expr("NOW()");
		$objZendDbTableRowContador->log_usuario = (Zend_Auth::getInstance()->hasIdentity() ? Zend_Auth::getInstance()->getIdentity() : '');
		$objZendDbTableRowContador->save();
	
		// Commita transação.
		$objZendDbAdapterPDOMysql->commit();

		if(!Zend_Auth::getInstance()->hasIdentity()) { 
			$this->getResponse()->setRedirect($this->view->url(array('controller' => 'cadastro', 'action' => 'selecao-contribuintes'), null, true))->sendResponse();
		} else {
			$this->_helper->FlashMessenger->addMessage($strAcao == 'alterar' ? 'Alteração realizada com sucesso.' : 'Inclusão realizada com sucesso.');
			$this->getResponse()->setRedirect($this->view->url(array('controller' => Zend_Auth::getInstance()->hasIdentity() ? 'contador' : 'login'), null, true))->sendResponse();
		}
		
	}
	
	
	public function excluirAction() {
		$intIdBase64 = $this->getRequest()->getParam('id');
		$intId = base64_decode($intIdBase64);
		
		$objZendDbTableContador = Entity_Contador::getInstance();
		$objZendDbTableRowContador = $objZendDbTableContador->find($intId)->current();
		try {
			$objZendDbTableRowContador->delete();
		} catch(Exception $e) {
			if(trim($e->getCode()) == EXCEPTION_FOREIGN_KEY) {
				$arrParams['erro'][] = 'Exclusão negada. Este registro é referenciado por um ou mais registros dependentes.';
				$this->forward('listagem', null, null, $arrParams);
				return;
			}
		}
		
		$this->_helper->FlashMessenger->addMessage('Exclusão realizada com sucesso.');
		$this->getResponse()->setRedirect($this->view->url(array('controller' => 'contador', 'action' => 'listagem'), null, true))->sendResponse();
	}
	
}
