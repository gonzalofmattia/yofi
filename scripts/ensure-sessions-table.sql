-- Ejecutar en phpMyAdmin si el sitio da HTTP 500 tras importar el dump.
-- tbl_sessions se excluye del dump (sin datos locales) pero la estructura es obligatoria.

CREATE TABLE IF NOT EXISTS `tbl_sessions` (
  `id` varchar(128) NOT NULL,
  `data` mediumblob NOT NULL,
  `last_access` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_last_access` (`last_access`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Sesiones PHP almacenadas en BD';
