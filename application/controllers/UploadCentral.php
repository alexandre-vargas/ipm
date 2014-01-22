<?php

/**
 * 
 * Controller para upload e importação de GIA para o MySql
 * @author sigcorp - Felipe Santiago
 *
 */

class UploadCentral extends Zend_Controller_Action {
		
	protected $objImportacaoControleModel;
	protected $intIdControle;
	protected $objZendConfig;
	
	
	/**
	 * (non-PHPdoc)
	 * @see Zend_Controller_Action::init()
	 */
	public function init() {		
		$this->objImportacaoControleModel = Entity_ImportacaoCentral::getInstance();
		$this->objZendConfig = Zend_Registry::get('config');
	}
	
	/**
	 * 
	 * Formulário de envio de arquivo
	 */
	public function indexAction() {
	}
	
	/**
	 * 
	 * Método para realizar o upload do arquivo de GIA
	 */
	public function salvarAction() {	

		$strDirFile = $this->objZendConfig->batch->mysql->dir_processamento;
				
		$request = $this->getRequest();	
		if(!$request->isPost()) 
			return ;
			
		try	{
			$adapter = new Zend_File_Transfer_Adapter_Http();
			$adapter->addValidator('Count',false, array('min'=>1, 'max'=>3))
					->addValidator('Size',false,array('max' => 10000000))
					->addValidator('Extension',false,array('extension' => 'mdb','case' => true));
				
			$adapter->setDestination($strDirFile);

			$file = $adapter->getFileInfo();
			$strNameFile = $file['uploadedfile']['name'];
					
			if(($adapter->isUploaded($strNameFile))&& ($adapter->isValid($strNameFile))) {
				$adapter->receive($strNameFile);

				$arrNameFile = explode('.mdb', $strNameFile);
				$strUniqID = uniqid(); 
				$strUniNameFile = "{$arrNameFile[0]}_{$strUniqID}.mdb";
				
				rename("{$strDirFile}{$strNameFile}", "{$strDirFile}{$strUniNameFile}");

				$this->objImportacaoControleModel->registraDiretorioArquivoControleImportacao($strDirFile, $strUniNameFile);

				$this->intIdControle = $this->objImportacaoControleModel->getLastInsertID();

				$this->objImportacaoControleModel->insereHistorico($this->intIdControle, Entity_ImportacaoCentral::$intNumOrdem, 'PROC_IMP_MYSQL');
				Entity_ImportacaoCentral::$intNumOrdem++;
					
				$this->intIdControleStatus = $this->objImportacaoControleModel->getLastInsertID();

				$this->objImportacaoControleModel->atualizaHistorico($this->intIdControleStatus );
				
				if(!$this->exportaCSV($strDirFile, $strUniNameFile, $strUniqID, $this->intIdControle))
					return ;

				if(!$this->objImportacaoControleModel->importaArquivosMySql($strDirFile, $strUniNameFile, $strUniqID, $this->intIdControle))
					return ;	
	
				$this->_redirect('upload/index?msg=Upload Realizado com Sucesso!&erro=0');
			}		
			$this->_redirect('upload/index?msg=Arquivo Invalido!&erro=1');
		} catch (Exception $ex) {
			$strErro = print_r($ex, true);
			$this->objImportacaoControleModel->atualizaHistorico($this->intIdControleStatus, $strErro);
		}
	}
	
