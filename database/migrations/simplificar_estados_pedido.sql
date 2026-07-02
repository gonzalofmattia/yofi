-- =============================================================================
-- Yofi — Simplificación de estados de pedido a 5 valores
-- pendiente -> confirmado -> enviado -> entregado -> cancelado
--
-- Mapeo de estados viejos a nuevos:
--   en_preparacion   -> confirmado
--   preparando_envio -> confirmado
--   (pendiente, confirmado, enviado, entregado, cancelado ya coinciden)
--
-- NO se toca tbl_ordenes_historial: es un log de auditoría de lo que pasó
-- en su momento (columnas estado_anterior/estado_nuevo). Reescribir esos
-- valores sería alterar el registro histórico, no simplificar el sistema.
-- Solo se actualiza el estado VIVO de cada orden en tbl_ordenes.
--
-- Ejecutar manualmente contra la base yofi. NO se ejecuta automáticamente.
-- =============================================================================

SET NAMES utf8mb4;

UPDATE tbl_ordenes
SET estado = 'confirmado'
WHERE estado IN ('en_preparacion', 'preparando_envio');
