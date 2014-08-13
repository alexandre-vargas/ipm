<?php
class ImportacaoCentral extends Zend_Controller_Action{

    private $_strDriverMdb;
    private $_objZendDbAdapterPdoMysql;
    private $_strDirectoryPendentes;

    public function init() {
        // Desabilita layout e view.
        Zend_Layout::getMvcInstance()->disableLayout();
        $this->getFrontController()->setParam('noViewRenderer', true);

        $this->_strDriverMdb = Zend_Registry::get('config')->resources->db->access->driver; // Definido em /etc/odbcinst.ini
        $this->_objZendDbAdapterPdoMysql = Zend_Registry::get('db');
        $this->_strDirectoryPendentes = Zend_Registry::get('config')->path->declaracao->contribuinte->pendente;
    }

    public function indexAction() {
        echo "Início do processo";
        $this->_processFiles();
        echo "Fim do processo";
    }

    private function _processFiles() {
        $objDirectory = dir($this->_strDirectoryPendentes);
        while($strFile = $objDirectory->read()) {
            if (trim($strFile) == '.' || trim($strFile) == '..')
                continue;

            $arrExtensao = array_reverse(explode('.', $strFile));
            switch($arrExtensao[0]) {
                case 'mdb':
                    $this->_gia($strFile);
                    break;
                default:
                    $this->_descarta();
            }
/**
 * @TODO retirar os comentários abaixo
 */
            copy($this->_strDirectoryPendentes . $strFile, Zend_Registry::get('config')->path->gia->processado . $strFile);
            unlink($strDirectory . $strFile);
        }
    }

    private function _gia($strFile) {
        $strDSN = "odbc:Driver=$this->_strDriverMdb;DBQ=" . $this->_strDirectoryPendentes . $strFile . ";";
        $objPDO = new PDO($strDSN);

        $this->_objZendDbAdapterPdoMysql->beginTransaction();
        $this->_import($objPDO, $strFile);
        $this->_objZendDbAdapterPdoMysql->commit();

    }

    private function _descarta() {
        echo "descartou";
        die;

    }

    private function _import($objPDO, $strFile){
        $this->_importApuracaoOperacoesProprias($objPDO, $strFile);
        $this->_importApuracaoSubstituicaoTributaria($objPDO, $strFile);
        $this->_importContribuinte($objPDO, $strFile);
        $this->_importDetalhesCFOPs($objPDO, $strFile);
        $this->_importDetalhesInterUFs($objPDO, $strFile);
        $this->_importDIPAM($objPDO, $strFile);
        $this->_importGIA($objPDO, $strFile);
        $this->_importIEsRemetente($objPDO, $strFile);
        $this->_importIESubstituido($objPDO, $strFile);
        $this->_importIESubstituto($objPDO, $strFile);
        $this->_importOcorrencias($objPDO, $strFile);
        /**
         * TODO encontrar uma forma de tratar o erro no PDO na linha abaixo.
         */
        // $this->_importPagamento($objPDO, $strFile);
        $this->_importRecibosCredito($objPDO, $strFile);
        $this->_importRegistroExportacao($objPDO, $strFile);
        $this->_importResumoCFOPsEntradas($objPDO, $strFile);
        $this->_importResumoCFOPsSaidas($objPDO, $strFile);
        $this->_importVersao($objPDO, $strFile);
        $this->_importZFM_ALC($objPDO, $strFile);
    }

    private function _importApuracaoOperacoesProprias($objPDO, $strFile) {
        $objPDOStatement = $objPDO->query('select * from tblApuraçãoOperaçõesPróprias');
        foreach($objPDOStatement as $arrRow) {
            $objZendDbStatementPdo = $this->_objZendDbAdapterPdoMysql->query('
                insert into declaracao_gia_apuracao_operacoes_proprias
                values (
                    null,
                    \'' . $strFile . '\',

                    ' . (trim($arrRow['NroLinha']) != '' ? $arrRow['NroLinha'] : 0) . ',
                    ' . (trim($arrRow['NroGIA']) != '' ? $arrRow['NroGIA'] : 0) . ',

                    \'' . $arrRow['Campo'] . '\',
                    \'' . $arrRow['Linha'] . '\',
                    \'' . $arrRow['CódigoSubitem'] . '\',
                    \'' . $arrRow['Total'] . '\',
                    \'' . $arrRow['Valor'] . '\',

                    null

                )'
            );
        }
    }

