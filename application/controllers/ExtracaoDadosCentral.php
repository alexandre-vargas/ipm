<?php

/**
 * 
 * Controller para extração de dados para os relatórios
 * @author sigcorp - Felipe Santiago
 *
 */

class ExtracaoDadosCentral extends Zend_Controller_Action {
		
	private $objZendConfig;	
	/**
	 * (non-PHPdoc)
	 * @see Zend_Controller_Action::init()
	 */
	public function init() {	
		$this->getFrontController()->setParam('noViewRenderer', true);
		$this->objExtracaoDadosModel = Entity_ExtracaoDadosCentral::getInstance();
		$this->objZendConfig = Zend_Registry::get('config');		
	}
	
	/**
	 * Extracao de dados para o relatório de declarações mensais.
	 * declaracoes-mensais
	 */   
	public function declaracoesMensaisAction() {			
		if($this->objExtracaoDadosModel->getDeclaracoesMensaisToFile('relatorio_declaracoes_mensais.csv')) {
			@unlink($this->objZendConfig->batch->relatorios->dawnload_dir."relatorio_declaracoes_mensais.csv");	
			@rename($this->objZendConfig->batch->mysql->dir_processamento."relatorio_declaracoes_mensais.csv", $this->objZendConfig->batch->relatorios->dawnload_dir."relatorio_declaracoes_mensais.csv");
			@unlink($this->objZendConfig->batch->mysql->dir_processamento."relatorio_declaracoes_mensais.csv");	
		}
		echo 'fim';
		exit;	
	}
	
	/**
	 * Extracao de dados para o relatório de declarações mensais não transmitidas.
	 *    declaracoes-mensais-nao-enviadas
	 */
	public function declaracoesMensaisNaoEnviadasAction() {	
		if($this->objExtracaoDadosModel->getDeclaracoesMensaisNaoRealizadasToFile('relatorio_declaracoes_mensais_nao_transmitidas.csv')) {
			@unlink($this->objZendConfig->batch->relatorios->dawnload_dir."relatorio_declaracoes_mensais_nao_transmitidas.csv");	
			@rename($this->objZendConfig->batch->mysql->dir_processamento."relatorio_declaracoes_mensais_nao_transmitidas.csv", $this->objZendConfig->batch->relatorios->dawnload_dir."relatorio_declaracoes_mensais_nao_transmitidas.csv");
			@unlink($this->objZendConfig->batch->mysql->dir_processamento."relatorio_declaracoes_mensais_nao_transmitidas.csv");	
		}
		echo 'fim';
		exit;
	}
	
}