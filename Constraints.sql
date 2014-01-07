--
-- Restrições para a tabela `componente`
--
ALTER TABLE `componente`
  ADD CONSTRAINT `FK_COMPONENTE_ESTADO` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para a tabela `contribuinte`
--
ALTER TABLE `contribuinte`
  ADD CONSTRAINT `FK_CONTRIBUINTE_PESSOA` FOREIGN KEY (`id_pessoa`) REFERENCES `pessoa` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_CONTRIBUINTE_REGIME` FOREIGN KEY (`id_regime`) REFERENCES `regime` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `contador_contribuinte`
  ADD CONSTRAINT `FK_CONTADOR_CONTRIBUINTE_CONTRIBUINTE` FOREIGN KEY (`id_contribuinte`) REFERENCES `contribuinte` (`id`),
  ADD CONSTRAINT `FK_CONTADOR_CONTRIBUINTE_CONTADOR` FOREIGN KEY (`id_contador`) REFERENCES `contador` (`id`);
  
--
-- Restrições para a tabela `municipio`
--
ALTER TABLE `municipio`
  ADD CONSTRAINT `FK_MUNICIPIO_ESTADO` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para a tabela `pessoa`
--
ALTER TABLE `pessoa`
  ADD CONSTRAINT `FK_PESSOA_MUNICIPIO` FOREIGN KEY (`id_municipio`) REFERENCES `municipio` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para a tabela `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `FK_USUARIO_PESSOA` FOREIGN KEY (`id_pessoa`) REFERENCES `pessoa` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

  
ALTER TABLE `sigicms_itapira`.`esqueci_minha_senha` 
  ADD CONSTRAINT `FK_ESQUECIMINHASENHA_USUARIO`
  FOREIGN KEY (`id_usuario` )
  REFERENCES `sigicms_itapira`.`usuario` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `FK_ESQUECIMINHASENHA_USUARIO_idx` (`id_usuario` ASC) ;  
