-- Yofi — Migración: de 1 producto por color → 1 producto padre con variantes de color
-- Ejecutar UNA sola vez sobre la base `yofi` después del seed WhatsApp (patrón Mimo).
-- No altera el schema: tbl_skus + tbl_prod_imagenes ya soportan id_prod + id_color.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

UPDATE tbl_productos SET nombre = 'Infinito tejido', slug = 'infinito-tejido', codigo = 'YF-MINI-INF'
WHERE id_prod = 7;

UPDATE tbl_skus SET id_prod = 7 WHERE id_prod IN (8, 9, 10);
UPDATE tbl_prod_imagenes SET id_prod = 7 WHERE id_prod IN (8, 9, 10);
DELETE FROM tbl_productos WHERE id_prod IN (8, 9, 10);

UPDATE tbl_productos SET nombre = 'Bandana tejida', slug = 'bandana-tejida', codigo = 'YF-MINI-BAN'
WHERE id_prod = 11;

UPDATE tbl_skus SET id_prod = 11 WHERE id_prod = 12;
UPDATE tbl_prod_imagenes SET id_prod = 11 WHERE id_prod = 12;
DELETE FROM tbl_productos WHERE id_prod = 12;

UPDATE tbl_productos SET nombre = 'Gorro aspen', slug = 'gorro-aspen', codigo = 'YF-MINI-ASP'
WHERE id_prod = 13;

UPDATE tbl_skus SET id_prod = 13 WHERE id_prod IN (14, 15);
UPDATE tbl_prod_imagenes SET id_prod = 13 WHERE id_prod IN (14, 15);
DELETE FROM tbl_productos WHERE id_prod IN (14, 15);

SET FOREIGN_KEY_CHECKS = 1;

-- 2026-06-22 — Migración agrupación colores catálogo WhatsApp
