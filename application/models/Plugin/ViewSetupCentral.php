<?php 
class Plugin_ViewSetupCentral extends Zend_Controller_Plugin_Abstract {
	
	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
		$this->carregaDadosCabecalho($request);
	}

	public function postDispatch(Zend_Controller_Request_Abstract $request) {
		$this->importaJavascriptCss($request);
	}

	protected function carregaDadosCabecalho($request) {
		$objZendSessionNamespaceLogin = new Zend_Session_Namespace('login');
		if(!isset($objZendSessionNamespaceLogin))
			return;
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$viewRenderer->init();
		$view = $viewRenderer->view;
		$view->objZendDbTableRowPessoa = $objZendSessionNamespaceLogin->objZendDbTableRowPessoa;
		
	}
	
	protected function importaJavascriptCss($request) {
		$strControllerName = strtolower($request->getControllerName());
		$strActionName = strtolower($request->getActionName());
		
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$viewRenderer->init();
		$view = $viewRenderer->view;

		if(file_exists('js/scripts/' . strtolower($strControllerName) . '/' . $strActionName . '.js'))
			$view->headScript()->appendFile('js/scripts/' . strtolower($strControllerName) . '/' . $strActionName . '.js');
		if(file_exists('css/scripts/' . strtolower($strControllerName) . '/' . $strActionName . '.css'))
			$view->headLink()->appendStylesheet('css/scripts/' . strtolower($strControllerName) . '/' . $strActionName . '.css');
		if(file_exists('prefeituras/' . PREFEITURA . '/js/scripts/' . strtolower($strControllerName) . '/' . $strActionName . '.js'))
			$view->headScript()->appendFile('prefeituras/' . PREFEITURA . '/js/scripts/' . strtolower($strControllerName) . '/' . $strActionName . '.js');
		if(file_exists('prefeituras/' . PREFEITURA . '/css/scripts/' . strtolower($strControllerName) . '/' . $strActionName . '.css'))
			$view->headLink()->appendStylesheet('prefeituras/' . PREFEITURA . '/css/scripts/' . strtolower($strControllerName) . '/' . $strActionName . '.css');
	}
	
}
?>
