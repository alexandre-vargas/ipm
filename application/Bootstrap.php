<?php
class Bootstrap {

	public function runApp() {
		/*
		 * Constantes do sistema
		 */
		define('STATUS_INATIVO', 0);
		define('STATUS_ATIVO', 1);
		
		define('STATUS_PENDENTE', 1);
		define('STATUS_APROVADO', 2);
		define('STATUS_NAO_APROVADO', 3);
	
		define('TIPO_CONTROLE_LISTAGEM_DEFAULT', 1);
		define('TIPO_CONTROLE_LISTAGEM_REMOVER', 2);
		define('TIPO_CONTROLE_LISTAGEM_SELECIONAR', 3);
		
		define('TIPO_CONTROLE_FORM_DEFAULT', 1);
		define('TIPO_CONTROLE_FORM_WORKFLOW', 2);
	
		define('PERFIL_CONTRIBUINTE',1);
		define('PERFIL_CONTADOR',2);
		define('PERFIL_GESTOR',3);
		define('PERFIL_ADMIN',4);
		
		define('EXCEPTION_FOREIGN_KEY', '23000');
		
		/*
		 * Constantes de Path
		 */
		define('BASE_PATH', dirname(dirname(__FILE__)));
		define('APPLICATION_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'application');
		$arrHost = explode('.', $_SERVER['HTTP_HOST']);
		define('PREFEITURA', $arrHost[0]);

		set_include_path(
				get_include_path() .
                PATH_SEPARATOR . '/usr/share/php/ZendFramework-1.12.3/library/' .

				PATH_SEPARATOR . APPLICATION_PATH . DIRECTORY_SEPARATOR . 'prefeituras' . DIRECTORY_SEPARATOR . PREFEITURA . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR .

				PATH_SEPARATOR . APPLICATION_PATH . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR .
				PATH_SEPARATOR . APPLICATION_PATH . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR);

		include 'Zend/Loader/Autoloader.php';
		Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);

		$objZendControllerFront = Zend_Controller_Front::getInstance();
		$objZendControllerFront->throwExceptions(false);
		$objZendControllerFront->setControllerDirectory(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'prefeituras' . DIRECTORY_SEPARATOR . PREFEITURA . DIRECTORY_SEPARATOR . 'controllers');

		Zend_Layout::startMvc(array('layoutPath' => APPLICATION_PATH . DIRECTORY_SEPARATOR . 'prefeituras' . DIRECTORY_SEPARATOR . PREFEITURA . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR  . 'layout' . DIRECTORY_SEPARATOR));
		$objZendControllerFront->registerPlugin(new Plugin_ViewSetup());

		// Cria a conexão com o banco de dados e registra.
		if($_SERVER['REMOTE_ADDR'] == '127.0.0.1')
			$strEnviroment = 'localhost';
		elseif($arrURI[1] == 'xxxx')
			$strEnviroment = 'homologacao';
		elseif($arrURI[1] == 'xxxxxx')
			$strEnviroment = 'desenvolvimento';

		// Cria uma instância do Config e registra.
		$objZendConfigIni = new Zend_Config_Ini(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'prefeituras' . DIRECTORY_SEPARATOR . PREFEITURA . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.ini');
		Zend_Registry::set('config', $objZendConfigIni->$strEnviroment);

		$objZendDbAdapterPDOMysql = Zend_Db::factory(Zend_Registry::get('config')->resources->db);
		Zend_Db_Table::setDefaultAdapter($objZendDbAdapterPDOMysql);
		Zend_Registry::set('db', $objZendDbAdapterPDOMysql);

        $objZendControllerFront->dispatch();

	}
}