    private function _importApuracaoSubstituicaoTributaria($objPDO, $strFile) {
        $objPDOStatement = $objPDO->query('select * from tblApuraçãoSUbstituiçãoTributária');
        foreach($objPDOStatement as $arrRow) {
            $objZendDbStatementPdo = $this->_objZendDbAdapterPdoMysql->query('
                insert into declaracao_gia_apuracao_substituicao_tributaria
                values (
                    null,
                    \'' . $strFile . '\',

                    ' . (trim($arrRow['NroLinha']) != '' ? $arrRow['NroLinha'] : 0) . ',
                    ' . (trim($arrRow['NroGIA']) != '' ? $arrRow['NroGIA'] : 0) . ',

                    \'' . $arrRow['Campo'] . '\',
                    \'' . $arrRow['Linha'] . '\',
                    \'' . $arrRow['CódigoSubitem'] . '\',
                    \'' . $arrRow['Total'] . '\',
                    \'' . $arrRow['Valor'] . '\',

                    null
                )'
            );
        }
    }

    private function _importContribuinte($objPDO, $strFile) {
        $objPDOStatement = $objPDO->query('select * from tblContribuinte');
        foreach($objPDOStatement as $arrRow) {
            $objZendDbStatementPdo = $this->_objZendDbAdapterPdoMysql->query('
                insert into declaracao_gia_contribuinte
                values (
                    null,
                    \'' . $strFile . '\',

                    \'' . $arrRow['IE'] . '\',
                    \'' . $arrRow['RazãoSocial'] . '\',
                    \'' . $arrRow['CNPJ'] . '\',
                    \'' . $arrRow['CNAE'] . '\',
                    \'' . $arrRow['Localização'] . '\',
                    \'' . $arrRow['Número'] . '\',
                    \'' . $arrRow['Complemento'] . '\',
                    \'' . $arrRow['Bairro'] . '\',
                    \'' . $arrRow['CEP'] . '\',
                    \'' . $arrRow['Município'] . '\',
                    \'' . $arrRow['UF'] . '\',
                    \'' . $arrRow['Telefone'] . '\',
                    \'' . $arrRow['HomePage'] . '\',
                    \'' . $arrRow['FAX'] . '\',
                    \'' . $arrRow['Contato'] . '\',

                    \'' . (trim($arrRow['DataCadastro']) != '' ? date('Y-m-d', strtotime($arrRow['DataCadastro'])) : null) . '\',

                    \'' . $arrRow['Email'] . '\',
                    \'' . $arrRow['Observação'] . '\',

                    null
                )'
            );
        }
    }

    private function _importDetalhesCFOPs($objPDO, $strFile) {
        $objPDOStatement = $objPDO->query('select * from tblDetalhesCFOPs');
        foreach($objPDOStatement as $arrRow) {

            $objZendDbStatementPdo = $this->_objZendDbAdapterPdoMysql->query('
                insert into declaracao_gia_detalhes_cfops
                (
                  `novo_nome_arquivo`,

                  `nro_gia`,

                  `cfop`,

                  `valor_contabil`,
                  `base_calculo`,
                  `imposto`,
                  `isentas_nao_trib`,
                  `outras`,
                  `imposto_retido_st`,
                  `imp_ret_substituto_st`,
                  `imp_ret_substituido`,
                  `outros_impostos`
                )
                values (
                    \'' . $strFile . '\',

                    ' . (trim($arrRow['NroGIA']) != '' ? $arrRow['NroGIA'] : 0) . ',

                    \'' . $arrRow['CFOP'] . '\',

                    ' . (trim($arrRow['ValorContábil']) != '' ? str_replace(',', '', $arrRow['ValorContábil']) : 0) . ',
                    ' . (trim($arrRow['BaseCálculo']) != '' ? str_replace(',', '', $arrRow['BaseCálculo']) : 0) . ',
                    ' . (trim($arrRow['Imposto']) != '' ? str_replace(',', '', $arrRow['Imposto']) : 0) . ',
                    ' . (trim($arrRow['IsentasNãoTrib']) != '' ? str_replace(',', '', $arrRow['IsentasNãoTrib']) : 0) . ',
                    ' . (trim($arrRow['Outras']) != '' ? str_replace(',', '', $arrRow['Outras']) : 0) . ',
                    ' . (trim($arrRow['ImpostoRetidoST']) != '' ? str_replace(',', '', $arrRow['ImpostoRetidoST']) : 0) . ',
                    ' . (trim($arrRow['ImpRetSubstitutoST']) != '' ? str_replace(',', '', $arrRow['ImpRetSubstitutoST']) : 0) . ',
                    ' . (trim($arrRow['ImpRetSubstituido']) != '' ? str_replace(',', '', $arrRow['ImpRetSubstituido']) : 0) . ',
                    ' . (trim($arrRow['OutrosImpostos']) != '' ? str_replace(',', '', $arrRow['OutrosImpostos']) : 0) . '

                )'
            );
        }
    }

