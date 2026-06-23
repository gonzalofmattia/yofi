-- =============================================================================
-- Yofi — Mi cuenta + wishlist + recuperación de contraseña
-- Ejecutar sobre base yofi existente (después de crear-db.sql)
-- =============================================================================

SET NAMES utf8mb4;

-- Direcciones guardadas (múltiples por usuario)
CREATE TABLE IF NOT EXISTS `tbl_usuarios_direcciones` (
  `id_direccion` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `calle` varchar(150) NOT NULL,
  `numero` varchar(20) NOT NULL DEFAULT '',
  `depto` varchar(30) DEFAULT NULL,
  `ciudad` varchar(100) NOT NULL,
  `provincia` varchar(100) NOT NULL,
  `cp` varchar(20) NOT NULL,
  `predeterminada` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_direccion`),
  KEY `idx_usuario_direcciones` (`usuario_id`),
  CONSTRAINT `fk_usuario_direcciones_usuario`
    FOREIGN KEY (`usuario_id`) REFERENCES `tbl_usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Lista de deseos (producto padre por usuario)
CREATE TABLE IF NOT EXISTS `tbl_wishlist` (
  `id_wishlist` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `producto_id` int NOT NULL,
  `fecha_agregado` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_wishlist`),
  UNIQUE KEY `uq_wishlist_usuario_producto` (`usuario_id`, `producto_id`),
  KEY `idx_wishlist_usuario` (`usuario_id`),
  KEY `idx_wishlist_producto` (`producto_id`),
  CONSTRAINT `fk_wishlist_usuario`
    FOREIGN KEY (`usuario_id`) REFERENCES `tbl_usuarios` (`id_usuario`) ON DELETE CASCADE,
  CONSTRAINT `fk_wishlist_producto`
    FOREIGN KEY (`producto_id`) REFERENCES `tbl_productos` (`id_prod`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Magic links de contraseña (mismo esquema que Casa de Insecticidas)
CREATE TABLE IF NOT EXISTS `tbl_password_tokens` (
  `id_token` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_token`),
  UNIQUE KEY `token` (`token`),
  KEY `idx_token` (`token`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_expires_at` (`expires_at`),
  KEY `idx_used` (`used`),
  KEY `idx_usuario_used` (`usuario_id`, `used`),
  CONSTRAINT `tbl_password_tokens_ibfk_1`
    FOREIGN KEY (`usuario_id`) REFERENCES `tbl_usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
