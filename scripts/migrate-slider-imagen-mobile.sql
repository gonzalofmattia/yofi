-- MIGRACIÓN (2026-06-30) — Imagen mobile opcional en slider hero
ALTER TABLE `tbl_slider`
  ADD COLUMN `imagen_mobile` varchar(250) DEFAULT NULL
  AFTER `imagen`;
