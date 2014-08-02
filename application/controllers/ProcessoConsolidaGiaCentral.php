<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 18/06/14
 * Time: 01:28
 */

class ProcessoConsolidaGiaCentral extends Zend_Controller_Action{
    private $_objZendDbAdapterPdoMysql;

    public function init() {
        // Desabilita layout e view.
        Zend_Layout::getMvcInstance()->disableLayout();
        $this->getFrontController()->setParam('noViewRenderer', true);

        $this->_objZendDbAdapterPdoMysql = Zend_Registry::get('db');
    }

    public function indexAction() {
        $this->getResponse()->setRedirect($this->view->url(array('controller' => 'processo-consolida-gia', 'action' => 'processa-pendentes'), null, true))->sendResponse();
    }

    public function processaPendentesAction() {
        /**
         * TODO Criar campos consolidados na tabela de gia e dar continuidade ao desenvolvimento deste metodo.
         */
echo "processa pendentes";
die;
        $objZendDbStatementPdo = $this->_objZendDbAdapterPdoMysql->query('select * from declaracao_gia_gia');

        foreach($objZendDbStatementPdo as $arrRow) {

            $strQuery = '
                update declaracao_gia_gia
                set
                where
                  id = ' . $arrRow['id'];

            $this->_objZendDbAdapterPdoMysql->query($strQuery);
            /**
             * TODO Retirar o die. Ele serve apenas pra que o foreach não atualize todos os registros, permitindo outros testes.
             */
            die;
        }
    }

} 