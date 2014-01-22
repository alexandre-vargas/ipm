<?php
/**
 * 
 * Model genérica: extração de dados para relatórios 
 * @author Felipe Santiago
 *
 */
class Entity_ExtracaoDadosCentral extends Zend_Db_Table_Abstract {
	
	protected static $instance = null;
	protected $objZendDbAdapterPdoMysql ;
			
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
		$this->objZendConfig = Zend_Registry::get('config');
	}
	
	/**
	 * 
	 * Query para extração do relatório
	 * @param string $strFileName - Nome do arquivo
	 */
	public function getDeclaracoesMensaisToFile($strFileName){		
		$sql = 'SELECT c.inscricao_estadual,
			c.razao_social,
			c.cnpj,
			c.localizacao,
			c.numero,
			c.bairro,
			c.cep,
			c.municipio,
			c.uf,
			c.telefone,
			g.ref1 data_referencia,
			g.data_transmissao data_transmissao_para_estado
			INTO OUTFILE ';
		$sql.= "'".$this->objZendConfig->batch->mysql->dir_processamento.$strFileName."'";
		$sql.= ' FIELDS TERMINATED BY \';\' OPTIONALLY ENCLOSED BY \'"\'
				 LINES TERMINATED BY \'\n\'		
			FROM importacao_gia_contribuinte c
			INNER JOIN importacao_gia_gia g ON c.id_importacao_controle = g.id_importacao_controle
			GROUP BY 
				c.inscricao_estadual,
				g.ref1 ';
			
		try {
			$objRelatorio = $this->objZendDbAdapterPdoMysql->query($sql);
		}catch (Exception $e) {
			echo $e->getMessage();
			exit;
		}			
		
		return true;
		
	}

	/**
	 * 
	 * Query para extração do relatório de declarações mensais não realizadas
	 * @param string $strFileName - Nome do arquivo
	 */
	public function getDeclaracoesMensaisNaoRealizadasToFile($strFileName){		
		$sql = 'SELECT c.inscricao_estadual,
					p.descri,
					p.cpf_cnpj,
					p.logradouro,
					p.numero,
					p.bairro,
					p.cep,
					p.municipio,
					p.estado,
					g.ref1 data_referencia,
					g.data_transmissao data_transmissao_para_estado,
					IFNULL(g.id, \'GIA não transmitida\') status
					INTO OUTFILE ';
		$sql.= "'".$this->objZendConfig->batch->mysql->dir_processamento.$strFileName."'";
		$sql.= ' FIELDS TERMINATED BY \';\' OPTIONALLY ENCLOSED BY \'"\'
			  			LINES TERMINATED BY \'\n\'	
				FROM sigicms_itapira_desenvolvimento.contribuinte c
				INNER JOIN sigicms_itapira_desenvolvimento.pessoa p ON p.id = c.id_pessoa
				LEFT JOIN importacao_gia_gia g ON c.inscricao_estadual = replace (g.ie, \'.\', \'\')
				WHERE g.id IS NULL
					GROUP BY 
					c.inscricao_estadual,
					g.ref1
					ORDER BY g.id';
		
		try {
			$objRelatorio = $this->objZendDbAdapterPdoMysql->query($sql);
		}catch (Exception $e) {
			echo $e->getMessage();
			exit;
		}			
	
		return true;
	
	}
	
}