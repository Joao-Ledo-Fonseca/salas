-- Migração incremental da base de dados sgreserva
-- Aplicar a BDs existentes. Seguro para reexecução (IF NOT EXISTS / verificação).

USE `sgreserva`;

-- 1. Índices em reserva (performance)
CREATE INDEX IF NOT EXISTS `IDX_reserva_usuario_dia` ON `reserva` (`usuario_id`, `dia`);
CREATE INDEX IF NOT EXISTS `IDX_reserva_sala_dia` ON `reserva` (`sala_id`, `dia`);

-- 2. Email único (completo) - remover o índice com prefixo e criar único completo
-- (ignorar erro se já existir)
ALTER TABLE `usuario` DROP INDEX `UK_usuario_email`;
ALTER TABLE `usuario` ADD UNIQUE KEY `UK_usuario_email` (`email`);

-- 3. FKs com ON DELETE/UPDATE (recriar constraints)
-- reserva.sala_id
ALTER TABLE `reserva` DROP FOREIGN KEY `FK_reserva_sala_id`;
ALTER TABLE `reserva` ADD CONSTRAINT `FK_reserva_sala_id`
    FOREIGN KEY (`sala_id`) REFERENCES `sala` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- reserva.periodo_id
ALTER TABLE `reserva` DROP FOREIGN KEY `FK_reserva_periodo_id`;
ALTER TABLE `reserva` ADD CONSTRAINT `FK_reserva_periodo_id`
    FOREIGN KEY (`periodo_id`) REFERENCES `periodo` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- reserva.usuario_id
ALTER TABLE `reserva` DROP FOREIGN KEY `FK_reserva_usuario_id`;
ALTER TABLE `reserva` ADD CONSTRAINT `FK_reserva_usuario_id`
    FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- reserva.uso_id (se existir)
ALTER TABLE `reserva` DROP FOREIGN KEY `FK_reserva_uso_id`;
ALTER TABLE `reserva` ADD CONSTRAINT `FK_reserva_uso_id`
    FOREIGN KEY (`uso_id`) REFERENCES `uso` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
