CREATE TABLE `declaracao_gia_apuracao_operacoes_proprias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `novo_nome_arquivo` varchar(150) NOT NULL,

  `nro_linha` int(11) DEFAULT NULL,
  `nro_gia` int(11) DEFAULT NULL,
  `campo` varchar(255) DEFAULT NULL,
  `linha` varchar(255) DEFAULT NULL,
  `codigo_subitem` varchar(255) DEFAULT NULL,
  `total` varchar(255) DEFAULT NULL,
  `valor` varchar(255) DEFAULT NULL,
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ;


CREATE TABLE `declaracao_gia_apuracao_substituicao_tributaria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `novo_nome_arquivo` varchar(150) NOT NULL,

  `nro_linha` int(11) DEFAULT NULL,
  `nro_gia` int(11) DEFAULT NULL,
  `campo` varchar(255) DEFAULT NULL,
  `linha` varchar(255) DEFAULT NULL,
  `codigo_subitem` varchar(255) DEFAULT NULL,
  `total` varchar(255) DEFAULT NULL,
  `valor` varchar(255) DEFAULT NULL,
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ;


CREATE TABLE `declaracao_gia_contribuinte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `novo_nome_arquivo` varchar(150) NOT NULL,
  `ie` varchar(15) DEFAULT NULL,
  `razao_social` varchar(255) DEFAULT NULL,
  `cnpj` varchar(30) DEFAULT NULL,
  `cnae` varchar(20) DEFAULT NULL,
  `localizacao` varchar(100) DEFAULT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `complemento` varchar(50) DEFAULT NULL,
  `bairro` varchar(50) DEFAULT NULL,
  `cep` varchar(20) DEFAULT NULL,
  `municipio` varchar(50) DEFAULT NULL,
  `uf` varchar(2) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `home_page` varchar(100) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `contato` varchar(50) DEFAULT NULL,
  `data_cadastro` timestamp NULL DEFAULT NULL,
  `email` text,
  `observacao` text,
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ;

CREATE TABLE `declaracao_gia_detalhes_cfops` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `novo_nome_arquivo` varchar(150) NOT NULL,

  `nro_gia` int(11) DEFAULT NULL,
  `cfop` varchar(10) DEFAULT NULL,
  `valor_contabil` decimal(19,4) DEFAULT NULL,
  `base_calculo` decimal(19,4) DEFAULT NULL,
  `imposto` decimal(19,4) DEFAULT NULL,
  `isentas_nao_trib` decimal(19,4) DEFAULT NULL,
  `outras` decimal(19,4) DEFAULT NULL,
  `imposto_retido_st` decimal(19,4) DEFAULT NULL,
  `imp_ret_substituto_st` decimal(19,4) DEFAULT NULL,
  `imp_ret_substituido` decimal(19,4) DEFAULT NULL,
  `outros_impostos` decimal(19,4) DEFAULT NULL,
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  `cons_basecalc_isennaotrib_outras` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `cons_tipo_basico` tinyint(4) NOT NULL COMMENT '1-Entrada, 2-Saída',
  `cons_tipo` tinyint(4) NOT NULL COMMENT '1-Dentro do estado, 2-Fora do estado, 3-Exterior',
  `cons_status` tinyint(4) DEFAULT '0',
  `cons_cfop_digito` tinyint(4) NOT NULL COMMENT 'Dígito à esquerda no código CFOP',
  `cons_cfop_fracao` smallint(6) NOT NULL COMMENT 'Dígito à direita no código CFOP',
  `cons_contribui_va` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8

CREATE TABLE IF NOT EXISTS `declaracao_gia_detalhes_inter_ufs` (
  `id` int not null auto_increment,
  `novo_nome_arquivo` varchar(150) NOT NULL,

  `nro_gia` int null,
  `cfop` varchar(10),
  `uf` varchar(2),
  `valor_contabil_1` numeric(19,4),
  `base_calculo_1` numeric(19,4),
  `valor_contabil_2` numeric(19,4),
  `base_calculo_2` numeric(19,4),
  `imposto` numeric(19,4),
  `outras` numeric(19,4),
  `icms_cobrado_st` numeric(19,4),
  `petroleo_energia` numeric(19,4),
  `outros_produtos` numeric(19,4),
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY(`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `declaracao_gia_dipam` (
  `id` int not null auto_increment,
  `novo_nome_arquivo` varchar(150) NOT NULL,

  `nro_gia` int null,
  `codigo` int null,
  `cod_dipam` varchar(10) null,
  `municipio` varchar(255) null,
  `valor` numeric(19,4) null,
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY(`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `declaracao_gia_gia` (
  `id` int not null auto_increment,
  `novo_nome_arquivo` varchar(150) NOT NULL,

  `nro_gia` int null,
  `ie` varchar(15) null,
  `tipo` smallint null, -- ACCESS: 1 Byte de 0 a 255. MySQL: 2 Bytes de -32768 a 32767
  `reg_trib` varchar(2) null,
  `ref1` timestamp null, -- ACCESS: Data/Hora 8 Bytes. MySQL: 4 bytes (datetime is 8 bytes)
  `ref2` timestamp null,
  `movimento` tinyint(1), -- ACCESS: Sim/Não de 1 bit --MySQL: tinyint de -128 a 127
  `saldo_credor` numeric(19,4) null,
  `saldo_cred_ant_dig` numeric(19,4) null,
  `transmitida` varchar(1) null,
  `deducoes_rpa` numeric(19,4) null,
  `icms_fix_per_res` numeric(19,4) null,
  `outras_res` numeric(19,4) null,
  `saldo_credor_st` numeric(19,4) null,
  `saldo_credor_ant_dig_st` numeric(19,4) null,
  `deducoes_st` numeric(19,4) null,
  `origem` varchar(15) null,
  `origem_pref_dig` varchar(1) null,
  `consistente` boolean,
  `data_transmissao` timestamp null,
  `data_geracao_substitutiva` timestamp null,
  `data_geracao_coligida` timestamp null,
  `autenticacao` varchar(32) null,
  `chave_interna` varchar(32) null,
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY(`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `declaracao_gia_ies_remetente` (
  `id` int not null auto_increment,
  `novo_nome_arquivo` varchar(150) NOT NULL,

  `codigo` int null,
  `ie_remetente` varchar(15) null,
  `valor` numeric(19,4) null,
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY(`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `declaracao_gia_ie_substituido` (
  `id` int not null auto_increment,
  `novo_nome_arquivo` varchar(150) NOT NULL,

  `codigo` int null, -- Access: Inteiro Longo de -2.147.483.648 a 2.147.483.647 -- MySQL: 4 bytes de -2.147.483.648 a 2.147.483.647
  `ie_substituido` varchar(15) null,
  `nf`varchar(6) null,
  `valor` numeric(19,4) null,
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY(`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `declaracao_gia_ie_substituto` (
  `id` int not null auto_increment,
  `novo_nome_arquivo` varchar(150) NOT NULL,

  `codigo` int null,
  `ie_substituto` varchar(15) null,
  `nf` varchar(6) null,
  `data_inicio` timestamp null,
  `data_fim` timestamp null,
  `valor` numeric(19, 4) null,
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY(`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `declaracao_gia_ocorrencias` (
  `id` int not null auto_increment,
  `novo_nome_arquivo` varchar(150) NOT NULL,

  `ie_substituto` int null,
  `codigo` int null,
  `cod_sub_item` varchar(10) null,
  `valor` numeric(19, 4) null, /* ACCESS: Unidade Monetaria que utiliza 8 bytes e armazena de 922.337.203.685.477,5808 à 922.337.203.685.477,5807 : */
  `flegal` text null, /* ACCESS: Memorando que armazena 65.535 caracteres --MySQL: Text que armazena 65.535 caracteres */
  `ocorrencia` text null,
  `op_proprias` tinyint(1) null,
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY(`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `declaracao_gia_pagamento` (
  `id` int not null auto_increment,
  `novo_nome_arquivo` varchar(150) NOT NULL,

  `nro_gia` int null,
  `op_proprias` tinyint(1) null,
  `pagto_id` int null,
  `data` varchar(10) null,
  `valor` decimal(19,4) null,
  `juros_mora` varchar(50) null,
  `multa_mora_inflacao` varchar(50) null,
  `acrescimo_financeiro` varchar(50) null,
  `honorarios_advocaricios` varchar(50) null,
  `observacoes` text null,
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY(`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `declaracao_gia_recibos_credito` (
  `id` int not null auto_increment,
  `novo_nome_arquivo` varchar(150) NOT NULL,

  `codigo` int null,
  `cod_autorizacao` varchar(16) null,
  `valor` decimal(19,4) null,
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY(`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `declaracao_gia_registro_exportacao` (
  `id` int not null auto_increment,
  `novo_nome_arquivo` varchar(150) NOT NULL,

  `nro_gia` int null,
  `re` varchar(15),
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY(`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `declaracao_gia_resumo_cfops_entradas` (
  `id` int not null auto_increment,
  `novo_nome_arquivo` varchar(150) NOT NULL,

  `nro_linha` int null,
  `nro_gia` int null,
  `linha` varchar(255),
  `cfop` varchar(255),
  `valor_contabil` varchar(255),
  `base_calculo` varchar(255),
  `imposto` varchar(255),
  `isentas_nao_trib` varchar(255),
  `outras` varchar(255),
  `imposto_retido_st` varchar(255),
  `imp_ret_substituto_st` varchar(255),
  `imp_ret_substituido` varchar(255),
  `outros_impostos` varchar(255),
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY(`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `declaracao_gia_resumo_cfops_saidas` (
  `id` int not null auto_increment,
  `novo_nome_arquivo` varchar(150) NOT NULL,

  `nro_linha` int null,
  `nro_gia` int null,
  `linha` varchar(255),
  `cfop` varchar(255),
  `valor_contabil` varchar(255),
  `base_calculo` varchar(255),
  `imposto` varchar(255),
  `isentas_nao_trib` varchar(255),
  `outras` varchar(255),
  `imposto_retido_st` varchar(255),
  `imp_ret_substituto_st` varchar(255),
  `imp_ret_substituido` varchar(255),
  `outros_impostos` varchar(255),
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY(`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `declaracao_gia_versao` (
  `id` int not null auto_increment,
  `novo_nome_arquivo` varchar(150) NOT NULL,

  `versao` varchar(15) null,
  `observacao` varchar(255) null,
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY(`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `declaracao_gia_zfm_alc` (
  `id` int not null auto_increment,
  `novo_nome_arquivo` varchar(150) NOT NULL,

  `nro_gia` int null,
  `cfop` varchar(10) null,
  `uf` varchar(2) null,
  `nro_lancamento` int null,
  `nf` varchar(6),
  `data` timestamp null,
  `valor` decimal(19, 4) null,
  `cnpj_dest` varchar(30) null,
  `municipio_dest` varchar(50),
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY(`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ;


--
-- Estrutura da tabela `componente`
--

CREATE TABLE IF NOT EXISTS `componente` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descri` varchar(50) NOT NULL,
  `peso` float NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UQ_ID` (`id`),
  UNIQUE KEY `UQ_DESCRI` (`descri`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


--
-- Estrutura da tabela `contador`
--

CREATE TABLE IF NOT EXISTS `contador` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_pessoa` int(11) NOT NULL,
  `crc` varchar(100) NOT NULL,
  `data_crc` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `log_usuario` varchar(14) NOT NULL,
  `status` int(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


--
-- Estrutura da tabela `contribuinte`
--

/*
 @TODO - Criar tabela que relaciona Contribuinte com CNAE
  */

CREATE TABLE IF NOT EXISTS `gestor` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_pessoa` int(11) NOT NULL,
  `id_municipio` int(11) NOT NULL,
  `cargo` varchar(200) NOT NULL,
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `log_usuario` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `IDX_ID_PESSOA` (`id_pessoa`),
  KEY `FK_ID_MUNICIPIO` (`id_municipio`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `contribuinte` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_pessoa` int(11) NOT NULL,
  `ano_base` varchar(4) NOT NULL,
  `id_regime` int(10) NOT NULL DEFAULT '0',
  `data_inicio_regime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `inscricao_estadual` varchar(20) NOT NULL DEFAULT '0',
  `origem` int(1) NOT NULL COMMENT '1- Lote, 2- CRUD',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '0-Pendente, 1- Aprovado, 2- Não Aprovado',
  `cadastro_complementado` int(1) NOT NULL DEFAULT '0' COMMENT '0 -Não atualizado 1 -Atualizado',
  `data_inicio_atividade` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `log_usuario` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `IDX_ID_PESSOA` (`id_pessoa`),
  KEY `FK_ID_REGIME` (`id_regime`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



--
-- Estrutura da tabela `email`
--

CREATE TABLE IF NOT EXISTS `email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `conteudo` text NOT NULL,
  `assunto` varchar(200) NOT NULL,
  `destino` varchar(150) NOT NULL,
  `cc` text,
  `status` int(1) NOT NULL DEFAULT '0',
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `log_usuario` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



--
-- Estrutura da tabela `esqueci_minha_senha`
--

CREATE TABLE IF NOT EXISTS `esqueci_minha_senha` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(14) NOT NULL DEFAULT '0',
  `senha` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



--
-- Estrutura da tabela `estado`
--

CREATE TABLE IF NOT EXISTS `estado` (
  `id` varchar(2) NOT NULL,
  `codigo_prodesp` varchar(2) NOT NULL,
  `descri` varchar(150) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UQ_ID` (`id`),
  UNIQUE KEY `UQ_CODIGO_PRODESP` (`codigo_prodesp`),
  UNIQUE KEY `UQ_DESCRI` (`descri`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


--
-- Estrutura da tabela `lote_estado`
--

CREATE TABLE IF NOT EXISTS `lote_estado` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome_arquivo` varchar(200) NOT NULL,
  `data_inicio` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `data_fim` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `log_usuario` varchar(14) NOT NULL,
  `qtd_importados` int(11) unsigned NOT NULL DEFAULT '0',
  `qtd_total` int(11) unsigned NOT NULL DEFAULT '0',
  `qtd_nao_importados` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;




--
-- Estrutura da tabela `lote_estado_contribuinte`
--

CREATE TABLE IF NOT EXISTS `lote_estado_contribuinte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_lote` int(11) NOT NULL,
  `id_contribuinte` int(11) NOT NULL,
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `log_usuario` varchar(14) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



--
-- Estrutura da tabela `municipio`
--

CREATE TABLE IF NOT EXISTS `municipio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_orgao_estado` int(11) NOT NULL,
  `id_estado` varchar(2) NOT NULL,
  `descri` varchar(150) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UQ_ID` (`id`),
  UNIQUE KEY `UQ_CODIGO_ORGAO_ESTADO` (`codigo_orgao_estado`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


--
-- Estrutura da tabela `pessoa`
--

CREATE TABLE IF NOT EXISTS `pessoa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descri` varchar(200) DEFAULT '',
  `cpf_cnpj` varchar(14) NOT NULL DEFAULT '' COMMENT 'CPF/CNPJ nao pode ser Unique pois este campo pode se repetir em alguns casos como por exemplo no lote de Itapira de 2011',
  `cep` varchar(8) NULL DEFAULT '',
  `tipo_logradouro` varchar(30) NULL DEFAULT '',
  `logradouro` varchar(200) NULL DEFAULT '',
  `numero` int(11) DEFAULT NULL,
  `complemento` varchar(50) DEFAULT NULL,
  `bairro` varchar(150) NULL DEFAULT '',
  `id_municipio` int(11) NULL DEFAULT 0,
  `municipio` varchar(100) NULL DEFAULT '',
  `estado` varchar(2) NULL,
  `email` varchar(150) DEFAULT NULL,
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `log_usuario` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UQ_ID` (`id`),
  KEY `FK_ID_MUNICIPIO` (`id_municipio`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



--
-- Estrutura da tabela `regime`
--

CREATE TABLE IF NOT EXISTS `regime` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_orgao_estado` varchar(1) NOT NULL,
  `descri` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UQ_CODIGO_ORGAO_ESTADO` (`codigo_orgao_estado`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


--
-- Estrutura da tabela `usuario`
--

CREATE TABLE IF NOT EXISTS `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descri` varchar(14) NOT NULL DEFAULT '',
  `senha` varchar(32) NOT NULL DEFAULT '',
  `id_pessoa` int(11) NOT NULL,
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `log_usuario` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UQ_ID` (`id`),
  UNIQUE KEY `UQ_DESCRI` (`descri`),
  KEY `FK_ID_PESSOA` (`id_pessoa`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `cnae` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descri` varchar(150) NOT NULL DEFAULT '',
  `codigo` varchar(9) NOT NULL DEFAULT '',
  `codigo_ini` varchar(6) NOT NULL DEFAULT '',
  `codigo_fim`varchar(2) NOT NULL DEFAULT '',
  `status` int(1) DEFAULT NULL,
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `log_usuario` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UQ_ID` (`id`),
  UNIQUE KEY `UQ_DESCRI` (`descri`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;



create table if not exists `cfop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descri` text NOT NULL,
  `codigo` varchar(10) NOT NULL,
  `codigo_digito` tinyint NOT NULL,
  `codigo_fracao` smallint not null,
  `tipo_basico` tinyint not null COMMENT '1-Entrada, 2-Saída',
  `tipo` tinyint NOT NULL COMMENT '1-Dentro do estado, 2-Fora do estado, 3-Exterior',
  `computado_va` tinyint DEFAULT '0',
  `status` int(1) DEFAULT NULL COMMENT '0-Inativo, 1-Ativo',
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `log_usuario` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UQ_ID` (`id`),
  UNIQUE KEY `UQ_DESCRI` (`descri`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