    private function _importDetalhesInterUFs($objPDO, $strFile) {
        $objPDOStatement = $objPDO->query('select * from tblDetalhesInterUFs');
        foreach($objPDOStatement as $arrRow) {
            $objZendDbStatementPdo = $this->_objZendDbAdapterPdoMysql->query('
                insert into declaracao_gia_detalhes_inter_ufs
                values (
                    null,
                    \'' . $strFile . '\',

                    ' . (trim($arrRow['NroGIA']) != '' ? $arrRow['NroGIA'] : 0) . ',

                    \'' . $arrRow['CFOP'] . '\',
                    \'' . $arrRow['UF'] . '\',

                    ' . (trim($arrRow['ValorContábil_1']) != '' ? str_replace(',', '', $arrRow['ValorContábil_1']) : 0) . ',
                    ' . (trim($arrRow['BaseCálculo_1']) != '' ? str_replace(',', '', $arrRow['BaseCálculo_1']) : 0) . ',
                    ' . (trim($arrRow['ValorContábil_2']) != '' ? str_replace(',', '', $arrRow['ValorContábil_2']) : 0) . ',
                    ' . (trim($arrRow['BaseCálculo_2']) != '' ? str_replace(',', '', $arrRow['BaseCálculo_2']) : 0) . ',
                    ' . (trim($arrRow['Imposto']) != '' ? str_replace(',', '', $arrRow['Imposto']) : 0) . ',
                    ' . (trim($arrRow['Outras']) != '' ? str_replace(',', '', $arrRow['Outras']) : 0) . ',
                    ' . (trim($arrRow['ICMSCobradoST']) != '' ? str_replace(',', '', $arrRow['ICMSCobradoST']) : 0) . ',
                    ' . (trim($arrRow['PetróleoEnergia']) != '' ? str_replace(',', '', $arrRow['PetróleoEnergia']) : 0) . ',
                    ' . (trim($arrRow['OutrosProdutos']) != '' ? str_replace(',', '', $arrRow['OutrosProdutos']) : 0) . ',

                    null

                )'
            );
        }
    }

    private function _importDIPAM($objPDO, $strFile) {
        $objPDOStatement = $objPDO->query('select * from tblDIPAM');
        foreach($objPDOStatement as $arrRow) {
            $objZendDbStatementPdo = $this->_objZendDbAdapterPdoMysql->query('
                insert into declaracao_gia_dipam
                values (
                    null,
                    \'' . $strFile . '\',

                    ' . (trim($arrRow['NroGIA']) != '' ? $arrRow['NroGIA'] : 0) . ',
                    ' . (trim($arrRow['Código']) != '' ? $arrRow['Código'] : 0) . ',

                    \'' . $arrRow['CódDIPAM'] . '\',
                    \'' . $arrRow['Município'] . '\',

                    ' . (trim($arrRow['Valor']) != '' ? str_replace(',', '', $arrRow['Valor']) : 0) . ',

                    null
                )'
            );
        }
    }

