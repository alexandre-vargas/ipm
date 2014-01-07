<?php
/**
 * 
 * Model genérica: rotinas de upload e importação
 * @author Felipe Santiago
 *
 */
class Entity_ImportacaoCentral extends Zend_Db_Table_Abstract {
	
	protected static $instance = null;
	protected $objZendDbAdapterPdoMysql ;
	public static $intNumOrdem;
	
	CONST EM_PROCESSAMENTO = 1;
	CONST PROCESSADO_OK = 2;
	CONST PROCESSADO_ERRO = 3;
		
	/**
	 * Implementação do método singleton para obter a instancia da classe
	 * 
	 * @return Entity_Usuario
	 */
	public static function getInstance()
	{
		if(null == self::$instance) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * 
	 * Construtor
	 */
	public function __construct() {
		$this->objZendDbAdapterPdoMysql = Zend_Registry::get('db');
		self::$intNumOrdem = 1;
	}
	
	
	/**
	 * 
	 * Método para impotação de arquivos csv para o MYSQL
	 * @param String $strDirFile - diretório dos arquivos a serem importados 
	 * @param String $strFileName - nome do arquivo a origem (MDB)
	 * @param String $strUniqID - hash único do nome do arquivo 
	 */
	public function importaArquivosMySql($strDirFile, $strFileName, $strUniqID = '', $intIdControle) {					
		$this->insereHistorico($intIdControle, Entity_ImportacaoCentral::$intNumOrdem, 'PROC_IMP_MYSQL');
		$intIdHistoricoImportacao = $this->getLastInsertID();
		Entity_ImportacaoCentral::$intNumOrdem++;
		
		try{				
			$this->objZendDbAdapterPdoMysql->query("DROP TABLE IF EXISTS `tmp_contribuinte`;
			DROP TABLE IF EXISTS `tmp_detalhes_cfops`;
			DROP TABLE IF EXISTS `tmp_detalhes_inter_ufs`;
			DROP TABLE IF EXISTS `tmp_gia`;
			DROP TABLE IF EXISTS `tmp_ies_remetente`;
			DROP TABLE IF EXISTS `tmp_ocorrencias`;
			DROP TABLE IF EXISTS `tmp_versao`;");
			
			$arrResult['importacao_gia_contribuinte'][] = $this->objZendDbAdapterPdoMysql->query("CREATE TEMPORARY TABLE `tmp_contribuinte` (
			  `inscricao_estadual` varchar(15) NOT NULL,
			  `razao_social` varchar(255) DEFAULT NULL,
			  `cnpj` varchar(30) NOT NULL,
			  `cnae` varchar(20) DEFAULT NULL,
			  `localizacao` varchar(100) DEFAULT NULL,
			  `numero` varchar(10) DEFAULT NULL,
			  `complemento` varchar(50) DEFAULT NULL,
			  `bairro` varchar(50) DEFAULT NULL,
			  `cep` varchar(20) DEFAULT NULL,
			  `municipio` varchar(50) DEFAULT NULL,
			  `uf` varchar(2) DEFAULT NULL,
			  `telefone` varchar(20) DEFAULT NULL,
			  `homepage` varchar(100) DEFAULT NULL,
			  `fax` varchar(20) DEFAULT NULL,
			  `contato` varchar(50) DEFAULT NULL,
			  `datacadastro` datetime DEFAULT NULL,
			  `email` longtext,
			  `observacao` longtext,
			  PRIMARY KEY (`inscricao_estadual`)
			)");		
			
			$arrResult['importacao_gia_detalhes_cfops'][] = $this->objZendDbAdapterPdoMysql->query("CREATE TEMPORARY TABLE `tmp_detalhes_cfops` (
			  `nro_gia` int(11) DEFAULT NULL,
			  `cfop` varchar(10) DEFAULT NULL,
			  `valor_contabil` decimal(19,4) DEFAULT NULL,
			  `base_calculo` decimal(19,4) DEFAULT NULL,
			  `imposto` decimal(19,4) DEFAULT NULL,
			  `isentas_nao_trib` decimal(19,4) DEFAULT NULL,
			  `outras` decimal(19,4) DEFAULT NULL,
			  `imposto_retido_st` decimal(19,4) DEFAULT NULL,
			  `imp_ret_substituto_st` decimal(19,4) DEFAULT NULL,
			  `imp_ret_substituido` decimal(19,4) DEFAULT NULL,
			  `outros_impostos` decimal(19,4) DEFAULT NULL, 
			  PRIMARY KEY (`nro_gia`,`cfop`)
			);");
			
			$arrResult['importacao_gia_detalhes_inter_ufs'][] = $this->objZendDbAdapterPdoMysql->query("CREATE TEMPORARY TABLE `tmp_detalhes_inter_ufs` (
			  `nro_gia` int(11) DEFAULT NULL,
			  `cfop` varchar(10) DEFAULT NULL,
			  `uf` varchar(2) DEFAULT NULL,
			  `valor_contabil_1` decimal(19,4) DEFAULT NULL,
			  `base_calculo_1` decimal(19,4) DEFAULT NULL,
			  `valor_contabil_2` decimal(19,4) DEFAULT NULL,
			  `base_calculo_2` decimal(19,4) DEFAULT NULL,
			  `imposto` decimal(19,4) DEFAULT NULL,
			  `outras` decimal(19,4) DEFAULT NULL,
			  `icms_cobrado_st` decimal(19,4) DEFAULT NULL,
			  `petroleo_energia` decimal(19,4) DEFAULT NULL,
			  `outros_produtos` decimal(19,4) DEFAULT NULL,
			  PRIMARY KEY (`nro_gia`,`cfop`,`uf`)
			);");
			
			$arrResult['importacao_gia'][] = $this->objZendDbAdapterPdoMysql->query("CREATE TEMPORARY TABLE `tmp_gia` (
			  `nro_gia` int(11) NOT NULL AUTO_INCREMENT,
			  `ie` varchar(15) DEFAULT NULL,
			  `tipo` tinyint(4) DEFAULT NULL,
			  `reg_trib` varchar(2) DEFAULT NULL,
			  `ref1` varchar(80) DEFAULT NULL,
			  `ref2` varchar(80) DEFAULT NULL,
			  `movimento` tinyint(1) DEFAULT '0',
			  `saldo_credor` decimal(19,4) DEFAULT NULL,
			  `saldo_cred_ant_dig` decimal(19,4) DEFAULT NULL,
			  `transmitida` varchar(1) DEFAULT NULL,
			  `deducoes_rpa` decimal(19,4) DEFAULT NULL,
			  `icms_fix_per_res` decimal(19,4) DEFAULT NULL,
			  `outras_res` decimal(19,4) DEFAULT NULL,
			  `saldo_credor_st` decimal(19,4) DEFAULT NULL,
			  `saldo_cred_ant_dig_st` decimal(19,4) DEFAULT NULL,
			  `deducoes_st` decimal(19,4) DEFAULT NULL,
			  `origem` varchar(15) DEFAULT NULL,
			  `origem_pref_dig` varchar(1) DEFAULT NULL,
			  `consistente` tinyint(1) DEFAULT '0',
			  `data_transmissao` varchar(80) DEFAULT NULL,
			  `data_geracao_substitutiva` datetime DEFAULT NULL,
			  `data_geracao_coligida` datetime DEFAULT NULL,
			  `autenticacao` varchar(32) DEFAULT NULL,
			  `chave_interna` varchar(32) DEFAULT NULL,
			  PRIMARY KEY (`nro_gia`)
			);");
			
			$arrResult['importacao_gia_ies_remetente'][] = $this->objZendDbAdapterPdoMysql->query("CREATE TEMPORARY TABLE `tmp_ies_remetente` (
				`codigo` int(11) NOT NULL,
				`ie_remetente` varchar(15) NOT NULL,
				`valor` decimal(19,4) DEFAULT NULL,
				PRIMARY KEY (`codigo`,`ie_remetente`)
			);");
			
			$arrResult['importacao_gia_ocorrencias'][] = $this->objZendDbAdapterPdoMysql->query("CREATE TEMPORARY TABLE `tmp_ocorrencias` (
				`nro_gia` int(11) DEFAULT NULL,
				`codigo` int(11) NOT NULL AUTO_INCREMENT,
				`cod_sub_item` varchar(10) DEFAULT NULL,
				`valor` decimal(19,4) DEFAULT NULL,
				`f_legal` longtext,
				`ocorrencia` longtext,
				`op_proprias` tinyint(1) DEFAULT '0',
				PRIMARY KEY (`codigo`)
			);");
			
			$arrResult['importacao_gia_versao'][] = $this->objZendDbAdapterPdoMysql->query("CREATE TEMPORARY TABLE `tmp_versao` (
			    `versao` varchar(15) NOT NULL,
			    `observacao` varchar(255) DEFAULT NULL,
			    PRIMARY KEY (`versao`)
			);");
			
			
			$this->objZendDbAdapterPdoMysql->beginTransaction();							
		
			// inicio rotina de importação dos contribuintes
			$this->insereHistorico($intIdControle, Entity_ImportacaoCentral::$intNumOrdem, 'PROC_IMP_MYSQL');
			$intIdHistorico = $this->getLastInsertID();
			Entity_ImportacaoCentral::$intNumOrdem++;
			$arrResult['importacao_gia_contribuinte'][] = $this->objZendDbAdapterPdoMysql->query("LOAD DATA INFILE '{$strDirFile}contribuinte_{$strUniqID}.csv' INTO TABLE `tmp_contribuinte` FIELDS TERMINATED BY ',' ENCLOSED BY '\"' IGNORE 1 LINES;");
						
			//  - rotina para criação de diretórios por contribuinte para cada prefeitura
			$objContribuinte = $this->objZendDbAdapterPdoMysql->query("SELECT inscricao_estadual FROM tmp_contribuinte");			
			$objResultContribuinte = $objContribuinte->fetchAll();				
			$strInscricaoEstadual = Filtro::removeMascara($objResultContribuinte[0]['inscricao_estadual']);
	
			if(!empty($strInscricaoEstadual)) {
				if(!is_dir($strDirFile.$strInscricaoEstadual))
					mkdir("{$strDirFile}{$strInscricaoEstadual}");
				
				rename("{$strDirFile}{$strFileName}", "{$strDirFile}{$strInscricaoEstadual}/{$strFileName}");		
				unlink("{$strDirFile}{$strFileName}");			
			}
			// fim da rotina para criação de diretórios por contribuinte para cada prefeitura
			
			$arrResult['importacao_gia_contribuinte'][] = $this->objZendDbAdapterPdoMysql->query("INSERT INTO `importacao_gia_contribuinte`
			(`importacao_gia_contribuinte`.`inscricao_estadual`,
			 `importacao_gia_contribuinte`.`razao_social`,
			 `importacao_gia_contribuinte`.`cnpj`,
			 `importacao_gia_contribuinte`.`cnae`,
			 `importacao_gia_contribuinte`.`localizacao`,
			 `importacao_gia_contribuinte`.`numero`,
			 `importacao_gia_contribuinte`.`complemento`,
			 `importacao_gia_contribuinte`.`bairro`,
			 `importacao_gia_contribuinte`.`cep`,
			 `importacao_gia_contribuinte`.`municipio`,
			 `importacao_gia_contribuinte`.`uf`,
			 `importacao_gia_contribuinte`.`telefone`,
			 `importacao_gia_contribuinte`.`homepage`,
			 `importacao_gia_contribuinte`.`fax`,
			 `importacao_gia_contribuinte`.`contato`,
			 `importacao_gia_contribuinte`.`datacadastro`,
			 `importacao_gia_contribuinte`.`email`,
			 `importacao_gia_contribuinte`.`observacao`,
			 `importacao_gia_contribuinte`.`data_insert`,
			 `importacao_gia_contribuinte`.`id_importacao_controle`)
				SELECT
					`tmp_contribuinte`.`inscricao_estadual`,
					`tmp_contribuinte`.`razao_social`,
					`tmp_contribuinte`.`cnpj`,
					`tmp_contribuinte`.`cnae`,
					`tmp_contribuinte`.`localizacao`,
					`tmp_contribuinte`.`numero`,
					`tmp_contribuinte`.`complemento`,
					`tmp_contribuinte`.`bairro`,
					`tmp_contribuinte`.`cep`,
					`tmp_contribuinte`.`municipio`,
					`tmp_contribuinte`.`uf`,
					`tmp_contribuinte`.`telefone`,
					`tmp_contribuinte`.`homepage`,
					`tmp_contribuinte`.`fax`,
					`tmp_contribuinte`.`contato`,
					`tmp_contribuinte`.`datacadastro`,
					`tmp_contribuinte`.`email`,
					`tmp_contribuinte`.`observacao`,
					now(),
					{$intIdControle}
				FROM `tmp_contribuinte`;");
					
			$this->atualizaHistorico($intIdHistorico);
			// fim da rotina de importação dos contribuintes
	
			//inicio da rotina de importação dos detalhes cfops
			$this->insereHistorico($intIdControle, Entity_ImportacaoCentral::$intNumOrdem, 'PROC_IMP_MYSQL');
			$intIdHistorico = $this->getLastInsertID();
			Entity_ImportacaoCentral::$intNumOrdem++;
			
			$arrResult['importacao_gia_detalhes_cfops'][] = $this->objZendDbAdapterPdoMysql->query("LOAD DATA INFILE '{$strDirFile}detalhes_cfops_{$strUniqID}.csv' INTO TABLE `tmp_detalhes_cfops` FIELDS TERMINATED BY ',' ENCLOSED BY '\"' IGNORE 1 LINES;");
					
			$arrResult['importacao_gia_detalhes_cfops'][] = $this->objZendDbAdapterPdoMysql->query("INSERT INTO `importacao_gia_detalhes_cfops`
			(`nro_gia`,
			`cfop`,
			`valor_contabil`,
			`base_calculo`,
			`imposto`,
			`isentas_nao_trib`,
			`outras`,
			`imposto_retido_st`,
			`imp_ret_substituto_st`,
			`imp_ret_substituido`,
			`outros_impostos`,
			`data_insert`,
			`id_importacao_controle`)
				SELECT
					`tmp_detalhes_cfops`.`nro_gia`,
					`tmp_detalhes_cfops`.`cfop`,
					`tmp_detalhes_cfops`.`valor_contabil`,
					`tmp_detalhes_cfops`.`base_calculo`,
					`tmp_detalhes_cfops`.`imposto`,
					`tmp_detalhes_cfops`.`isentas_nao_trib`,
					`tmp_detalhes_cfops`.`outras`,
					`tmp_detalhes_cfops`.`imposto_retido_st`,
					`tmp_detalhes_cfops`.`imp_ret_substituto_st`,
					`tmp_detalhes_cfops`.`imp_ret_substituido`,
					`tmp_detalhes_cfops`.`outros_impostos`,
					NOW(),
					{$intIdControle}
				FROM `tmp_detalhes_cfops`;");
					
			$this->atualizaHistorico($intIdHistorico);
			
			// fim da rotina de importação dos detalhes cfops
			
			// inicia da rotina de importação dos detalhes inter ufs
			$this->insereHistorico($intIdControle, Entity_ImportacaoCentral::$intNumOrdem, 'PROC_IMP_MYSQL');
			$intIdHistorico = $this->getLastInsertID();
			Entity_ImportacaoCentral::$intNumOrdem++;		
			
			$arrResult['importacao_gia_detalhes_inter_ufs'][] = $this->objZendDbAdapterPdoMysql->query("LOAD DATA INFILE '{$strDirFile}detalhes_inter_ufs_{$strUniqID}.csv' INTO TABLE tmp_detalhes_inter_ufs FIELDS TERMINATED BY ',' ENCLOSED BY '\"' IGNORE 1 LINES;");
		
			$arrResult['importacao_gia_detalhes_inter_ufs'][] = $this->objZendDbAdapterPdoMysql->query("INSERT INTO `importacao_gia_detalhes_inter_ufs`
			(`nro_gia`,
			`cfop`,
			`uf`,
			`valor_contabil_1`,
			`base_calculo_1`,
			`valor_contabil_2`,
			`base_calculo_2`,
			`imposto`,
			`outras`,
			`icms_cobrado_st`,
			`petroleo_energia`,
			`outros_produtos`,
			`data_insert`,
			`id_importacao_controle`)
				SELECT
					`tmp_detalhes_inter_ufs`.`nro_gia`,
					`tmp_detalhes_inter_ufs`.`cfop`,
					`tmp_detalhes_inter_ufs`.`uf`,
					`tmp_detalhes_inter_ufs`.`valor_contabil_1`,
					`tmp_detalhes_inter_ufs`.`base_calculo_1`,
					`tmp_detalhes_inter_ufs`.`valor_contabil_2`,
					`tmp_detalhes_inter_ufs`.`base_calculo_2`,
					`tmp_detalhes_inter_ufs`.`imposto`,
					`tmp_detalhes_inter_ufs`.`outras`,
					`tmp_detalhes_inter_ufs`.`icms_cobrado_st`,
					`tmp_detalhes_inter_ufs`.`petroleo_energia`,
					`tmp_detalhes_inter_ufs`.`outros_produtos`,
					NOW(),
					{$intIdControle}
				FROM `tmp_detalhes_inter_ufs`;");
					
			$this->atualizaHistorico($intIdHistorico);
			
			// fim da rotina de importação dos detalhes inter ufs			
	
			// inicio da rotina de importação das gias
			$this->insereHistorico($intIdControle, Entity_ImportacaoCentral::$intNumOrdem, 'PROC_IMP_MYSQL');
			$intIdHistorico = $this->getLastInsertID();
			Entity_ImportacaoCentral::$intNumOrdem++;
			
			$arrResult['importacao_gia'][] = $this->objZendDbAdapterPdoMysql->query("LOAD DATA INFILE '{$strDirFile}gia_{$strUniqID}.csv' INTO TABLE `tmp_gia` FIELDS TERMINATED BY ',' ENCLOSED BY '\"' IGNORE 1 LINES;");
		
			$arrResult['importacao_gia'][] = $this->objZendDbAdapterPdoMysql->query("INSERT INTO `importacao_gia_gia`
			(`nro_gia`,
			`ie`,
			`tipo`,
			`reg_trib`,
			`ref1`,
			`ref2`,
			`movimento`,
			`saldo_credor`,
			`saldo_cred_ant_dig`,
			`transmitida`,
			`deducoes_rpa`,
			`icms_fix_per_res`,
			`outras_res`,
			`saldo_credor_st`,
			`saldo_cred_ant_dig_st`,
			`deducoes_st`,
			`origem`,
			`origem_pref_dig`,
			`consistente`,
			`data_transmissao`,
			`data_geracao_substitutiva`,
			`data_geracao_coligida`,
			`autenticacao`,
			`chave_interna`,
			`data_insert`,
			`id_importacao_controle`)
				SELECT
					`tmp_gia`.`nro_gia`,
					`tmp_gia`.`ie`,
					`tmp_gia`.`tipo`,
					`tmp_gia`.`reg_trib`,
					`tmp_gia`.`ref1`,
					`tmp_gia`.`ref2`,
					`tmp_gia`.`movimento`,
					`tmp_gia`.`saldo_credor`,
					`tmp_gia`.`saldo_cred_ant_dig`,
					`tmp_gia`.`transmitida`,
					`tmp_gia`.`deducoes_rpa`,
					`tmp_gia`.`icms_fix_per_res`,
					`tmp_gia`.`outras_res`,
					`tmp_gia`.`saldo_credor_st`,
					`tmp_gia`.`saldo_cred_ant_dig_st`,
					`tmp_gia`.`deducoes_st`,
					`tmp_gia`.`origem`,
					`tmp_gia`.`origem_pref_dig`,
					`tmp_gia`.`consistente`,
					`tmp_gia`.`data_transmissao`,
					`tmp_gia`.`data_geracao_substitutiva`,
					`tmp_gia`.`data_geracao_coligida`,
					`tmp_gia`.`autenticacao`,
					`tmp_gia`.`chave_interna`,
					NOW(),
					{$intIdControle}
				FROM `tmp_gia`;");
					
			$this->atualizaHistorico($intIdHistorico);
			
			// fim da rotina de importação das gias

			// inicio da rotina de importação dos ies remetentes
			$this->insereHistorico($intIdControle, Entity_ImportacaoCentral::$intNumOrdem, 'PROC_IMP_MYSQL');
			$intIdHistorico = $this->getLastInsertID();
			Entity_ImportacaoCentral::$intNumOrdem++;	
					
			$arrResult['importacao_gia_ies_remetente'][] = $this->objZendDbAdapterPdoMysql->query("LOAD DATA INFILE '{$strDirFile}ies_remetente_{$strUniqID}.csv' INTO TABLE tmp_ies_remetente FIELDS TERMINATED BY ',' ENCLOSED BY '\"' IGNORE 1 LINES;");
					
			$arrResult['importacao_gia_ies_remetente'][] = $this->objZendDbAdapterPdoMysql->query("INSERT INTO `importacao_gia_ies_remetente`
			(`codigo`,
			`ie_remetente`,
			`valor`,
			`data_insert`,
			`id_importacao_controle`)
				SELECT
					`tmp_ies_remetente`.`codigo`,
					`tmp_ies_remetente`.`ie_remetente`,
					`tmp_ies_remetente`.`valor`,
					NOW(),
					{$intIdControle}
				FROM `tmp_ies_remetente`;");
					
			$this->atualizaHistorico($intIdHistorico);
			
			// fim da rotina de importação dos ies remetentes

			// inicio da rotina de importação de ocorrências
			$this->insereHistorico($intIdControle, Entity_ImportacaoCentral::$intNumOrdem, 'PROC_IMP_MYSQL');
			$intIdHistorico = $this->getLastInsertID();
			Entity_ImportacaoCentral::$intNumOrdem++;		
				
			$arrResult['importacao_gia_ocorrencias'][] = $this->objZendDbAdapterPdoMysql->query("LOAD DATA INFILE '{$strDirFile}ocorrencias_{$strUniqID}.csv' INTO TABLE `tmp_ocorrencias` FIELDS TERMINATED BY ',' ENCLOSED BY '\"' IGNORE 1 LINES;");
					
			$arrResult['importacao_gia_ocorrencias'][] = $this->objZendDbAdapterPdoMysql->query("INSERT INTO `importacao_gia_ocorrencias`
			(`nro_gia`,
			`codigo`,
			`cod_sub_item`,
			`valor`,
			`f_legal`,
			`ocorrencia`,
			`op_proprias`,
			`data_insert`,
			`id_importacao_controle`)
				SELECT
					`tmp_ocorrencias`.`nro_gia`,
					`tmp_ocorrencias`.`codigo`,
					`tmp_ocorrencias`.`cod_sub_item`,
					`tmp_ocorrencias`.`valor`,
					`tmp_ocorrencias`.`f_legal`,
					`tmp_ocorrencias`.`ocorrencia`,
					`tmp_ocorrencias`.`op_proprias`,
					NOW(),
					{$intIdControle}
				FROM `tmp_ocorrencias`;");			
					
			$this->atualizaHistorico($intIdHistorico);
			
			// fim da rotina de importação de ocorrências
	
			// inicio da rotina de importação de versão	da gia		
			$this->insereHistorico($intIdControle, Entity_ImportacaoCentral::$intNumOrdem, 'PROC_IMP_MYSQL');
			$intIdHistorico = $this->getLastInsertID();
			Entity_ImportacaoCentral::$intNumOrdem++;		
				
			$arrResult['importacao_gia_versao'][] = $this->objZendDbAdapterPdoMysql->query("LOAD DATA INFILE '{$strDirFile}versao_{$strUniqID}.csv' INTO TABLE `tmp_versao` FIELDS TERMINATED BY ',' ENCLOSED BY '\"' IGNORE 1 LINES;");
										
			$arrResult['importacao_gia_versao'][] = $this->objZendDbAdapterPdoMysql->query("INSERT INTO `importacao_gia_versao`
			(`versao`,
			`observacao`,
			`data_insert`,
			`id_importacao_controle`)			
				SELECT
					`tmp_versao`.`versao`,
					`tmp_versao`.`observacao`,
					NOW(),
					{$intIdControle}
				FROM `tmp_versao`;");
					
			$this->atualizaHistorico($intIdHistorico);
			
			// fim da rotina de importação de versão da gia
		
			$this->objZendDbAdapterPdoMysql->commit();
		
			$this->atualizaControleImportacao($intIdControle, self::PROCESSADO_OK);
			$return = true;
		} catch (Exception $e) {	
			$this->objZendDbAdapterPdoMysql->rollBack();								
			$strErro = print_r($e, true);
			$this->atualizaHistorico($intIdControle, $strErro);
			
			$this->atualizaControleImportacao($intIdControle, self::PROCESSADO_ERRO);
			$return = false;
		}	
		
		$arrResult['importacao_gia_contribuinte'][] = $this->objZendDbAdapterPdoMysql->query("DROP TABLE IF EXISTS `tmp_contribuinte`;");
		@unlink("{$strDirFile}contribuinte_{$strUniqID}.csv");
		
		$arrResult['importacao_gia_detalhes_cfops'][] = $this->objZendDbAdapterPdoMysql->query("DROP TABLE IF EXISTS `tmp_detalhes_cfops`;");
		@unlink("{$strDirFile}detalhes_cfops_{$strUniqID}.csv");
		
		$arrResult['importacao_gia_detalhes_inter_ufs'][] = $this->objZendDbAdapterPdoMysql->query("DROP TABLE IF EXISTS `tmp_detalhes_inter_ufs`;");
		@unlink("{$strDirFile}detalhes_inter_ufs_{$strUniqID}.csv");
		
		$arrResult['importacao_gia'][] = $this->objZendDbAdapterPdoMysql->query("DROP TABLE IF EXISTS `tmp_gia`;");
		@unlink("{$strDirFile}gia_{$strUniqID}.csv");
		
		$arrResult['importacao_gia_ies_remetente'][] = $this->objZendDbAdapterPdoMysql->query("DROP TABLE IF EXISTS `tmp_ies_remetente`;");
		@unlink("{$strDirFile}ies_remetente_{$strUniqID}.csv");
		
		$arrResult['importacao_gia_ocorrencias'][] = $this->objZendDbAdapterPdoMysql->query("DROP TABLE IF EXISTS `tmp_ocorrencias`;");
		@unlink("{$strDirFile}ocorrencias_{$strUniqID}.csv");
		
		$arrResult['importacao_gia_versao'][] = $this->objZendDbAdapterPdoMysql->query("DROP TABLE IF EXISTS `tmp_versao`;");	
		@unlink("{$strDirFile}versao_{$strUniqID}.csv");	
		
		$this->atualizaHistorico($intIdHistoricoImportacao);
		return $return;
		
	}	
	
	/**
	 * 
	 * Insere nome arquivo e diretório na tabela de controle de importação
	 * @param unknown_type $strDirFile
	 * @param unknown_type $strUniNameFile
	 * @param unknown_type $intStatus
	 */
	public function registraDiretorioArquivoControleImportacao($strDirFile, $strUniNameFile, $intStatus = self::EM_PROCESSAMENTO) {	
		return $this->objZendDbAdapterPdoMysql->query("INSERT INTO `importacao_controle`
			(`dir_arquivo`,
			`dsc_arquivo`,
			`data_importacao`,
			`id_status`,
			`data_inicio`)
		VALUES (
			'{$strDirFile}',
			'{$strUniNameFile}',
			NOW(),
			{$intStatus},
			NOW()
		);");
		
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getLastInsertID() {
		$objDbStatement = $this->objZendDbAdapterPdoMysql->query("SELECT last_insert_id() AS id;");		
		$objDbResult = $objDbStatement->fetchAll();				
		return $objDbResult[0]['id'];
	}
	
	/**
	 * 
	 * Insere status no histórico da aplicação
	 * @param int $intIdControle
	 * @param int $intNumOrdem - numero ordem status processo
	 * @param int $intStatus  - id de status do processo
	 */
	public function insereHistorico($intIdControle, $intNumOrdem, $strIdProcessoUnico, $intStatus = 0){
		$sql = 's.id';
		if($intStatus)
			$sql = $intStatus;
			
		return $this->objZendDbAdapterPdoMysql->query("INSERT INTO `importacao_controle_status` (`importacao_status_id`, `importacao_controle_id`, `dsc_usuario`, `data_inicio`)
			SELECT
				{$sql},
				{$intIdControle},				
				'SYSTEM',
				NOW()
			FROM importacao_status s 
			INNER JOIN importacao_processo p ON p.id = s.importacao_processo_id
			WHERE s.num_ordem = {$intNumOrdem} 
			AND p.id_processo_unico = '{$strIdProcessoUnico}';");		
	}
	
	/**
	 * 
	 * Atualiza a data_fim processo
	 * @param int $id - ID da tabela importacao_controle_status
	 * @param String $strErro - descricao erro 
	 */
	public function atualizaHistorico($id, $strErro = '') {
	
		return $this->objZendDbAdapterPdoMysql->query("UPDATE `importacao_controle_status` SET 	
		`data_fim` = NOW(),
		`dsc_observacao` = '{$strErro}'
		WHERE `id` = {$id}");		
		
		//return $this->objZendDbAdapterPdoMysql->query("UPDATE `importacao_controle_status` 
		//SET `data_fim` = NOW() AND
		//`dsc_observacao` = '{$strErro}'
		//WHERE `id` = {$id}");		
	}
	
	/**
	 * 
	 * Atualiza status e data fim importação
	 * @param int $intIdControle - id de controle
	 * @param int $intIdStatus - id de status
	 */
	public function atualizaControleImportacao($intIdControle, $intIdStatus) {
		return $this->objZendDbAdapterPdoMysql->query("UPDATE `importacao_controle` 
			SET `id_status`='{$intIdStatus}',
			`data_fim` = NOW() 
		WHERE `id`='{$intIdControle}';");				
	}			
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $arrWhere
	 */
	public function getCount($arrWhere = null) {
		$objZendDbAdapterPdoMysql = $this->getAdapter();
		$objZendDbStatementPdo = $objZendDbAdapterPdoMysql->query('select count(*) as count from ' . $this->_name . General::convertWhere($arrWhere));
		$arrResult = $objZendDbStatementPdo->fetchAll();

		return $arrResult[0]['count'];
	}
	
}