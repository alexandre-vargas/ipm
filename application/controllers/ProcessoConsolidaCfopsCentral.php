<?php
class ProcessoConsolidaCfopsCentral extends Zend_Controller_Action {

    private $_objZendDbAdapterPdoMysql;

    public function init() {
        // Desabilita layout e view.
        Zend_Layout::getMvcInstance()->disableLayout();
        $this->getFrontController()->setParam('noViewRenderer', true);

        $this->_objZendDbAdapterPdoMysql = Zend_Registry::get('db');
    }

    public function indexAction() {
        $this->getResponse()->setRedirect($this->view->url(array('controller' => 'processo-consolida-cfops', 'action' => 'processa-pendentes'), null, true))->sendResponse();
    }

    public function processaPendentesAction() {
        $objZendDbStatementPdo = $this->_objZendDbAdapterPdoMysql->query('select * from declaracao_gia_detalhes_cfops where cons_status = 0');

        foreach($objZendDbStatementPdo as $arrRow) {
$this->_entraNoVa($arrRow['cfop']);
die;
            $strQuery = '
                update declaracao_gia_detalhes_cfops
                set
                  cons_basecalc_isennaotrib_outras = ' . $this->_somaTresCampos($arrRow) . ',
                  cons_tipo_basico = ' . $this->_tipoBasico($arrRow['cfop']) . ',
                  cons_tipo = ' . $this->_tipo($arrRow['cfop']) . ',
                  cons_status = ' . STATUS_PROCESSADO . ',
                  cons_cfop_digito = ' . $this->_recuperaDigito($arrRow['cfop']) . ',
                  cons_cfop_fracao = ' . $this->_recuperaFracao($arrRow['cfop']) . ',
                  cons_entra_no_va = ' . $this->_entraNoVa($arrRow['cfop']) . '
                where
                  id = ' . $arrRow['id'];

            $this->_objZendDbAdapterPdoMysql->query($strQuery);
/**
 * TODO Retirar o die. Ele serve apenas pra que o foreach não atualize todos os registros, permitindo outros testes.
 */
die;
        }
    }

    private function _entraNoVa($strCFOP) {

        echo $strCFOP;
        die;

    }

    private function _recuperaDigito($strCFOP) {
        $arrCFOP = explode('.', $strCFOP);
        return $arrCFOP[0];
    }

    private function _recuperaFracao($strCFOP) {
        $arrCFOP = explode('.', $strCFOP);
        return $arrCFOP[1];
    }

    private function _tipoBasico($strCFOP) {

        $intDigito = $this->_recuperaDigito($strCFOP);

        if ($intDigito == 1 || $intDigito == 2 || $intDigito == 3)
            return 1; // Entrada
        return 2; //  Saída, caso seja 5, 6 ou 7

    }

    private function _tipo($strCFOP) {

        $intDigito = $this->_recuperaDigito($strCFOP);

        if ($intDigito == 1 || $intDigito == 5)
            return 1; // No Estado
        elseif($intDigito == 2 || $intDigito == 6)
            return 2; //  Fora do Estado
        elseif($intDigito == 3 || $intDigito == 7)
            return 3; //  Exterior

    }

    private function _somaTresCampos($arrRow) {
        $intBaseCalculo = doubleval($arrRow['base_calculo']);
        $intIsennaotrib = doubleval($arrRow['isentas_nao_trib']);
        $intOutras = doubleval($arrRow['outras']);

        $intVA = $intBaseCalculo + $intIsennaotrib + $intOutras;

        return $intVA;
    }

}