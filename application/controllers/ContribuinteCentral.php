<?php

class ContribuinteCentral extends Zend_Controller_Action {
	
	const ORIGEM_LOTE = 1; 
	const ORIGEM_CRUD = 2;
	
	protected $objZendSessionNamespace = null;
	
	public function init() {
		set_time_limit(0);
		// Aviso: set_time_limit() não tem efeito quando o PHP esta sendo executado em safe mode. Não existe como contornar sem desabilitar o safe mode ou mudar o limite de tempo no php.ini.
		$this->objZendSessionNamespace = new Zend_Session_Namespace(strtolower($this->getRequest()->getControllerName()));
	}
	
	public function indexAction() {
		$this->getResponse()->setRedirect($this->view->url(array('controller' => 'contribuinte', 'action' => 'listagem'), null, true))->sendResponse();
	}
	
	public function listagemAction() {
		$this->view->intTipoControle = TIPO_CONTROLE_LISTAGEM_DEFAULT;
		if((boolean)$this->getRequest()->getParam('ajax')) {
			$this->getHelper('layout')->disableLayout();
			$this->view->boolEscondeTitulo = true;
			$this->view->intTipoControle = TIPO_CONTROLE_LISTAGEM_SELECIONAR;
		} 
		
		$objZendDbTableRegime = Entity_Regime::getInstance();
		$objZendDbTableRowsetRegime = $objZendDbTableRegime->fetchAll();
		$this->view->objZendDbTableRowsetRegime = $objZendDbTableRowsetRegime;
	
		$objZendDbTableContribuinte = new Entity_Contribuinte();
	
		/*
		$intAnoBase = $this->getRequest()->getParam('ano_base', Date('Y'));
		$arrWhere = array('ano_base =?' => $intAnoBase);
		*/
		
		$intQtdTotal = $objZendDbTableContribuinte->getCount();
		$intQtdPerPage = $this->getParam('qtdPerPage', 5);
	
		$intQtdPages = ceil($intQtdTotal / $intQtdPerPage);
		$intPage = $this->getParam('page', 1);
	
		if((boolean) $intQtdPages && $intPage > $intQtdPages)
			$this->getResponse()->setRedirect($this->view->url(array('controller' => 'contribuinte', 'action' => 'listagem', 'qtdPerPage' => $intQtdPerPage, 'page' => 1)))->sendResponse();

		$intOffset = $intQtdPerPage * ($intPage - 1);
		$arrOrder = null;
	
		$objZendDbTableRowsetContribuinte = $objZendDbTableContribuinte->fetchAll(null, $arrOrder, $intQtdPerPage, $intOffset);
	
	
		$this->view->intQtdPages = $intQtdPages;
		$this->view->intPage = $intPage;
		$this->view->intQtdPerPage = $intQtdPerPage;
		$this->view->intQtdTotal = $intQtdTotal;
	
		$this->view->objZendDbTableRowsetContribuinte = $objZendDbTableRowsetContribuinte;

		$this->view->arrSucesso = $this->_helper->FlashMessenger->getMessages();
		$this->view->arrParams = $this->getRequest()->getParams();
	}
	
	public function inserirAction() {
        Zend_Layout::getMvcInstance()->setLayout('logout');
        /*
		!Zend_Auth::getInstance()->hasIdentity() &&
			Zend_Layout::getMvcInstance()->setLayout('logout');
        */
		$this->_helper->viewRenderer->setRender('form');

		$this->objZendSessionNamespace->strAction = strtolower($this->getRequest()->getActionName());

		$arrParams = $this->getRequest()->getParams();
		if(!Zend_Auth::getInstance()->hasIdentity()) {
			$objZendSessionNamespaceCadastro = new Zend_Session_Namespace('cadastro');
			$arrParams['inscricao_estadual'] = $objZendSessionNamespaceCadastro->intInscricaoEstadual;
		}
		$this->view->arrParams = $arrParams;

		$objZendDbTableRegime = Entity_Regime::getInstance();
		$objZendDbTableRowsetRegime = $objZendDbTableRegime->fetchAll();
		$this->view->objZendDbTableRowsetRegime = $objZendDbTableRowsetRegime;

		$objZendDbTableEstado = Entity_Estado::getInstance();
		$objZendDbTableRowsetEstado = $objZendDbTableEstado->fetchAll();
		$this->view->objZendDbTableRowsetEstado = $objZendDbTableRowsetEstado;

		$this->view->boolInscricaoEstadualReadOnly = false;
		!Zend_Auth::getInstance()->hasIdentity() &&
			$this->view->boolInscricaoEstadualReadOnly = true;
		
		$this->view->intTipoControle = TIPO_CONTROLE_FORM_DEFAULT;
		!Zend_Auth::getInstance()->hasIdentity() &&
			$this->view->intTipoControle = TIPO_CONTROLE_FORM_WORKFLOW;
	}
	
