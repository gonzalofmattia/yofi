-- Reserva de stock en checkout (Yofi)
-- Ejecutar una vez sobre la BD existente.

SET NAMES utf8mb4;

ALTER TABLE `tbl_skus`
  ADD COLUMN IF NOT EXISTS `stock_reservado` int NOT NULL DEFAULT 0 AFTER `stock`;

ALTER TABLE `tbl_ordenes`
  ADD COLUMN IF NOT EXISTS `reserva_expira_at` datetime DEFAULT NULL AFTER `fecha_actualizacion`,
  ADD COLUMN IF NOT EXISTS `reserva_activa` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=stock reservado pendiente de pago' AFTER `reserva_expira_at`;

-- Ampliar motivos del log de stock
ALTER TABLE `tbl_stock_log`
  MODIFY COLUMN `motivo` enum(
    'venta',
    'ajuste_manual',
    'carga_inicial',
    'carga_csv',
    'devolucion',
    'reserva',
    'liberacion_reserva'
  ) NOT NULL;

CREATE INDEX IF NOT EXISTS `idx_ordenes_reserva_expira`
  ON `tbl_ordenes` (`estado`, `reserva_activa`, `reserva_expira_at`);
