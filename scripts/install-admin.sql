-- Tabla de administradores Yofi + usuario inicial
-- Ejecutar: mysql -u root yofi < scripts/install-admin.sql

CREATE TABLE IF NOT EXISTS `tbl_admin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuadmin` varchar(50) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `publicado` int NOT NULL DEFAULT 1,
  `username` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuadmin` (`usuadmin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usuario: admin / admin123
INSERT INTO `tbl_admin` (`usuadmin`, `clave`, `publicado`, `username`, `password`, `email`)
SELECT 'admin',
       '$2y$10$nj21n/Y7sc5MyHqo15dkqemiHrSeNcVVO2PUFr3LHUwvZOUdUJYxq',
       1,
       'admin',
       '$2y$10$nj21n/Y7sc5MyHqo15dkqemiHrSeNcVVO2PUFr3LHUwvZOUdUJYxq',
       'admin@yofi.local'
WHERE NOT EXISTS (SELECT 1 FROM tbl_admin WHERE usuadmin = 'admin');