    private function _importGIA($objPDO, $strFile) {
        $objPDOStatement = $objPDO->query('select * from tblGIA');
        foreach($objPDOStatement as $arrRow) {
            $objZendDbStatementPdo = $this->_objZendDbAdapterPdoMysql->query('
                insert into declaracao_gia_gia
                values (
                    null,
                    \'' . $strFile . '\',

                    ' . (trim($arrRow['NroGIA']) != '' ? $arrRow['NroGIA'] : 0) . ',

                    \'' . $arrRow['IE'] . '\',

                    ' . (trim($arrRow['Tipo']) != '' ? $arrRow['Tipo'] : 0) . ',

                    \'' . $arrRow['RegTrib'] . '\',

                    \'' . (trim($arrRow['Ref1']) != null ? date('Y-m-d', strtotime($arrRow['Ref1'])) : null) . '\',
                    \'' . (trim($arrRow['Ref2']) != null ? date('Y-m-d', strtotime($arrRow['Ref2'])) : null) . '\',

                    ' . $arrRow['Movimento'] . ',

                    ' . (trim($arrRow['SaldoCredor']) != '' ? str_replace(',', '', $arrRow['SaldoCredor']) : 0) . ',
                    ' . (trim($arrRow['SaldoCredAntDig']) != '' ? str_replace(',', '', $arrRow['SaldoCredAntDig']) : 0) . ',

                    \'' . $arrRow['Transmitida'] . '\',

                    ' . (trim($arrRow['DeduçõesRPA']) != '' ? str_replace(',', '', $arrRow['DeduçõesRPA']) : '0') . ',
                    ' . (trim($arrRow['ICMSFixPerRES']) != '' ? str_replace(',', '', $arrRow['ICMSFixPerRES']) : '0') . ',
                    ' . (trim($arrRow['OutrasRES']) != '' ? str_replace(',', '', $arrRow['OutrasRES']) : '0') . ',
                    ' . (trim($arrRow['SaldoCredorST']) != '' ? str_replace(',', '', $arrRow['SaldoCredorST']) : '0') . ',
                    ' . (trim($arrRow['SaldoCredAntDigST']) != '' ? str_replace(',', '', $arrRow['SaldoCredAntDigST']) : '0') . ',
                    ' . (trim($arrRow['DeduçõesST']) != '' ? str_replace(',', '', $arrRow['DeduçõesST']) : '0') . ',

                    \'' . $arrRow['Origem'] . '\',
                    \'' . $arrRow['OrigemPrefDig'] . '\',

                    ' . $arrRow['Consistente'] . ',

                    \'' . (trim($arrRow['DataTransmissão']) != null ? date('Y-m-d', strtotime($arrRow['DataTransmissão'])) : null) . '\',
                    \'' . (trim($arrRow['DataGeraçãoSubstitutiva']) != null ? date('Y-m-d', strtotime($arrRow['DataGeraçãoSubstitutiva'])) : null) . '\',
                    \'' . (trim($arrRow['DataGeraçãoColigida']) != null ? date('Y-m-d', strtotime($arrRow['DataGeraçãoColigida'])) : null) . '\',

                    \'' . $arrRow['Autenticação'] . '\',
                    \'' . $arrRow['ChaveInterna'] . '\',

                    null
                )'
            );
        }
    }

    private function _importIEsRemetente($objPDO, $strFile) {
        $objPDOStatement = $objPDO->query('select * from tblIEsRemetente');
        foreach($objPDOStatement as $arrRow) {
            $objZendDbStatementPdo = $this->_objZendDbAdapterPdoMysql->query('
                insert into declaracao_gia_ies_remetente
                values (
                    null,
                    \'' . $strFile . '\',

                    ' . (trim($arrRow['Código']) != '' ? $arrRow['Código'] : 0) . ',

                    \'' . $arrRow['IERemetente'] . '\',

                    ' . (trim($arrRow['Valor']) != '' ? str_replace(',', '', $arrRow['Valor']) : 0) . ',

                    null
                )'
            );
        }
    }

    private function _importIESubstituido($objPDO, $strFile) {
        $objPDOStatement = $objPDO->query('select * from tblIESubstituido');
        foreach($objPDOStatement as $arrRow) {
            $objZendDbStatementPdo = $this->_objZendDbAdapterPdoMysql->query('
                insert into declaracao_gia_ie_substituido
                values (
                    null,
                    \'' . $strFile . '\',

                    ' . (trim($arrRow['Código']) != '' ? $arrRow['Código'] : 0) . ',

                    \'' . $arrRow['IESubstituido'] . '\',
                    \'' . $arrRow['NF'] . '\',

                    ' . (trim($arrRow['Valor']) != '' ? str_replace(',', '', $arrRow['Valor']) : 0) . ',

                    null
                )'
            );
        }
    }

    private function _importIESubstituto($objPDO, $strFile) {
        $objPDOStatement = $objPDO->query('select * from tblIESubstituto');
        foreach($objPDOStatement as $arrRow) {
            $objZendDbStatementPdo = $this->_objZendDbAdapterPdoMysql->query('
                insert into declaracao_gia_ie_substituto
                values (
                    null,
                    \'' . $strFile . '\',

                    ' . (trim($arrRow['Código']) != '' ? $arrRow['Código'] : 0) . ',

                    \'' . $arrRow['IESubstituto'] . '\',
                    \'' . $arrRow['NF'] . '\',

                    \'' . (trim($arrRow['DataInicio']) != '' ? date('Y-m-d', strtotime($arrRow['DataInicio'])) : null) . '\',
                    \'' . (trim($arrRow['DataFim']) != '' ? date('Y-m-d', strtotime($arrRow['DataFim'])) : null) . '\',

                    ' . (trim($arrRow['Valor']) != '' ? str_replace(',', '', $arrRow['Valor']) : 0) . ',

                    null
                )'
            );
        }
    }

