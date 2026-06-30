-- =============================================================================
-- Yofi — Schema inicial de base de datos
-- Ejecutar sobre una base MySQL/MariaDB vacía (utf8mb4)
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- --- CATEGORÍAS ---
CREATE TABLE `tbl_categorias` (
  `id_cate` int NOT NULL AUTO_INCREMENT,
  `id_cate_padre` int DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `descripcion` text,
  `imagen` varchar(250) DEFAULT NULL,
  `seo_title` varchar(250) DEFAULT NULL,
  `seo_description` text,
  `orden` int DEFAULT 0,
  `publicado` tinyint DEFAULT 1,
  `destacado_home` tinyint DEFAULT 0,
  PRIMARY KEY (`id_cate`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --- COLORES ---
CREATE TABLE `tbl_colores` (
  `id_color` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(60) NOT NULL,
  `hex_code` varchar(7) NOT NULL,
  `activo` tinyint DEFAULT 1,
  PRIMARY KEY (`id_color`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --- TALLES ---
CREATE TABLE `tbl_talles` (
  `id_talle` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(20) NOT NULL,
  `orden` int DEFAULT 0,
  `activo` tinyint DEFAULT 1,
  PRIMARY KEY (`id_talle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --- USUARIOS (copiado de Casa de Insecticidas) ---
CREATE TABLE `tbl_usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `direccion` text,
  `ciudad` varchar(100) DEFAULT NULL,
  `provincia` varchar(100) DEFAULT NULL,
  `codigo_postal` varchar(20) DEFAULT NULL,
  `dni` varchar(20) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `is_guest` tinyint(1) NOT NULL DEFAULT 1,
  `email_verificado` tinyint(1) NOT NULL DEFAULT 0,
  `token_verificacion` varchar(100) DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_ultimo_acceso` datetime DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_activo` (`activo`),
  KEY `idx_fecha_registro` (`fecha_registro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --- PRODUCTOS ---
CREATE TABLE `tbl_productos` (
  `id_prod` int NOT NULL AUTO_INCREMENT,
  `id_cate` int NOT NULL,
  `nombre` varchar(250) NOT NULL,
  `slug` varchar(270) NOT NULL,
  `codigo` varchar(30) NOT NULL,
  `precio_base` decimal(10,2) NOT NULL,
  `precio_oferta` decimal(10,2) DEFAULT NULL,
  `descripcion` text,
  `composicion` text,
  `cuidados` text,
  `peso` decimal(10,2) DEFAULT 0.00,
  `alto` decimal(10,2) DEFAULT 0.00,
  `ancho` decimal(10,2) DEFAULT 0.00,
  `profundidad` decimal(10,2) DEFAULT 0.00,
  `publicado` tinyint DEFAULT 1,
  `destacado` tinyint DEFAULT 0,
  `oferta` tinyint DEFAULT 0,
  `promo_badge` varchar(20) DEFAULT NULL,
  `borrado` tinyint DEFAULT 0,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_prod`),
  UNIQUE KEY `slug` (`slug`),
  KEY `id_cate` (`id_cate`),
  FOREIGN KEY (`id_cate`) REFERENCES `tbl_categorias` (`id_cate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --- SKUs (variantes) ---
CREATE TABLE `tbl_skus` (
  `id_sku` int NOT NULL AUTO_INCREMENT,
  `id_prod` int NOT NULL,
  `id_color` int NOT NULL,
  `id_talle` int NOT NULL,
  `codigo_sku` varchar(60) NOT NULL,
  `stock` int NOT NULL DEFAULT 0,
  `stock_reservado` int NOT NULL DEFAULT 0,
  `precio_extra` decimal(10,2) DEFAULT 0.00,
  `activo` tinyint DEFAULT 1,
  PRIMARY KEY (`id_sku`),
  UNIQUE KEY `variante_unica` (`id_prod`,`id_color`,`id_talle`),
  FOREIGN KEY (`id_prod`) REFERENCES `tbl_productos` (`id_prod`),
  FOREIGN KEY (`id_color`) REFERENCES `tbl_colores` (`id_color`),
  FOREIGN KEY (`id_talle`) REFERENCES `tbl_talles` (`id_talle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --- IMÁGENES DE PRODUCTOS ---
CREATE TABLE `tbl_prod_imagenes` (
  `id_imagen` int NOT NULL AUTO_INCREMENT,
  `id_prod` int NOT NULL,
  `id_color` int DEFAULT NULL,
  `path` varchar(250) NOT NULL,
  `orden` int DEFAULT 0,
  `es_principal` tinyint DEFAULT 0,
  PRIMARY KEY (`id_imagen`),
  FOREIGN KEY (`id_prod`) REFERENCES `tbl_productos` (`id_prod`),
  FOREIGN KEY (`id_color`) REFERENCES `tbl_colores` (`id_color`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --- MERCADO PAGO: PREFERENCIAS (copiado de Casa de Insecticidas) ---
CREATE TABLE `tbl_mp_preferences` (
  `id` int NOT NULL AUTO_INCREMENT,
  `preference_id` varchar(255) NOT NULL,
  `items` text NOT NULL,
  `shipping_info` text,
  `status` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `preference_id` (`preference_id`),
  KEY `idx_preference_id` (`preference_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --- ÓRDENES ---
CREATE TABLE `tbl_ordenes` (
  `id_orden` int NOT NULL AUTO_INCREMENT,
  `numero_orden` varchar(50) NOT NULL,
  `estado` varchar(50) NOT NULL DEFAULT 'pendiente',
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `usuario_id` int DEFAULT NULL,
  `telefono` varchar(50) NOT NULL,
  `direccion` text NOT NULL,
  `ciudad` varchar(100) NOT NULL,
  `provincia` varchar(100) NOT NULL,
  `codigo_postal` varchar(20) NOT NULL,
  `notas` text,
  `metodo_pago` varchar(50) NOT NULL,
  `shipping_method_code` varchar(50) DEFAULT NULL,
  `shipping_carrier` varchar(100) DEFAULT NULL,
  `shipping_eta` varchar(100) DEFAULT NULL,
  `shipping_meta` json DEFAULT NULL,
  `zipnova_shipment_id` varchar(100) DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `envio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `items` json NOT NULL,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `reserva_expira_at` datetime DEFAULT NULL,
  `reserva_activa` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=stock reservado pendiente de pago',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_orden`),
  UNIQUE KEY `numero_orden` (`numero_orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --- MERCADO PAGO: PAGOS (copiado de Casa de Insecticidas) ---
CREATE TABLE `tbl_mp_payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `payment_id` varchar(255) NOT NULL,
  `preference_id` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL,
  `status_detail` varchar(100) DEFAULT NULL,
  `payment_type` varchar(50) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_id` (`payment_id`),
  KEY `idx_payment_id` (`payment_id`),
  KEY `idx_preference_id` (`preference_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `tbl_mp_payments_ibfk_1` FOREIGN KEY (`preference_id`) REFERENCES `tbl_mp_preferences` (`preference_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --- HISTORIAL DE ESTADOS DE ÓRDENES (copiado de Casa de Insecticidas) ---
CREATE TABLE `tbl_ordenes_historial` (
  `id_historial` int NOT NULL AUTO_INCREMENT,
  `id_orden` int NOT NULL,
  `estado_anterior` varchar(50) DEFAULT NULL,
  `estado_nuevo` varchar(50) NOT NULL,
  `fecha_cambio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_admin` varchar(100) DEFAULT NULL COMMENT 'Usuario del admin que hizo el cambio',
  `notas` text COMMENT 'Notas o comentarios sobre el cambio',
  `tracking_number` varchar(100) DEFAULT NULL COMMENT 'Número de seguimiento si aplica',
  `motivo_cancelacion` text COMMENT 'Motivo si se canceló',
  PRIMARY KEY (`id_historial`),
  KEY `idx_id_orden` (`id_orden`),
  KEY `idx_estado_nuevo` (`estado_nuevo`),
  KEY `idx_fecha_cambio` (`fecha_cambio`),
  CONSTRAINT `tbl_ordenes_historial_ibfk_1` FOREIGN KEY (`id_orden`) REFERENCES `tbl_ordenes` (`id_orden`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --- AUDITORÍA DE ÓRDENES (copiado de Casa de Insecticidas) ---
CREATE TABLE `tbl_ordenes_audit` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_orden` int NOT NULL,
  `evento` varchar(50) NOT NULL,
  `usuario_admin` varchar(100) DEFAULT NULL,
  `ip_origen` varchar(45) DEFAULT NULL,
  `payload_json` longtext,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_orden_evento` (`id_orden`,`evento`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --- LOG DE STOCK (copiado de Casa de Insecticidas) ---
CREATE TABLE `tbl_stock_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `producto_id` int NOT NULL COMMENT 'Ref. lógica: tbl_productos.id_prod',
  `cantidad_anterior` int NOT NULL,
  `cantidad_nueva` int NOT NULL,
  `diferencia` int NOT NULL COMMENT 'Positivo ingreso, negativo egreso',
  `motivo` enum('venta','ajuste_manual','carga_inicial','carga_csv','devolucion','reserva','liberacion_reserva') NOT NULL,
  `orden_id` int DEFAULT NULL COMMENT 'Ref. lógica: tbl_ordenes.id_orden (ventas)',
  `usuario_admin` varchar(100) DEFAULT NULL,
  `nota` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_stock_log_producto` (`producto_id`),
  KEY `idx_stock_log_orden` (`orden_id`),
  KEY `idx_stock_log_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --- SESIONES PHP (copiado de Casa de Insecticidas) ---
CREATE TABLE `tbl_sessions` (
  `id` varchar(128) NOT NULL,
  `data` mediumblob NOT NULL,
  `last_access` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_last_access` (`last_access`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Sesiones PHP almacenadas en BD';

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================================
-- DATOS INICIALES
-- =============================================================================

INSERT INTO `tbl_talles` (`nombre`, `orden`) VALUES
('0-3M',1),('3-6M',2),('6-12M',3),
('1A',4),('2A',5),('3A',6),('4A',7),
('6A',8),('8A',9),('10A',10),('12A',11),
('Único',99);

INSERT INTO `tbl_colores` (`nombre`, `hex_code`) VALUES
('Blanco','#FFFFFF'),('Negro','#1A1A1A'),('Rosa','#F4A7B9'),
('Celeste','#96AFC8'),('Rojo','#E1644B'),('Verde','#7D9B6E'),
('Beige','#FAE1C8'),('Lila','#C4A8D4'),('Naranja','#FAAF7D'),
('Amarillo','#F9E784');

INSERT INTO `tbl_categorias` (`nombre`,`slug`,`descripcion`,`orden`) VALUES
('Abrigos','abrigos','Abrigos, camperas y abrigos',1),
('Buzos y Cardigans','buzos','Buzos, sweaters y cardigans',2),
('Pantalones','pantalones','Pantalones y joggers',3),
('Remeras','remeras','Remeras y tops',4),
('Vestidos','vestidos','Vestidos y polleras',5),
('Accesorios','accesorios',NULL,6),
('Calzado','calzado',NULL,7);

-- 2026-06-16 — Schema inicial Yofi v1.0

-- =============================================================================
-- MIGRACIÓN NUEVA (2026-06-22) — Config operativa de envíos y métodos de pago
-- Sin secretos de API. Equivalente al patrón de Casa de Insecticidas adaptado a Zipnova.
-- =============================================================================
CREATE TABLE IF NOT EXISTS `tbl_shipping_config` (
  `id` int NOT NULL AUTO_INCREMENT,
  `clave` varchar(100) NOT NULL,
  `valor` text NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clave` (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `tbl_shipping_config` (`clave`, `valor`, `descripcion`) VALUES
  ('zipnova_enabled', '1', 'Si el envío vía Zipnova está activo en el checkout'),
  ('zipnova_label', 'Envío a domicilio', 'Texto visible para el cliente'),
  ('zipnova_eta_default', '3 a 5 días hábiles', 'ETA mostrado cuando la API no devuelve uno específico'),
  ('free_shipping_threshold', '0', 'Monto mínimo de compra para envío gratis (0 = desactivado)'),
  ('pickup_enabled', '0', 'Si retiro en local está habilitado'),
  ('pickup_label', 'Retiro en local', 'Texto visible para el cliente'),
  ('pickup_address', '', 'Dirección de retiro mostrada al cliente');

CREATE TABLE IF NOT EXISTS `tbl_metodos_pago` (
  `id_metodo` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `activo` tinyint DEFAULT 1,
  `orden` int DEFAULT 0,
  PRIMARY KEY (`id_metodo`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `tbl_metodos_pago` (`codigo`, `nombre`, `descripcion`, `activo`, `orden`) VALUES
  ('mercadopago', 'Mercado Pago', 'Tarjetas, transferencia y dinero en cuenta vía Mercado Pago', 1, 1);

-- =============================================================================
-- MIGRACIÓN NUEVA (2026-06-22) — Slider hero, banners secundarios, config empresa
-- =============================================================================

-- Slider de imágenes del hero (sin texto, solo imagen + link opcional)
CREATE TABLE IF NOT EXISTS `tbl_slider` (
  `id_slide` int NOT NULL AUTO_INCREMENT,
  `imagen` varchar(250) NOT NULL,
  `imagen_mobile` varchar(250) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `orden` int DEFAULT 0,
  `activo` tinyint DEFAULT 1,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_slide`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Banners secundarios del home (imagen + textos superpuestos editables)
CREATE TABLE IF NOT EXISTS `tbl_banners` (
  `id_banner` int NOT NULL AUTO_INCREMENT,
  `eyebrow` varchar(150) DEFAULT NULL,
  `titulo` varchar(150) DEFAULT NULL,
  `subtitulo` varchar(150) DEFAULT NULL,
  `texto_boton` varchar(100) DEFAULT NULL,
  `imagen` varchar(250) NOT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `posicion` varchar(50) DEFAULT NULL,
  `orden` int DEFAULT 0,
  `activo` tinyint DEFAULT 1,
  PRIMARY KEY (`id_banner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Banners de edad del home (MINI, 1 a 4, 4 a 12)
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

INSERT INTO `tbl_home_edad_banners` (`slug`, `titulo`, `link_url`, `orden`, `activo`) VALUES
  ('mini', 'MINI', 'index.php?p=catalogo&edad=mini', 1, 1),
  ('1-a-4', '1 A 4 AÑOS', 'index.php?p=catalogo&edad=1-a-4', 2, 1),
  ('4-a-12', '4 A 12 AÑOS', 'index.php?p=catalogo&edad=4-a-12', 3, 1);

-- Datos generales de la empresa (clave-valor)
CREATE TABLE IF NOT EXISTS `tbl_config_empresa` (
  `clave` varchar(100) NOT NULL,
  `valor` text,
  PRIMARY KEY (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `tbl_config_empresa` (`clave`, `valor`) VALUES
  ('whatsapp', ''),
  ('email_contacto', ''),
  ('telefono', ''),
  ('direccion', ''),
  ('instagram', ''),
  ('facebook', ''),
  ('horario_atencion', '');

-- Slide inicial: misma imagen que el hero hardcodeado actual
INSERT INTO `tbl_slider` (`imagen`, `orden`, `activo`) VALUES
  ('hero-principal.jpg', 1, 1);

-- Banner inicial: campaign banner hardcodeado en home.php (líneas 126-147)
INSERT INTO `tbl_banners` (`eyebrow`, `titulo`, `subtitulo`, `texto_boton`, `imagen`, `link_url`, `posicion`, `orden`, `activo`) VALUES
  ('Solo por tiempo limitado', '3 x 2', 'EN SELECCIONADOS', 'COMPRAR', 'banner-3x2.jpg', 'index.php?p=catalogo&categoria=ofertas', 'home_secundario', 1, 1);

-- =============================================================================
-- MIGRACIÓN NUEVA (2026-06-23) — Mi cuenta: direcciones, wishlist, password tokens
-- Ver también scripts/migrate-cuenta-wishlist.sql
-- =============================================================================

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

-- =============================================================================
-- MIGRACIÓN NUEVA (2026-06-30) — Imagen mobile opcional en slider hero
-- Ver también scripts/migrate-slider-imagen-mobile.sql
-- =============================================================================

-- ALTER TABLE `tbl_slider`
--   ADD COLUMN `imagen_mobile` varchar(250) DEFAULT NULL
--   AFTER `imagen`;
