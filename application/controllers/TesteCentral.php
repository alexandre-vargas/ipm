<?php
class TesteCentral extends Zend_Controller_Action {
	
	public function init() {
		Zend_Layout::getMvcInstance()->setLayout('logout');
	}
	
	public function accessAction() {

		try {
            $driver = 'MDBTools';
            $mdb_file = '/home/alexandre/Workspace/ipm/gia.mdb';
            $dataSourceName = "odbc:Driver=$driver;DBQ=$mdb_file;";
echo $dataSourceName;
die;

            $connection = new PDO($dataSourceName);

echo "passou";
die;

			$rsc = odbc_connect("ACCESS_SAMPLE", '', 'kamisama2');
print '<pre>';
print_r($rsc);
die;

			$rscResult = odbc_exec($rsc, "select * from tblDetalhesInterUFs");

			$arrResult = Array();
			While($arrResult[] = odbc_fetch_array($rscResult));
			if($arrResult[0] === false) {
				echo "sem resultado";
				die;
			}
			
			/*
			print '<pre>';
			print_r($arrResult);
			die;
			*/
			
			/*
			$intQtdRegistros = odbc_num_rows($rscResult);
			echo $intQtdRegistros;
			die;
			*/
			
			
			$arrCampos = array();
			for($i=1; $i <= $intQtdCampos; $i++) {
				$strCampo = odbc_field_name($rscResult, $i);
				$arrCampos[$strCampo] = null;
				
				
			}
			
			print '<pre>';
			print_r($arrCampos);
			die;
		} catch (Exception $e) {
			echo $e->getMessage();
            die;
		}
		
	}
	
	
	public function md5Action() {
		
		echo md5('111');
		die;
		
	}
	
	
	public function pillBoxAction() {
		
	}
	
}
?>