-- Yofi — Banners de edad editables + categorías destacadas en home
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `tbl_home_edad_banners` (
  `id_edad_banner` int NOT NULL AUTO_INCREMENT,
  `slug` varchar(20) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `subtitulo` varchar(255) DEFAULT NULL,
  `imagen` varchar(250) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `orden` int DEFAULT 0,
  `activo` tinyint DEFAULT 1,
  PRIMARY KEY (`id_edad_banner`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `tbl_home_edad_banners` (`slug`, `titulo`, `link_url`, `orden`, `activo`)
SELECT 'mini', 'MINI', 'index.php?p=catalogo&edad=mini', 1, 1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM tbl_home_edad_banners WHERE slug = 'mini');

INSERT INTO `tbl_home_edad_banners` (`slug`, `titulo`, `link_url`, `orden`, `activo`)
SELECT '1-a-4', '1 A 4 AÑOS', 'index.php?p=catalogo&edad=1-a-4', 2, 1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM tbl_home_edad_banners WHERE slug = '1-a-4');

INSERT INTO `tbl_home_edad_banners` (`slug`, `titulo`, `link_url`, `orden`, `activo`)
SELECT '4-a-12', '4 A 12 AÑOS', 'index.php?p=catalogo&edad=4-a-12', 3, 1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM tbl_home_edad_banners WHERE slug = '4-a-12');

SET @col_exists := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tbl_categorias'
    AND COLUMN_NAME = 'destacado_home'
);
SET @sql := IF(
  @col_exists = 0,
  'ALTER TABLE tbl_categorias ADD COLUMN destacado_home tinyint DEFAULT 0 AFTER publicado',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

UPDATE tbl_categorias SET destacado_home = 1 WHERE slug IN ('abrigos', 'buzos', 'pantalones', 'remeras', 'vestidos');
