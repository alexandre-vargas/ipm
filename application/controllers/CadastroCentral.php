<?php
class CadastroCentral extends Zend_Controller_Action {
	
	const TIPO_CONTROLE_REMOVER = 2;
	
	const STATUS_PENDENTE = 1;
	const STATUS_APROVADO = 2;
	const STATUS_NAO_APROVADO = 3;
	
	protected $objZendSessionNamespace = null;
	
	public function init() {
		Zend_Layout::getMvcInstance()->setLayout('logout');
		$this->objZendSessionNamespace = new Zend_Session_Namespace(strtolower($this->getRequest()->getControllerName()));
		Zend_Auth::getInstance()->clearIdentity();
	}
	
	public function indexAction() {
		$this->getResponse()->setRedirect($this->view->url(array('controller' => 'cadastro', 'action' => 'opcoes'), null, true))->sendResponse();
	}
	
	public function opcoesAction() {
		$arrParams = $this->getRequest()->getParams();
		!isset($arrParams['opcao']) &&
			$arrParams['opcao'] = $this->objZendSessionNamespace->strOpcao;
		$this->view->arrParams = $arrParams;
	}
	
	public function receberOpcaoAction() {
		// Valida metodo.
		if(!$this->getRequest()->isPost()) 
			throw new Zend_Controller_Request_Exception('Acesso não permitido através deste metodo.');
		
		// Recepciona.
		$arrParams = $this->getRequest()->getParams();

		// Filtra
		$arrParams = array_map('trim', $arrParams);
		
		if(!isset($arrParams['opcao']) || trim($arrParams['opcao']) == '') {
			$arrParams['erro'][] = 'Escolha uma opção.';
			$this->forward('opcoes', null, null, $arrParams);
			return;
		}

		$this->objZendSessionNamespace->strOpcao = $arrParams['opcao'];
		
		if($arrParams['opcao'] == 'contribuinte')
			$this->getResponse()->setRedirect($this->view->url(array('controller' => 'cadastro', 'action' => 'inscricao-estadual'), null, true))->sendResponse();
		elseif($arrParams['opcao'] == 'contador')
			$this->getResponse()->setRedirect($this->view->url(array('controller' => 'cadastro', 'action' => 'crc'), null, true))->sendResponse();

	}
	
	
	
	
	
	
	
	public function inscricaoEstadualAction() {
		$arrParams = $this->getRequest()->getParams();
		!isset($arrParams['inscricao_estadual']) &&
			$arrParams['inscricao_estadual'] = $this->objZendSessionNamespace->intInscricaoEstadual;
		$this->view->arrParams = $arrParams;
	}
	
	public function receberInscricaoAction() {
		// Valida metodo.
		if(!$this->getRequest()->isPost())
			throw new Zend_Controller_Request_Exception('Acesso não permitido através deste metodo.');

		// Recepciona.
		$arrParams = $this->getRequest()->getParams();

		// Filtra.
		$arrParams = array_map('trim', $arrParams);
		$arrParams['inscricao_estadual'] = Filtro::removeMascara($arrParams['inscricao_estadual']);

		if(!isset($arrParams['inscricao_estadual']) || trim($arrParams['inscricao_estadual']) == '') {
			$arrParams['erro'][] = 'Informe a inscrição estadual.';
			$this->forward('inscricao-estadual', null, null, $arrParams);
			return;
		}

		$this->objZendSessionNamespace->intInscricaoEstadual = $arrParams['inscricao_estadual'];
		
		$objZendDbTableRowContribuinte = Entity_Contribuinte::getInstance()->fetchRow(array('inscricao_estadual =?' => $arrParams['inscricao_estadual']));
		if(isset($objZendDbTableRowContribuinte) && $objZendDbTableRowContribuinte->status != self::STATUS_PENDENTE) {
			$arrParams['erro'][] = 'Este contribuinte já foi analisado e seu parecer enviado para o e-mail de cadastro.';
			$this->forward('inscricao-estadual', null, null, $arrParams);
			return;
		}
/* **********************************************************************************************************************************************
 * ***************************************** Varrer todos os Redirects colocando sendResponse() no final padronizando o código*******************
 * **********************************************************************************************************************************************
 * **********************************************************************************************************************************************
 */
		if(isset($objZendDbTableRowContribuinte))
			$this->getResponse()->setRedirect($this->view->url(array('controller' => 'contribuinte', 'action' => 'alterar', 'id' => base64_encode($objZendDbTableRowContribuinte->id)), null, true))->sendResponse();
		else
			$this->getResponse()->setRedirect($this->view->url(array('controller' => 'contribuinte', 'action' => 'inserir'), null, true))->sendResponse();
	
	}
	
	
	
	
	
	
	public function crcAction() {
		$arrParams = $this->getRequest()->getParams();
		!isset($arrParams['crc']) &&
			$arrParams['crc'] = $this->objZendSessionNamespace->intCrc;
		$this->view->arrParams = $arrParams;
	}
	
