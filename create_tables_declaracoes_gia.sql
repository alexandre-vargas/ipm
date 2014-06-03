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

CREATE TABLE IF NOT EXISTS `declaracao_gia_detalhes_cfops` (
  `id` int not null auto_increment,
  `novo_nome_arquivo` varchar(150) NOT NULL,

  `nro_gia` int null,
  `cfop` varchar(10),
  `valor_contabil` numeric(19,4),
  `base_calculo` numeric(19,4),
  `imposto` numeric(19,4),
  `isentas_nao_trib` numeric(19,4),
  `outras` numeric(19,4),
  `imposto_retido_st` numeric(19,4),
  `imp_ret_substituto_st` numeric(19,4),
  `imp_ret_substituido` numeric(19,4),
  `outros_impostos` numeric(19,4),
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY(`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ;

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