    private function _importOcorrencias($objPDO, $strFile) {
        $objPDOStatement = $objPDO->query('select * from tblOcorrências');
        foreach($objPDOStatement as $arrRow) {
            $objZendDbStatementPdo = $this->_objZendDbAdapterPdoMysql->query('
                insert into declaracao_gia_ocorrencias
                values (
                    null,
                    \'' . $strFile . '\',

                    ' . (trim($arrRow['NroGIA']) != '' ? $arrRow['NroGIA'] : 0) . ',
                    ' . (trim($arrRow['Código']) != '' ? $arrRow['Código'] : 0) . ',

                    \'' . $arrRow['CódSubItem'] . '\',

                    ' . (trim($arrRow['Valor']) != '' ? str_replace(',', '', $arrRow['Valor']) : 0) . ',

                    \'' . $arrRow['FLegal'] . '\',
                    \'' . $arrRow['Ocorrência'] . '\',

                    ' . (trim($arrRow['OpPróprias']) != '' ? $arrRow['OpPróprias'] : 0) . ',

                    null

                )'
            );
        }
    }

    private function _importPagamento($objPDO, $strFile) {
        try {
            $objPDOStatement = $objPDO->query('select * from tblPagamento');
            foreach($objPDOStatement as $arrRow) {
                $objZendDbStatementPdo = $this->_objZendDbAdapterPdoMysql->query('
                    insert into declaracao_gia_pagamento
                    values (
                        null,
                        \'' . $strFile . '\',

                        ' . (trim($arrRow['NroGIA']) != '' ? $arrRow['NroGIA'] : 0) . ',
                        ' . (trim($arrRow['OpPróprias']) != '' ? $arrRow['OpPróprias'] : 0) . ',
                        ' . (trim($arrRow['PagtoID']) != '' ? $arrRow['PagtoID'] : 0) . ',

                        \'' . $arrRow['Data'] . '\',

                        ' . (trim($arrRow['Valor']) != '' ? str_replace(',', '', $arrRow['Valor']) : 0) . ',

                        \'' . $arrRow['JurosMora'] . '\',
                        \'' . $arrRow['MultaMora_Infração'] . '\',
                        \'' . $arrRow['AcréscimoFinanceiro'] . '\',
                        \'' . $arrRow['HonoráriosAdvocatícios'] . '\',
                        \'' . $arrRow['Observações'] . '\',

                        null

                    )'
                );
            }
        }catch(Exception $e){
            echo "teste";
            die;

        }
    }

    private function _importRecibosCredito($objPDO, $strFile) {
        $objPDOStatement = $objPDO->query('select * from tblRecibosCredito');
        foreach($objPDOStatement as $arrRow) {
            $objZendDbStatementPdo = $this->_objZendDbAdapterPdoMysql->query('
                insert into declaracao_gia_recibos_credito
                values (
                    null,
                    \'' . $strFile . '\',

                    ' . (trim($arrRow['Código']) != '' ? $arrRow['Código'] : 0) . ',

                    \'' . $arrRow['CodAutorização'] . '\',

                    ' . (trim($arrRow['Valor']) != '' ? str_replace(',', '', $arrRow['Valor']) : 0) . ',

                    null
                )'
            );
        }
    }

    private function _importRegistroExportacao($objPDO, $strFile) {
        $objPDOStatement = $objPDO->query('select * from tblRegistroExportação');
        foreach($objPDOStatement as $arrRow) {
            $objZendDbStatementPdo = $this->_objZendDbAdapterPdoMysql->query('
                insert into declaracao_gia_registro_exportacao
                values (
                    null,
                    \'' . $strFile . '\',

                    ' . (trim($arrRow['NroGIA']) != '' ? $arrRow['NroGIA'] : 0) . ',

                    \'' . $arrRow['RE'] . '\',

                    null
                )'
            );
        }
    }

