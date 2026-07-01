-- =============================================================================
-- Yofi — Login por código de un solo uso (OTP) enviado por email
-- Ejecutar sobre base yofi existente (después de crear-db.sql y migrate-cuenta-wishlist.sql)
-- =============================================================================

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `tbl_login_otp` (
  `id_otp` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `code_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `attempts` int NOT NULL DEFAULT 0,
  `consumed_at` datetime DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_otp`),
  KEY `idx_login_otp_usuario` (`usuario_id`),
  KEY `idx_login_otp_expires` (`expires_at`),
  CONSTRAINT `fk_login_otp_usuario`
    FOREIGN KEY (`usuario_id`) REFERENCES `tbl_usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
