<?php

/**
 * 
 * Controller para relatórios
 * @author sigcorp - Felipe Santiago
 *
 */

class RelatoriosCentral extends Zend_Controller_Action {
		
		
	/**
	 * (non-PHPdoc)
	 * @see Zend_Controller_Action::init()
	 */
	public function init() {			
		$this->objExtracaoDadosModel = Entity_ExtracaoDadosCentral::getInstance();
		
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {		
		$this->objZendConfig = Zend_Registry::get('config');
		$this->view->download_url = $this->objZendConfig->batch->relatorios->dawnload_url;
	}
	
	
}