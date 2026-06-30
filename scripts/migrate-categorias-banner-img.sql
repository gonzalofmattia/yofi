-- MIGRACIÓN (2026-06-30) — Banner propio para la página de categoría
ALTER TABLE `tbl_categorias`
  ADD COLUMN `banner_img` varchar(250) DEFAULT NULL
  AFTER `imagen`;
