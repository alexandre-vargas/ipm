<?php
class GiaCentral extends Zend_Controller_Action {

    public function listagemGiaAction() {
        $objZendDbAdapterPdoMysql = Zend_Db_Table::getDefaultAdapter();
        $objZendDbStatmentPdo = $objZendDbAdapterPdoMysql->query('select * from declaracao_gia_gia');

        $this->view->objZendDbStatmentPdo = $objZendDbStatmentPdo;
    }
}