	public function receberCrcAction() {
		// Valida metodo.
		if(!$this->getRequest()->isPost())
			throw new Zend_Controller_Request_Exception('Acesso não permitido através deste metodo.');
			
		// Recepciona.
		$arrParams = $this->getRequest()->getParams();

		// Filtra.
		$arrParams = array_map('trim', $arrParams);

		if(!isset($arrParams['crc']) || trim($arrParams['crc']) == '') {
			$arrParams['erro'][] = 'Informe o CRC.';
			$this->forward('crc', null, null, $arrParams);
			return;
		}

		$this->objZendSessionNamespace->intCrc = $arrParams['crc'];
		
		
		// Valida se o contador possui algum vinculo analisado
		$objZendDbTableRowContador = Entity_Contador::getInstance()->fetchRow(array('crc =?' => $arrParams['crc']));
		if(isset($objZendDbTableRowContador)) {
			$boolVinculoAnalisado = false;
			$objZendDbTableRowsetContadorContribuinte = Entity_ContadorContribuinte::getInstance()->fetchAll(array('id_contador =?' => $objZendDbTableRowContador->id));
			if((boolean)$objZendDbTableRowsetContadorContribuinte->count())
				foreach($objZendDbTableRowsetContadorContribuinte as $objZendDbTableRowContadorContribuinte)
					if($objZendDbTableRowContadorContribuinte->status != STATUS_PENDETE)
						$boolVinculoAnalisado = true;
			if($boolVinculoAnalisado) {
				$arrParams['erro'][] = 'Este contador já possui vinculos analisados. É necessário realizar o login para qualquer manutenção ou operação.';
				$this->forward('crc', null, null, $arrParams);
				return;
			}
		}
		
		
		if(!isset($objZendDbTableRowContador)) {
			$this->getResponse()->setRedirect($this->view->url(array('controller' => 'contador', 'action' => 'inserir'), null, true))->sendResponse();
		} else {
			$this->getResponse()->setRedirect($this->view->url(array('controller' => 'contador', 'action' => 'alterar', 'id' => base64_encode($objZendDbTableRowContribuinte->id)), null, true))->sendResponse();
		}
		
	}
	
	
	
	public function selecaoContribuintesAction() {
	}
	
	public function recuperarContribuinteAjaxAction() {
		// $this->getFrontController()->setParam('noViewRenderer', true);
		$this->getHelper('viewRenderer')->setNoRender();
		$this->getHelper('layout')->disableLayout();
		
		$arrParams = $this->getRequest()->getParams();
		$arrParams = array_map('trim', $arrParams);
		$arrParams['inscricao_estadual'] = Filtro::removeMascara($arrParams['inscricao_estadual']);
		
		$objZendView = new Zend_View();
		$objZendView->setScriptPath(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'scripts');
		// $objZendView->objZendDbTableRowPessoa = $objZendDbTableRowPessoa;
$arrParams['inscricao_estadual'] = '374000150110';
		$objZendDbTableContribuinte = new Entity_Contribuinte();
		$arrID = $this->objZendSessionNamespace->arrID;
		$arrID[] = $arrParams['inscricao_estadual']; 
		$strId = join(',', $arrID);
		$objZendDbTableRowsetContribuinte = $objZendDbTableContribuinte->fetchAll(array('inscricao_estadual in(' . $strId . ')'));
		
		$this->objZendSessionNamespace->arrID = $arrID;

		$objZendView->objZendDbTableRowsetContribuinte = $objZendDbTableRowsetContribuinte;
		$objZendView->intTipoControle = self::TIPO_CONTROLE_REMOVER;
		$strBody = $objZendView->render('contribuinte/listagem-conteudo.phtml');
		print $strBody;
	}
	
	public function conclusaoAction() {
		$this->objZendSessionNamespace->unsetAll();
	}	
	
	
}