	public function alterarAction() {
		!Zend_Auth::getInstance()->hasIdentity() &&
			Zend_Layout::getMvcInstance()->setLayout('logout');
		$this->_helper->viewRenderer->setRender('form');
		
		$this->objZendSessionNamespace->strAction = strtolower($this->getRequest()->getActionName());
		
		$arrParams = $this->getRequest()->getParams();
		if(!Zend_Auth::getInstance()->hasIdentity()) {
			$objZendSessionNamespaceCadastro = new Zend_Session_Namespace('cadastro');
			$arrParams['inscricao_estadual'] = $objZendSessionNamespaceCadastro->intInscricaoEstadual;
		}
		
		if(isset($arrParams['id'])) {
			$objZendDbTableContribuinte = Entity_Contribuinte::getInstance();
			$objZendDbTableRowContribuinte = $objZendDbTableContribuinte->find(base64_decode($arrParams['id']))->current();
			if(!isset($objZendDbTableRowContribuinte)) {
				throw new Exception('Contribuinte não encontrado');
			}
			$objZendDbTableRowPessoa = $objZendDbTableRowContribuinte->findParentEntity_Pessoa();
			$arrParams = array_merge($objZendDbTableRowPessoa->toArray(), $objZendDbTableRowContribuinte->toArray());
		}
		$this->view->arrParams = $arrParams;
		
		$objZendDbTableRegime = Entity_Regime::getInstance();
		$objZendDbTableRowsetRegime = $objZendDbTableRegime->fetchAll();
		$this->view->objZendDbTableRowsetRegime = $objZendDbTableRowsetRegime;
		
		$objZendDbTableEstado = Entity_Estado::getInstance();
		$objZendDbTableRowsetEstado = $objZendDbTableEstado->fetchAll();
		$this->view->objZendDbTableRowsetEstado = $objZendDbTableRowsetEstado;
		
		$this->view->boolInscricaoEstadualReadOnly = false;
		!Zend_Auth::getInstance()->hasIdentity() &&
			$this->view->boolInscricaoEstadualReadOnly = true;
		
		$this->view->intTipoControle = TIPO_CONTROLE_FORM_DEFAULT;
		!Zend_Auth::getInstance()->hasIdentity() &&
			$this->view->intTipoControle = TIPO_CONTROLE_FORM_WORKFLOW;
	}
	