    private function _importResumoCFOPsEntradas($objPDO, $strFile) {
        $objPDOStatement = $objPDO->query('select * from tblResumoCFOPsEntradas');
        foreach($objPDOStatement as $arrRow) {
            $objZendDbStatementPdo = $this->_objZendDbAdapterPdoMysql->query('
                insert into declaracao_gia_resumo_cfops_entradas
                values (
                    null,
                    \'' . $strFile . '\',

                    ' . (trim($arrRow['NroLinha']) != '' ? $arrRow['NroLinha'] : 0) . ',
                    ' . (trim($arrRow['NroGIA']) != '' ? $arrRow['NroGIA'] : 0) . ',

                    \'' . $arrRow['Linha'] . '\',
                    \'' . $arrRow['CFOP'] . '\',
                    \'' . $arrRow['ValorContábil'] . '\',
                    \'' . $arrRow['BaseCálculo'] . '\',
                    \'' . $arrRow['Imposto'] . '\',
                    \'' . $arrRow['IsentasNãoTrib'] . '\',
                    \'' . $arrRow['Outras'] . '\',
                    \'' . $arrRow['ImpostoRetidoST'] . '\',
                    \'' . $arrRow['ImpRetSubstitutoST'] . '\',
                    \'' . $arrRow['ImpRetSubstituto'] . '\',
                    \'' . $arrRow['OutrosImpostos'] . '\',

                    null
                )'
            );
        }
    }

    private function _importResumoCFOPsSaidas($objPDO, $strFile) {
        $objPDOStatement = $objPDO->query('select * from tblResumoCFOPsSaídas');
        foreach($objPDOStatement as $arrRow) {
            $objZendDbStatementPdo = $this->_objZendDbAdapterPdoMysql->query('
                insert into declaracao_gia_resumo_cfops_saidas
                values (
                    null,
                    \'' . $strFile . '\',

                    ' . (trim($arrRow['NroLinha']) != '' ? $arrRow['NroLinha'] : 0) . ',
                    ' . (trim($arrRow['NroGIA']) != '' ? $arrRow['NroGIA'] : 0) . ',

                    \'' . $arrRow['Linha'] . '\',
                    \'' . $arrRow['CFOP'] . '\',
                    \'' . $arrRow['ValorContábil'] . '\',
                    \'' . $arrRow['BaseCálculo'] . '\',
                    \'' . $arrRow['Imposto'] . '\',
                    \'' . $arrRow['IsentasNãoTrib'] . '\',
                    \'' . $arrRow['Outras'] . '\',
                    \'' . $arrRow['ImpostoRetidoST'] . '\',
                    \'' . $arrRow['ImpRetSubstitutoST'] . '\',
                    \'' . $arrRow['ImpRetSubstituto'] . '\',
                    \'' . $arrRow['OutrosImpostos'] . '\',

                    null
                )'
            );
        }
    }


    private function _importVersao($objPDO, $strFile) {
        $objPDOStatement = $objPDO->query('select * from tblVersão');
        foreach($objPDOStatement as $arrRow) {
            $objZendDbStatementPdo = $this->_objZendDbAdapterPdoMysql->query('
                insert into declaracao_gia_versao
                values (
                    null,
                    \'' . $strFile . '\',

                    \'' . $arrRow['Versão'] . '\',
                    \'' . $arrRow['Observação'] . '\',

                    null
                )'
            );
        }
    }

    private function _importZFM_ALC($objPDO, $strFile) {
        $objPDOStatement = $objPDO->query('select * from tblZFM_ALC');
        foreach($objPDOStatement as $arrRow) {
            $objZendDbStatementPdo = $this->_objZendDbAdapterPdoMysql->query('
                insert into declaracao_gia_zfm_alc
                values (
                    null,
                    \'' . $strFile . '\',

                    ' . (trim($arrRow['']) != '' ? $arrRow[''] : 0) . ',

                    \'' . $arrRow[''] . '\',
                    \'' . $arrRow[''] . '\',

                    ' . (trim($arrRow['']) != '' ? $arrRow[''] : 0) . ',

                    \'' . $arrRow[''] . '\',

                    \'' . (trim($arrRow['']) != '' ? date('Y-m-d', strtotime($arrRow[''])) : null) . '\',

                    ' . (trim($arrRow['']) != '' ? str_replace(',', '', $arrRow['']) : 0) . ',

                    \'' . $arrRow[''] . '\',
                    \'' . $arrRow[''] . '\',

                    null
                )'
            );
        }
    }


}