	/**
	 * 
	 * Método para ler o arquivo de GIA (.mdb) e gerar arquivos (.csv) 
	 * @param String $strDirFile - diretório dos arquivos a serem importados 
	 * @param String $strFileName - nome do arquivo a origem (MDB)
	 * @param String $strUniqID - hash único do nome do arquivo 
	 */
	private function exportaCSV($strDirFile, $strFileName, $strUniqID = '', $idControle) {			
		$this->objImportacaoControleModel->insereHistorico($idControle, Entity_ImportacaoCentral::$intNumOrdem, 'PROC_IMP_MYSQL');
		$intIdHistoricoExportacao = $this->objImportacaoControleModel->getLastInsertID();		
		Entity_ImportacaoCentral::$intNumOrdem++;
		
		$boolCommandExport = true;
		
		$this->objImportacaoControleModel->insereHistorico($idControle, Entity_ImportacaoCentral::$intNumOrdem, 'PROC_IMP_MYSQL');
		$intIdHistorico = $this->objImportacaoControleModel->getLastInsertID();		
		Entity_ImportacaoCentral::$intNumOrdem++;
		
		if (!is_null(shell_exec("mdb-export {$strDirFile}{$strFileName} tblContribuinte > {$strDirFile}contribuinte_{$strUniqID}.csv")))
			$boolCommandExport = false;
		$this->objImportacaoControleModel->atualizaHistorico($intIdHistorico);
		
		$this->objImportacaoControleModel->insereHistorico($idControle, Entity_ImportacaoCentral::$intNumOrdem, 'PROC_IMP_MYSQL');
		$intIdHistorico = $this->objImportacaoControleModel->getLastInsertID();		
		Entity_ImportacaoCentral::$intNumOrdem++;
		
		if (!is_null(shell_exec("mdb-export {$strDirFile}{$strFileName} tblDetalhesCFOPs > {$strDirFile}detalhes_cfops_{$strUniqID}.csv")))
			$boolCommandExport = false;
		$this->objImportacaoControleModel->atualizaHistorico($intIdHistorico);

		$this->objImportacaoControleModel->insereHistorico($idControle, Entity_ImportacaoCentral::$intNumOrdem, 'PROC_IMP_MYSQL');
		$intIdHistorico = $this->objImportacaoControleModel->getLastInsertID();		
		Entity_ImportacaoCentral::$intNumOrdem++;
		
		if (!is_null(shell_exec("mdb-export {$strDirFile}{$strFileName} tblDetalhesInterUFs > {$strDirFile}detalhes_inter_ufs_{$strUniqID}.csv")))
			$boolCommandExport = false;
		$this->objImportacaoControleModel->atualizaHistorico($intIdHistorico);

		$this->objImportacaoControleModel->insereHistorico($idControle, Entity_ImportacaoCentral::$intNumOrdem, 'PROC_IMP_MYSQL');
		$intIdHistorico = $this->objImportacaoControleModel->getLastInsertID();
		Entity_ImportacaoCentral::$intNumOrdem++;
		
		if (!is_null(shell_exec("mdb-export {$strDirFile}{$strFileName} tblGIA > {$strDirFile}gia_{$strUniqID}.csv")))
			$boolCommandExport = false;	
		$this->objImportacaoControleModel->atualizaHistorico($intIdHistorico);
		
		$this->objImportacaoControleModel->insereHistorico($idControle, Entity_ImportacaoCentral::$intNumOrdem, 'PROC_IMP_MYSQL');
		$intIdHistorico = $this->objImportacaoControleModel->getLastInsertID();
		Entity_ImportacaoCentral::$intNumOrdem++;
		
		if (!is_null(shell_exec("mdb-export {$strDirFile}{$strFileName} tblIEsRemetente > {$strDirFile}ies_remetente_{$strUniqID}.csv")))
			$boolCommandExport = false;		
		$this->objImportacaoControleModel->atualizaHistorico($intIdHistorico);
		
		$this->objImportacaoControleModel->insereHistorico($idControle, Entity_ImportacaoCentral::$intNumOrdem, 'PROC_IMP_MYSQL');
		$intIdHistorico = $this->objImportacaoControleModel->getLastInsertID();
		Entity_ImportacaoCentral::$intNumOrdem++;
		
		if (!is_null(shell_exec("mdb-export {$strDirFile}{$strFileName} tblOcorrências > {$strDirFile}ocorrencias_{$strUniqID}.csv")))
			$boolCommandExport = false;				
		$this->objImportacaoControleModel->atualizaHistorico($intIdHistorico);

		$this->objImportacaoControleModel->insereHistorico($idControle, Entity_ImportacaoCentral::$intNumOrdem, 'PROC_IMP_MYSQL');
		$intIdHistorico = $this->objImportacaoControleModel->getLastInsertID();
		Entity_ImportacaoCentral::$intNumOrdem++;
		
		if (!is_null(shell_exec("mdb-export {$strDirFile}{$strFileName} tblVersão > {$strDirFile}versao_{$strUniqID}.csv")))
			$boolCommandExport = false;		
		$this->objImportacaoControleModel->atualizaHistorico($intIdHistorico);
			
		if(!$boolCommandExport) 
			return ;
			
		$this->objImportacaoControleModel->atualizaHistorico($intIdHistoricoExportacao);
		
		return $boolCommandExport;		
		//then, store links etc in db for retrieval later..		
	}
	
	
}