	public function salvarAction() {
		// Valida metodo.
		if(!$this->getRequest()->isPost())			
			throw new Zend_Controller_Request_Exception('Acesso não permitido através deste metodo.');		
			
		// Recepciona POST e retira os espaços.
		$arrParams = $this->getRequest()->getParams();
		
		// Troca o valor do parâmetro
		if(!Zend_Auth::getInstance()->hasIdentity()) {
			$objZendSessionNamespaceCadastro = new Zend_Session_Namespace('cadastro');
			$arrParams['inscricao_estadual'] = $objZendSessionNamespaceCadastro->intInscricaoEstadual;
		}

		// Filtra e formata os dados.
		$arrParams = array_map('trim', $arrParams);
		$arrParams = array_map('strtoupper', $arrParams);
		$arrParams['inscricao_estadual'] = Filtro::removeMascara($arrParams['inscricao_estadual']);
		$arrParams['cnae'] = Filtro::removeMascara($arrParams['cnae']);
		$arrParams['cep'] = Filtro::removeMascara($arrParams['cep']);
		$arrParams['email'] = strtolower($arrParams['email']);
		
		// Valida.
		if(!isset($arrParams['id_regime']) || trim($arrParams['id_regime']) == '' || !is_numeric($arrParams['id_regime']))
			$arrParams['erro'][] = 'Regime atual inválido.';

		if(!isset($arrParams['cpf_cnpj']) || trim($arrParams['cpf_cnpj']) == '' || !is_numeric($arrParams['cpf_cnpj']))
			$arrParams['erro'][] = 'CPF/CNPJ inválido.';

		if(!isset($arrParams['inscricao_estadual']) || trim($arrParams['inscricao_estadual']) == '' || !is_numeric($arrParams['inscricao_estadual']))
			$arrParams['erro'][] = 'Inscrição estadual inválida.';
		
		if(!isset($arrParams['descri']) || trim($arrParams['descri']) == '')
			$arrParams['erro'][] = 'Razão social inválida.';
		
		if(!isset($arrParams['cnae']) || trim($arrParams['cnae']) == '' || !is_numeric($arrParams['cnae']))
			$arrParams['erro'][] = 'CNAE inválido.';
	
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
		
		// Altera ou insere contribuinte na base.
		$objZendDbTableContribuinte = Entity_Contribuinte::getInstance();
		$objZendDbTableRowContribuinte = $objZendDbTableContribuinte->fetchRow(array('inscricao_estadual =?' => $arrParams['inscricao_estadual']));

		if(isset($objZendDbTableRowContribuinte)) { // É uma alteração
			$strAcao = 'alterar';
			$objZendDbTableRowPessoa = $objZendDbTableRowContribuinte->findParentEntity_Pessoa();
		} else { // É uma inserção
			$strAcao = 'inserir';
			$objZendDbTableRowContribuinte = $objZendDbTableContribuinte->createRow();
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
		
		$objZendDbTableRowContribuinte->id_pessoa = $objZendDbTableRowPessoa->id;
		$objZendDbTableRowContribuinte->id_regime = $arrParams['id_regime'];
		$objZendDbTableRowContribuinte->inscricao_estadual = $arrParams['inscricao_estadual'];
		$objZendDbTableRowContribuinte->cnae = $arrParams['cnae'];

		if($strAcao == 'inserir') { // É uma inserção.
			$objZendDbTableRowContribuinte->origem = ORIGEM_CRUD;
			$objZendDbTableRowContribuinte->status = STATUS_PENDENTE;
		}	
		
		// É um complemento de cadastro.
		$objZendDbTableRowContribuinte->cadastro_complementado = 0;
		$objZendDbTableRowContribuinte->origem == ORIGEM_LOTE &&
			$objZendDbTableRowContribuinte->status == STATUS_PENDENTE &&
				$objZendDbTableRowContribuinte->cadastro_complementado = 1;
		
		$objZendDbTableRowContribuinte->log_data = new Zend_Db_Expr("NOW()");
		$objZendDbTableRowContribuinte->log_usuario = (Zend_Auth::getInstance()->hasIdentity() ? Zend_Auth::getInstance()->getIdentity() : '');
		$objZendDbTableRowContribuinte->save();
	
		// Commita transação.
		$objZendDbAdapterPDOMysql->commit();

		if(!Zend_Auth::getInstance()->hasIdentity()) {
			$this->getResponse()->setRedirect($this->view->url(array('controller' => 'cadastro', 'action' => 'conclusao'), null, true))->sendResponse();
		} else {
			$objZendSessionNamespaceLogin = new Zend_Session_Namespace('login');
			$objZendDbTableRowUsuario = $objZendSessionNamespaceLogin->objZendDbTableRowUsuario;
			$this->_helper->FlashMessenger->addMessage($strAcao == 'alterar' ? 'Alteração realizada com sucesso.' : 'Inclusão realizada com sucesso.');
			$this->getResponse()->setRedirect($this->view->url(array('controller' => ($objZendDbTableRowUsuario->perfil == PERFIL_GESTOR ? 'contribuinte' : 'upload'), 'action' => 'index'), null, true))->sendResponse();
		}
	}
	
	
	public function excluirAction() {
		$intIdEncode = $this->getRequest()->getParam('id');
		$intId = base64_decode($intIdEncode);
		
		$objZendDbTableRowContribuinte = Entity_Contribuinte::getInstance()->find($intId)->current();

		try {
			$objZendDbTableRowContribuinte->delete();
		} catch(Exception $e) {
			if(trim($e->getCode()) == EXCEPTION_FOREIGN_KEY) {
				$arrParams['erro'][] = 'Exclusão negada. Este registro é referenciado por um ou mais registros dependentes.';
				$this->forward('listagem', null, null, $arrParams);
				return;
			}
		}
		
		$this->_helper->FlashMessenger->addMessage('Exclusão realizada com sucesso.');
		$this->getResponse()->setRedirect($this->view->url(array('controller' => 'contribuinte', 'action' => 'index'), null, true))->sendResponse();
	}
	
	
	
	public function uploadLoteAction() {
		$arrParams = $this->getRequest()->getParams();
		
		$objZendSessionNamespaceLogin = new Zend_Session_Namespace('login');
		$objZendDbTableRowMunicipio = $objZendSessionNamespaceLogin->objZendDbTableRowMunicipio;
		
		$this->view->objZendDbTableRowMunicipio = $objZendDbTableRowMunicipio;
		$this->view->arrSucesso = $this->_helper->FlashMessenger->getMessages();
		$this->view->arrParams = $arrParams;
	}
	
	
	public function importarLoteAction() {
		// Valida metodo.
		if(!$this->getRequest()->isPost())
			throw new Zend_Controller_Request_Exception('Acesso não permitido através deste metodo.');
		
		// Recepciona POST e retira os espaços.
		$arrParams = $this->getRequest()->getParams();
		
		// Carrega dependências.
		$objZendSessionNamespaceLogin = new Zend_Session_Namespace('login');
		$objZendDbTableRowMunicipio = $objZendSessionNamespaceLogin->objZendDbTableRowMunicipio;
		$objZendDbTableRowEstado = $objZendSessionNamespaceLogin->objZendDbTableRowEstado;

		$arrTiposPermitidos = array('txt');
		$arrArquivoInfo = pathinfo($_FILES['Filedata']['name']);
		if (!isset($arrArquivoInfo['extension']) || !in_array($arrArquivoInfo['extension'], $arrTiposPermitidos)) {
			$arrParams['erro'][] = 'Extensão do arquivo não permitida.';
			$this->forward('upload-lote', null, null, $arrParams);
			return;
		}

		$strPathDestino = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'prefeituras' . DIRECTORY_SEPARATOR . PREFEITURA . DIRECTORY_SEPARATOR . 'files'. DIRECTORY_SEPARATOR . 'cadastro_estado';
		if(!file_exists($strPathDestino))
			mkdir($strPathDestino);

		// Compõe o nome do arquivo.
		$strNomeArquivoOriginal = $_FILES['Filedata']['name'];
		$strNomeArquivoFormatado = $arrParams['ano_base'] . '_' . Zend_Auth::getInstance()->getIdentity() . '_' . Date('Y-m-d-H-i-s') . '_' . $strNomeArquivoOriginal;
		$strPathDestinoCompleto = $strPathDestino . DIRECTORY_SEPARATOR . $strNomeArquivoFormatado; 
		
		$strPathArquivoTmp = $_FILES['Filedata']['tmp_name'];
		if(!@move_uploaded_file($strPathArquivoTmp, $strPathDestinoCompleto)) {
			$arrParams['erro'][] = 'Erro ao realizar o upload.';
			$this->forward('upload-lote', null, null, $arrParams);
			return;
		}
		
		if(!(boolean) filesize($strPathDestinoCompleto)) {
			$arrParams['erro'][] = 'Arquivo vazio.';
			$this->forward('upload-lote', null, null, $arrParams);
			return;
		}
		
		$objZendDbTableRowLoteEstado = Entity_LoteEstado::getInstance()->createRow();
		$objZendDbTableRowLoteEstado->nome_arquivo = $strNomeArquivoFormatado;
		$objZendDbTableRowLoteEstado->data_inicio = new Zend_Db_Expr("NOW()");
		$objZendDbTableRowLoteEstado->log_data = new Zend_Db_Expr("NOW()");
		$objZendDbTableRowLoteEstado->log_usuario = Zend_Auth::getInstance()->getIdentity();
		$objZendDbTableRowLoteEstado->save();
		
		// $strConteudoArquivo = file_get_contents($strPathDestinoCompleto);
		$rscFile = fopen($strPathDestinoCompleto, 'r');
		$intQtdTotal = 0;
		$intQtdImportados = 0;
		$intQtdNaoImportados = 0;
		while($strLinha = fgets($rscFile)) {
			
			$intQtdTotal++;
			
			try {
				
				$intIE = trim(substr($strLinha, 1 - 1, 12));
				$strRazaoSocial = strtolower(trim(substr($strLinha, 13 - 1, 40)));
				$strLogradouro = strtolower(trim(substr($strLinha, 53 - 1, 4)));
				$strEndereco = strtolower(trim(substr($strLinha, 57 - 1, 20)));
				$strNumero = trim(substr($strLinha, 77 - 1, 5)); // Número foi colocado como string pois o layout assim determina.
				$strConjunto = strtolower(trim(substr($strLinha, 82 - 1, 5)));
				$strBairro = strtolower(trim(substr($strLinha, 87 - 1, 10)));
				$intCep = trim(substr($strLinha, 97 - 1, 5));
				$intComplementoCep = trim(substr($strLinha, 102 - 1, 3));
				$intDataInicioAtividade = trim(substr($strLinha, 105 - 1, 8));
				$intCNPJ = trim(substr($strLinha, 113 - 1, 14));
				$intCNAE = trim(substr($strLinha, 127 - 1, 7));
				$intMunicipio = trim(substr($strLinha, 134 - 1, 3));
				$strRegimeAtual = strtolower(trim(substr($strLinha, 137 - 1, 1)));
				$intDataInicioRegimeAtual = trim(substr($strLinha, 138 - 1, 6));
				$intSituacaoJan = trim(substr($strLinha, 144 - 1, 1));
				$intSituacaoFev = trim(substr($strLinha, 145 - 1, 1));
				$intSituacaoMar = trim(substr($strLinha, 146 - 1, 1));
				$intSituacaoAbr = trim(substr($strLinha, 147 - 1, 1));
				$intSituacaoMai = trim(substr($strLinha, 148 - 1, 1));
				$intSituacaoJun = trim(substr($strLinha, 149 - 1, 1));
				$intSituacaoJul = trim(substr($strLinha, 150 - 1, 1));
				$intSituacaoAgo = trim(substr($strLinha, 151 - 1, 1));
				$intSituacaoSet = trim(substr($strLinha, 152 - 1, 1));
				$intSituacaoOut = trim(substr($strLinha, 153 - 1, 1));
				$intSituacaoNov = trim(substr($strLinha, 154 - 1, 1));
				$intSituacaoDez = trim(substr($strLinha, 155 - 1, 1));

				$objZendDbTableContribuinte = Entity_Contribuinte::getInstance();
				$objZendDbTableRowContribuinte = $objZendDbTableContribuinte->fetchRow(array('inscricao_estadual =?' => $intIE));
				if(isset($objZendDbTableRowContribuinte)) {
					$intQtdNaoImportados++;
					continue;
				}
				
				// Abre transação
				$objZendDbAdapterPDOMysql  = Zend_Registry::get('db');
				$objZendDbAdapterPDOMysql->beginTransaction();
				
				$objZendDbTableRowPessoa = Entity_Pessoa::getInstance()->createRow();
				$objZendDbTableRowPessoa->descri = $strRazaoSocial;
				$objZendDbTableRowPessoa->cpf_cnpj = $intCNPJ;
				$objZendDbTableRowPessoa->cep = $intCep . $intComplementoCep;
				$objZendDbTableRowPessoa->tipo_logradouro = $strLogradouro;
				$objZendDbTableRowPessoa->logradouro = $strEndereco;
				$objZendDbTableRowPessoa->numero = $strNumero;
				$objZendDbTableRowPessoa->complemento = $strConjunto;
				$objZendDbTableRowPessoa->bairro = $strBairro;
				$objZendDbTableRowPessoa->id_municipio = $objZendDbTableRowMunicipio->id;
				$objZendDbTableRowPessoa->municipio = $objZendDbTableRowMunicipio->descri;
				$objZendDbTableRowPessoa->estado = $objZendDbTableRowEstado->sigla;
				$objZendDbTableRowPessoa->email = null;
				$objZendDbTableRowPessoa->log_data = new Zend_Db_Expr("NOW()");
				$objZendDbTableRowPessoa->log_usuario = Zend_Auth::getInstance()->getIdentity();
				$objZendDbTableRowPessoa->save();
				
				$objZendDbTableRowContribuinte = $objZendDbTableContribuinte->createRow();
				$objZendDbTableRowContribuinte->id_pessoa = $objZendDbTableRowPessoa->id;
				$objZendDbTableRowContribuinte->ano_base = $arrParams['ano_base'];
				$objZendDbTableRowContribuinte->id_regime = Entity_Regime::getInstance()->fetchRow(array('codigo_prodesp =?' => $strRegimeAtual))->id;
				$objZendDbTableRowContribuinte->inscricao_estadual = $intIE;
				$objZendDbTableRowContribuinte->cnae = $intCNAE;
				$objZendDbTableRowContribuinte->origem = ORIGEM_LOTE;
				$objZendDbTableRowContribuinte->status = STATUS_PENDENTE;
				$objZendDbTableRowContribuinte->cadastro_complementado = 0;
				$objZendDbTableRowContribuinte->log_data = new Zend_Db_Expr("NOW()");
				$objZendDbTableRowContribuinte->log_usuario = Zend_Auth::getInstance()->getIdentity();
				$objZendDbTableRowContribuinte->save();

				$objZendDbTableRowLoteEstadoContribuinte = Entity_LoteEstadoContribuinte::getInstance()->createRow();
				$objZendDbTableRowLoteEstadoContribuinte->id_lote = $objZendDbTableRowLoteEstado->id;
				$objZendDbTableRowLoteEstadoContribuinte->id_contribuinte = $objZendDbTableRowContribuinte->id;
				$objZendDbTableRowLoteEstadoContribuinte->log_data = new Zend_Db_Expr("NOW()");
				$objZendDbTableRowLoteEstadoContribuinte->log_usuario = Zend_Auth::getInstance()->getIdentity();
				

				$objZendDbTableRowLoteEstadoContribuinte->save();
				
				// Commita transação.
				$objZendDbAdapterPDOMysql->commit();
				$intQtdImportados++;				
			} catch(Exception $e) {
				$objZendDbAdapterPDOMysql->rollBack();
				$intQtdNaoImportados++;
			}

		}
		$objZendDbTableRowLoteEstado->qtd_total = $intQtdTotal;
		$objZendDbTableRowLoteEstado->qtd_importados = $intQtdImportados;
		$objZendDbTableRowLoteEstado->qtd_nao_importados = $intQtdNaoImportados;
		$objZendDbTableRowLoteEstado->data_fim = new Zend_Db_Expr("NOW()");
		$objZendDbTableRowLoteEstado->log_data = new Zend_Db_Expr("NOW()");
		$objZendDbTableRowLoteEstado->save();
		
		$this->_helper->FlashMessenger->addMessage(
			'Importação concluída. <br> ' . 
			$intQtdImportados . ' contribuintes importados. <br> ' . 
			$intQtdNaoImportados . ' contribuintes não importados.');
		$this->getResponse()->setRedirect($this->view->url(array('controller' => 'contribuinte', 'action' => 'upload-lote'), null, true))->sendResponse();
	}
	
	
}
