-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 30-06-2026 a las 19:34:56
-- Versión del servidor: 8.4.8-8
-- Versión de PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `a0141154_yofi`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `id` int NOT NULL,
  `usuadmin` varchar(50) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `publicado` int NOT NULL DEFAULT '1',
  `username` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tbl_admin`
--

INSERT INTO `tbl_admin` (`id`, `usuadmin`, `clave`, `publicado`, `username`, `password`, `email`) VALUES
(1, 'admin', '$2y$10$vBAYTZx3aBOCkgLEfolXluBl.8AuDxjZuVJFHLoMlWgWqVvHDZaJm', 1, 'admin', '$2y$10$vBAYTZx3aBOCkgLEfolXluBl.8AuDxjZuVJFHLoMlWgWqVvHDZaJm', 'admin@yofi.com.ar');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_banners`
--

CREATE TABLE `tbl_banners` (
  `id_banner` int NOT NULL,
  `eyebrow` varchar(150) DEFAULT NULL,
  `titulo` varchar(150) DEFAULT NULL,
  `subtitulo` varchar(150) DEFAULT NULL,
  `texto_boton` varchar(100) DEFAULT NULL,
  `imagen` varchar(250) NOT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `posicion` varchar(50) DEFAULT NULL,
  `orden` int DEFAULT '0',
  `activo` tinyint DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tbl_banners`
--

INSERT INTO `tbl_banners` (`id_banner`, `eyebrow`, `titulo`, `subtitulo`, `texto_boton`, `imagen`, `link_url`, `posicion`, `orden`, `activo`) VALUES
(1, 'Solo por tiempo limitado', '2X1', 'EN  PRODUCTOS SELECCIONADOS', 'COMPRAR', 'banner-3x2.jpg', 'index.php?p=catalogo&categoria=ofertas', 'home_secundario', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_categorias`
--

CREATE TABLE `tbl_categorias` (
  `id_cate` int NOT NULL,
  `id_cate_padre` int DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `descripcion` text,
  `imagen` varchar(250) DEFAULT NULL,
  `seo_title` varchar(250) DEFAULT NULL,
  `seo_description` text,
  `orden` int DEFAULT '0',
  `publicado` tinyint DEFAULT '1',
  `destacado_home` tinyint DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tbl_categorias`
--

INSERT INTO `tbl_categorias` (`id_cate`, `id_cate_padre`, `nombre`, `slug`, `descripcion`, `imagen`, `seo_title`, `seo_description`, `orden`, `publicado`, `destacado_home`) VALUES
(1, NULL, 'Abrigos', 'abrigos', 'Abrigos, camperas y abrigos', 'categoria-1782176049.png', NULL, NULL, 1, 1, 1),
(2, NULL, 'Buzos y Cardigans', 'buzos-y-cardigans', 'Buzos, sweaters y cardigans', 'categoria-1782176920.png', NULL, NULL, 2, 1, 1),
(3, NULL, 'Pantalones', 'pantalones', 'Pantalones y joggers', 'categoria-1782176603.png', NULL, NULL, 3, 1, 1),
(4, NULL, 'Accesorios', 'accesorios', '', 'categoria-1782176793.png', NULL, NULL, 6, 1, 1),
(7, NULL, 'Remeras', 'remeras', 'Remeras y tops', 'categoria-1782176510.png', NULL, NULL, 4, 1, 1),
(9, NULL, 'Mini Ánima Invierno', 'mini-nima-invierno', 'Accesorios tejidos de invierno Mini Ánima', 'categoria-1782175186.png', NULL, NULL, 8, 1, 1),
(10, NULL, 'Regalos', 'regalos', 'Regalos y productos especiales', 'categoria-1782175349.png', NULL, NULL, 9, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_colores`
--

CREATE TABLE `tbl_colores` (
  `id_color` int NOT NULL,
  `nombre` varchar(60) NOT NULL,
  `hex_code` varchar(7) NOT NULL,
  `activo` tinyint DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tbl_colores`
--

INSERT INTO `tbl_colores` (`id_color`, `nombre`, `hex_code`, `activo`) VALUES
(1, 'Blanco', '#FFFFFF', 1),
(2, 'Negro', '#1A1A1A', 1),
(3, 'Rosa', '#F4A7B9', 1),
(4, 'Celeste', '#96AFC8', 1),
(5, 'Rojo', '#E1644B', 1),
(6, 'Verde', '#7D9B6E', 1),
(7, 'Beige', '#FAE1C8', 1),
(8, 'Lila', '#C4A8D4', 1),
(9, 'Naranja', '#FAAF7D', 1),
(10, 'Amarillo', '#F9E784', 1),
(11, 'Bordó', '#722F37', 1),
(12, 'Gris', '#9E9E9E', 1),
(13, 'Azul', '#4A6FA5', 1),
(14, 'Natural', '#E8DCC8', 1),
(15, 'Verde antiguo', '#6B7F5E', 1),
(16, 'Chocolate', '#5C4033', 1),
(17, 'Gris topo', '#8B8680', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_config_empresa`
--

CREATE TABLE `tbl_config_empresa` (
  `clave` varchar(100) NOT NULL,
  `valor` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tbl_config_empresa`
--

INSERT INTO `tbl_config_empresa` (`clave`, `valor`) VALUES
('direccion', 'Once - Ciudad Autónoma de Buenos Aires'),
('email_contacto', 'hola@yofi.com.ar'),
('facebook', ''),
('horario_atencion', ''),
('instagram', 'https://www.instagram.com/batia.valls/'),
('telefono', '+54 9 11 2527-9502'),
('whatsapp', '+54 9 11 2527-9502');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_home_edad_banners`
--

CREATE TABLE `tbl_home_edad_banners` (
  `id_edad_banner` int NOT NULL,
  `slug` varchar(20) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `subtitulo` varchar(255) DEFAULT NULL,
  `imagen` varchar(250) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `orden` int DEFAULT '0',
  `activo` tinyint DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tbl_home_edad_banners`
--

INSERT INTO `tbl_home_edad_banners` (`id_edad_banner`, `slug`, `titulo`, `subtitulo`, `imagen`, `link_url`, `orden`, `activo`) VALUES
(1, 'mini', 'MINI', NULL, 'edad-banner-1782175472.png', 'index.php?p=catalogo&edad=mini', 1, 1),
(2, '1-a-4', '1 A 4 AÑOS', NULL, 'edad-banner-1782175547.png', 'index.php?p=catalogo&edad=1-a-4', 2, 1),
(3, '4-a-12', '4 A 12 AÑOS', NULL, 'edad-banner-1782175702.png', 'index.php?p=catalogo&edad=4-a-12', 3, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_metodos_pago`
--

CREATE TABLE `tbl_metodos_pago` (
  `id_metodo` int NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `activo` tinyint DEFAULT '1',
  `orden` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tbl_metodos_pago`
--

INSERT INTO `tbl_metodos_pago` (`id_metodo`, `codigo`, `nombre`, `descripcion`, `activo`, `orden`) VALUES
(1, 'mercadopago', 'Mercado Pago', 'Tarjetas, transferencia y dinero en cuenta vía Mercado Pago', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_mp_payments`
--

CREATE TABLE `tbl_mp_payments` (
  `id` int NOT NULL,
  `payment_id` varchar(255) NOT NULL,
  `preference_id` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL,
  `status_detail` varchar(100) DEFAULT NULL,
  `payment_type` varchar(50) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tbl_mp_payments`
--

INSERT INTO `tbl_mp_payments` (`id`, `payment_id`, `preference_id`, `status`, `status_detail`, `payment_type`, `payment_method`, `amount`, `created_at`, `updated_at`) VALUES
(1, '165478695282', '3494486768-1a49478c-8727-4670-8cdb-a789363a12b3', 'approved', 'accredited', 'credit_card', 'master', 496006.00, '2026-06-23 15:05:27', '2026-06-23 15:06:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_mp_preferences`
--

CREATE TABLE `tbl_mp_preferences` (
  `id` int NOT NULL,
  `preference_id` varchar(255) NOT NULL,
  `items` text NOT NULL,
  `shipping_info` text,
  `status` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tbl_mp_preferences`
--

INSERT INTO `tbl_mp_preferences` (`id`, `preference_id`, `items`, `shipping_info`, `status`, `created_at`, `updated_at`) VALUES
(1, '3494486768-a5e27ae8-b050-496e-a6ae-0e8362b9a193', '[{\"id\":\"184\",\"title\":\"Delantal vintage sin bordar — Verde antiguo Único\",\"quantity\":1,\"unit_price\":40000,\"currency_id\":\"ARS\"},{\"id\":\"shipping\",\"title\":\"Envío\",\"quantity\":1,\"unit_price\":491641,\"currency_id\":\"ARS\"}]', '{\"order_id\":14,\"numero_orden\":\"ORD-20260623-77204D\",\"customer\":{\"email\":\"gonzalo.mattia@hotmail.com\",\"firstName\":\"gonzalo\",\"lastName\":\"mattia\"},\"shipping\":{\"address\":\"Fred Aden 406\",\"city\":\"Villa Sarmiento\",\"province\":\"Buenos Aires\",\"zip\":\"1706\",\"notes\":\"\"},\"mp_currency\":\"ARS\"}', 'pending', '2026-06-23 15:01:28', NULL),
(2, '3494486768-1a49478c-8727-4670-8cdb-a789363a12b3', '[{\"id\":\"153\",\"title\":\"Bandana tejida — Chocolate Único\",\"quantity\":1,\"unit_price\":5000,\"currency_id\":\"ARS\"},{\"id\":\"shipping\",\"title\":\"Envío\",\"quantity\":1,\"unit_price\":491006,\"currency_id\":\"ARS\"}]', '{\"order_id\":15,\"numero_orden\":\"ORD-20260623-82508A\",\"customer\":{\"email\":\"gonzalo.mattia@hotmail.com\",\"firstName\":\"gonzalo\",\"lastName\":\"mattia\"},\"shipping\":{\"address\":\"Fred Aden 406\",\"city\":\"Villa Sarmiento\",\"province\":\"Buenos Aires\",\"zip\":\"1706\",\"notes\":\"\"},\"mp_currency\":\"ARS\"}', 'confirmado', '2026-06-23 15:03:53', '2026-06-23 15:06:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_ordenes`
--

CREATE TABLE `tbl_ordenes` (
  `id_orden` int NOT NULL,
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
  `envio` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL,
  `items` json NOT NULL,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `reserva_expira_at` datetime DEFAULT NULL,
  `reserva_activa` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=stock reservado pendiente de pago',
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tbl_ordenes`
--

INSERT INTO `tbl_ordenes` (`id_orden`, `numero_orden`, `estado`, `nombre`, `apellido`, `email`, `usuario_id`, `telefono`, `direccion`, `ciudad`, `provincia`, `codigo_postal`, `notas`, `metodo_pago`, `shipping_method_code`, `shipping_carrier`, `shipping_eta`, `shipping_meta`, `zipnova_shipment_id`, `tracking_number`, `subtotal`, `envio`, `total`, `items`, `fecha_creacion`, `fecha_actualizacion`, `reserva_expira_at`, `reserva_activa`, `deleted_at`) VALUES
(1, 'TEST-0001', 'confirmado', 'Cliente', 'Prueba', 'prueba1@test.com', NULL, '1122334455', 'Calle Falsa 123', 'CABA', 'Buenos Aires', '1000', 'Orden de prueba para dashboard', 'mercadopago', NULL, NULL, NULL, NULL, NULL, NULL, 14900.00, 0.00, 14900.00, '[{\"id_sku\": 2, \"imagen\": \"\", \"nombre\": \"Vestido Floral Romántico\", \"id_prod\": 1, \"cantidad\": 1, \"color_nombre\": \"Rosa\", \"talle_nombre\": \"4A\", \"precio_unitario\": 13900.0}]', '2026-06-22 18:30:33', NULL, NULL, 0, NULL),
(2, 'TEST-0002', 'enviado', 'Cliente', 'Dos', 'prueba2@test.com', NULL, '1122334456', 'Av. Siempreviva 742', 'La Plata', 'Buenos Aires', '1900', 'Orden de prueba para dashboard', 'mercadopago', NULL, NULL, NULL, NULL, NULL, NULL, 25800.00, 0.00, 25800.00, '[{\"id_sku\": 6, \"imagen\": \"\", \"nombre\": \"Mameluco Tejido Bebé\", \"id_prod\": 2, \"cantidad\": 2, \"color_nombre\": \"Rosa\", \"talle_nombre\": \"0-3M\", \"precio_unitario\": 12900.0}]', '2026-06-22 18:30:33', NULL, NULL, 0, NULL),
(3, 'TEST-0003', 'entregado', 'Cliente', 'Tres', 'prueba3@test.com', NULL, '1122334457', 'Mitre 50', 'Mar del Plata', 'Buenos Aires', '7600', 'Orden de prueba para dashboard', 'mercadopago', NULL, NULL, NULL, NULL, NULL, NULL, 79500.00, 0.00, 79500.00, '[{\"id_sku\": 9, \"imagen\": \"\", \"nombre\": \"Buzo Canguro Niño\", \"id_prod\": 3, \"cantidad\": 5, \"color_nombre\": \"Beige\", \"talle_nombre\": \"4A\", \"precio_unitario\": 15900.0}]', '2026-06-22 18:30:33', NULL, NULL, 0, NULL),
(4, 'TEST-GUEST-1782233599', 'pendiente', 'Invitado', 'Test', 'checkout-guest-1782233599@yofi.local', NULL, '1100000000', 'Calle 1', 'CABA', 'CABA', '1406', NULL, 'transferencia', NULL, NULL, NULL, NULL, NULL, NULL, 1000.00, 500.00, 1500.00, '[]', '2026-06-23 13:53:19', NULL, NULL, 0, NULL),
(5, 'TEST-USER-1782233599', 'pendiente', 'Logueado', 'Test', 'checkout-user-1782233599@yofi.local', NULL, '1100000000', 'Calle 2', 'CABA', 'CABA', '1406', NULL, 'transferencia', NULL, NULL, NULL, NULL, NULL, NULL, 2000.00, 0.00, 2000.00, '[]', '2026-06-23 13:53:19', NULL, NULL, 0, NULL),
(13, 'ORD-20260623-F1B477', 'cancelado', 'gonzalo', 'mattia', 'gonzalo.mattia@hotmail.com', 4, '01160436765', 'Ruta Nacional 6 km 149.5', 'General Rodriguez', 'Buenos Aires', '1748', '', 'mercadopago', 'standard_delivery', 'OCA', '6 a 10 días hábiles', '{\"service\": \"Entrega a domicilio\", \"carrier_id\": 208, \"logistic_type\": \"xd_dropoff\"}', NULL, NULL, 25590.00, 491379.00, 516969.00, '[{\"id_sku\": 155, \"imagen\": \"http://localhost/yofi/imgprod/prod-13-14-1782231749-0.jpg\", \"nombre\": \"Gorro aspen\", \"id_prod\": 13, \"cantidad\": 1, \"color_nombre\": \"Natural\", \"talle_nombre\": \"Único\", \"precio_unitario\": 15590}, {\"id_sku\": 153, \"imagen\": \"http://localhost/yofi/imgprod/prod-11-16-1782231562-0.jpg\", \"nombre\": \"Bandana tejida\", \"id_prod\": 11, \"cantidad\": 1, \"color_nombre\": \"Chocolate\", \"talle_nombre\": \"Único\", \"precio_unitario\": 5000}, {\"id_sku\": 154, \"imagen\": \"http://localhost/yofi/imgprod/prod-11-17-1782231642-0.jpg\", \"nombre\": \"Bandana tejida\", \"id_prod\": 11, \"cantidad\": 1, \"color_nombre\": \"Gris topo\", \"talle_nombre\": \"Único\", \"precio_unitario\": 5000}]', '2026-06-23 14:39:27', '2026-06-29 19:29:21', '2026-06-23 15:09:27', 0, NULL),
(14, 'ORD-20260623-77204D', 'cancelado', 'gonzalo', 'mattia', 'gonzalo.mattia@hotmail.com', 4, '+541160436765', 'Fred Aden 406', 'Villa Sarmiento', 'Buenos Aires', '1706', '', 'mercadopago', 'standard_delivery', 'OCA', '7 a 12 días hábiles', '{\"service\": \"Entrega a domicilio\", \"carrier_id\": 208, \"logistic_type\": \"xd_dropoff\"}', NULL, NULL, 40000.00, 491641.00, 531641.00, '[{\"id_sku\": 184, \"imagen\": \"/yofi/imgprod/prod-17-15-1782231439-0.jpg\", \"nombre\": \"Delantal vintage sin bordar\", \"id_prod\": 17, \"cantidad\": 1, \"color_nombre\": \"Verde antiguo\", \"talle_nombre\": \"Único\", \"precio_unitario\": 40000}]', '2026-06-23 15:01:27', '2026-06-29 19:29:21', '2026-06-23 15:31:27', 0, NULL),
(15, 'ORD-20260623-82508A', 'preparando_envio', 'gonzalo', 'mattia', 'gonzalo.mattia@hotmail.com', 4, '+541160436765', 'Fred Aden 406', 'Villa Sarmiento', 'Buenos Aires', '1706', '', 'mercadopago', 'standard_delivery', 'OCA', '7 a 12 días hábiles', '{\"service\": \"Entrega a domicilio\", \"carrier_id\": 208, \"logistic_type\": \"xd_dropoff\"}', '27817827', 'https://app.zipnova.com.ar/track/1CcLeoqibws7Ep93fZfiWV', 5000.00, 491006.00, 496006.00, '[{\"id_sku\": 153, \"imagen\": \"/yofi/imgprod/prod-11-16-1782231562-0.jpg\", \"nombre\": \"Bandana tejida\", \"id_prod\": 11, \"cantidad\": 1, \"color_nombre\": \"Chocolate\", \"talle_nombre\": \"Único\", \"precio_unitario\": 5000}]', '2026-06-23 15:03:52', '2026-06-23 15:19:10', '2026-06-23 15:33:52', 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_ordenes_audit`
--

CREATE TABLE `tbl_ordenes_audit` (
  `id` int NOT NULL,
  `id_orden` int NOT NULL,
  `evento` varchar(50) NOT NULL,
  `usuario_admin` varchar(100) DEFAULT NULL,
  `ip_origen` varchar(45) DEFAULT NULL,
  `payload_json` longtext,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_ordenes_historial`
--

CREATE TABLE `tbl_ordenes_historial` (
  `id_historial` int NOT NULL,
  `id_orden` int NOT NULL,
  `estado_anterior` varchar(50) DEFAULT NULL,
  `estado_nuevo` varchar(50) NOT NULL,
  `fecha_cambio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_admin` varchar(100) DEFAULT NULL COMMENT 'Usuario del admin que hizo el cambio',
  `notas` text COMMENT 'Notas o comentarios sobre el cambio',
  `tracking_number` varchar(100) DEFAULT NULL COMMENT 'Número de seguimiento si aplica',
  `motivo_cancelacion` text COMMENT 'Motivo si se canceló'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tbl_ordenes_historial`
--

INSERT INTO `tbl_ordenes_historial` (`id_historial`, `id_orden`, `estado_anterior`, `estado_nuevo`, `fecha_cambio`, `usuario_admin`, `notas`, `tracking_number`, `motivo_cancelacion`) VALUES
(1, 15, 'pendiente', 'confirmado', '2026-06-23 15:05:27', 'MercadoPago', 'accredited', NULL, NULL),
(2, 15, 'confirmado', 'preparando_envio', '2026-06-23 15:19:10', 'Zipnova API', 'Envío creado en Zipnova', 'https://app.zipnova.com.ar/track/1CcLeoqibws7Ep93fZfiWV', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_password_tokens`
--

CREATE TABLE `tbl_password_tokens` (
  `id_token` int NOT NULL,
  `usuario_id` int NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tbl_password_tokens`
--

INSERT INTO `tbl_password_tokens` (`id_token`, `usuario_id`, `token`, `expires_at`, `used`, `fecha_creacion`) VALUES
(1, 4, '4e36ef3faff57715e4ae0468ee0e93e27b337b586348e685e6586f27dcfabdd4', '2026-06-23 18:05:21', 1, '2026-06-23 14:05:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_productos`
--

CREATE TABLE `tbl_productos` (
  `id_prod` int NOT NULL,
  `id_cate` int NOT NULL,
  `nombre` varchar(250) NOT NULL,
  `slug` varchar(270) NOT NULL,
  `codigo` varchar(30) NOT NULL,
  `precio_base` decimal(10,2) NOT NULL,
  `precio_oferta` decimal(10,2) DEFAULT NULL,
  `descripcion` text,
  `composicion` text,
  `cuidados` text,
  `peso` decimal(10,2) DEFAULT '0.00',
  `alto` decimal(10,2) DEFAULT '0.00',
  `ancho` decimal(10,2) DEFAULT '0.00',
  `profundidad` decimal(10,2) DEFAULT '0.00',
  `publicado` tinyint DEFAULT '1',
  `destacado` tinyint DEFAULT '0',
  `oferta` tinyint DEFAULT '0',
  `promo_badge` varchar(20) DEFAULT NULL,
  `borrado` tinyint DEFAULT '0',
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tbl_productos`
--

INSERT INTO `tbl_productos` (`id_prod`, `id_cate`, `nombre`, `slug`, `codigo`, `precio_base`, `precio_oferta`, `descripcion`, `composicion`, `cuidados`, `peso`, `alto`, `ancho`, `profundidad`, `publicado`, `destacado`, `oferta`, `promo_badge`, `borrado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 1, 'Vestido Floral Romántico', 'vestido-floral-rom-antico', 'YF-001', 18500.00, 13900.00, 'Vestido de algodón importado con estampado floral.', '', '', 0.30, 40.00, 35.00, 2.00, 0, 0, 0, '', 1, '2026-06-16 13:46:57', '2026-06-22 22:07:07'),
(2, 1, 'Mameluco Tejido Bebé', 'mameluco-tejido-bebe', 'YF-002', 12900.00, NULL, 'Mameluco tejido importado, suave al tacto.', NULL, NULL, 0.20, 35.00, 30.00, 2.00, 0, 0, 0, NULL, 0, '2026-06-16 13:46:57', '2026-06-22 21:27:10'),
(3, 2, 'Buzo Canguro Niño', 'buzo-canguro-nino', 'YF-003', 15900.00, NULL, 'Buzo canguro de algodón importado.', NULL, NULL, 0.35, 45.00, 40.00, 3.00, 0, 0, 0, NULL, 0, '2026-06-16 13:46:57', '2026-06-22 21:27:10'),
(4, 3, 'Conjunto Primavera', 'conjunto-primavera', 'YF-004', 22000.00, 17500.00, 'Conjunto de dos piezas, top y pantalón.', NULL, NULL, 0.40, 45.00, 38.00, 3.00, 0, 0, 1, '3x2', 0, '2026-06-16 13:46:57', '2026-06-22 21:27:10'),
(5, 1, 'Campera Acolchada Niña', 'campera-acolchada-nina', 'YF-005', 28500.00, NULL, 'Campera acolchada importada, abrigada y liviana.', NULL, NULL, 0.50, 48.00, 42.00, 5.00, 0, 0, 0, NULL, 1, '2026-06-16 13:46:57', '2026-06-22 22:09:03'),
(6, 1, 'Body Mini Argentina campeon', 'body-mini-argentina-campeon', 'I26M8806', 26900.00, NULL, 'El body mini Argentina, hecho en jersey con estampa.Tiene broche en escote y entre piernas. Para armar un look futbolero.\r\nComposición: 100% algodón\r\nTalles: del S al XXXL', '', '', 0.50, 20.00, 20.00, 5.00, 0, 1, 1, '2x1', 1, '2026-06-16 17:36:03', '2026-06-22 22:17:17'),
(7, 9, 'Infinito tejido', 'infinito-tejido', 'YF-MINI-INF', 15000.00, NULL, 'Fabricados en Acrílico Premium: Elegimos los mejores insumos para cuidar la piel de tu Mini. El acrílico ofrece suavidad al tacto y excelente durabilidad.\r\n\r\n- Hipoalergénicos y Sin Picazón: Nuestros Infinitos Tejidos son hipoalergénicos y no causan ninguna picazón. ¡Tu Mini cómodo y canchero todo el tiempo!.\r\n\r\n- Talles: Talle único que abarca desde recién nacidos hasta los 6 años. El diámetro del cuello es de aprox. 90cm', '', '', 0.00, 0.00, 0.00, 0.00, 1, 1, 0, '', 0, '2026-06-22 21:22:49', '2026-06-23 13:10:24'),
(11, 9, 'Bandana tejida', 'bandana-tejida', 'YF-MINI-BAN', 15000.00, 5000.00, 'Nuestros nuevos y exclusivos cuellitos tipo bandana tejidos están fabricados con Acrilico calidad premium. Son hipoalergénicos, super suaves y no pican.\r\n\r\nPoseen un practico botón de coco para facilitar su colocación y es el complemento ideal para abrigar a tu bebé este INVIERNO.\r\n\r\nÉste modelito abarca desde los 6 meses hasta los 3 años aproximadamente pero a los más grandes también les va!', '', '', 0.00, 0.00, 0.00, 0.00, 1, 1, 0, '', 0, '2026-06-22 21:22:49', '2026-06-23 13:20:44'),
(13, 9, 'Gorro aspen', 'gorro-aspen', 'YF-MINI-ASP', 15590.00, NULL, 'Fabricados en Acrílico Premium: Elegimos los mejores insumos para cuidar la piel de tu Mini. El acrílico ofrece suavidad al tacto y excelente durabilidad.\r\n\r\n- Hipoalergénicos y Sin Picazón: Nuestros Gorros Pompón son hipoalergénicos y no causan ninguna picazón. ¡Tu Mini cómodo y canchero todo el tiempo!.\r\n\r\n- Ajuste Elástico: El diámetro del gorro en la frente es de 32 cm, pero la elasticidad de la lana permite que se ajuste con comodidad hasta 55 cm. Esto significa un ajuste seguro y adaptable\r\n\r\n- Talles: Talle único para todas las edades. Van desde los 6 meses hasta los 6 años, adaptándose al crecimiento de tu bebé y brindando uso prolongado.\r\n\r\n- Hecho con Amor: Diseñado y Fabricado en Mar del Plata, Argentina', '', '', 0.00, 0.00, 0.00, 0.00, 1, 0, 0, '', 0, '2026-06-22 21:22:49', '2026-06-23 13:25:19'),
(16, 9, 'Gorro pompón bordó', 'gorro-pomp-on-bord-o', 'YF-MINI-010', 22390.00, 15590.00, '- Fabricados en Acrílico Premium: Elegimos los mejores insumos para cuidar la piel de tu Mini. El acrílico ofrece suavidad al tacto y excelente durabilidad.\r\n\r\n- Hipoalergénicos y Sin Picazón: Nuestros Gorros Pompón son hipoalergénicos y no causan ninguna picazón. ¡Tu Mini cómodo y canchero todo el tiempo!.\r\n\r\n- Ajuste Elástico: El diámetro del gorro en la frente es de 32 cm, pero la elasticidad de la lana permite que se ajuste con comodidad hasta 55 cm. Esto significa un ajuste seguro y adaptable\r\n\r\n- Talles: Talle único para todas las edades. Van desde los 6 meses hasta los 6 años, adaptándose al crecimiento de tu bebé y brindando uso prolongado.\r\n\r\n- Hecho con Amor: Diseñado y Fabricado en Mar del Plata, Argentina.', '', '', 0.00, 0.00, 0.00, 0.00, 1, 1, 0, '', 0, '2026-06-22 21:22:49', '2026-06-23 13:27:46'),
(17, 10, 'Delantal vintage sin bordar', 'delantal-vintage-sin-bordar', 'YF-REG-001', 40000.00, NULL, 'Delantal estilo romantico\r\nFabricado artesanalmente, edición limitada.\r\núnico color y talle.\r\nTela tusor 100% algodón', '', '', 0.00, 0.00, 0.00, 0.00, 1, 1, 0, '', 0, '2026-06-22 21:22:49', '2026-06-23 13:17:44'),
(18, 10, 'Bordado a pedido', 'bordado-a-pedido', 'YF-REG-002', 8000.00, NULL, '', '', '', 0.00, 0.00, 0.00, 0.00, 1, 0, 0, '', 0, '2026-06-22 21:22:49', '2026-06-23 13:28:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_prod_imagenes`
--

CREATE TABLE `tbl_prod_imagenes` (
  `id_imagen` int NOT NULL,
  `id_prod` int NOT NULL,
  `id_color` int DEFAULT NULL,
  `path` varchar(250) NOT NULL,
  `orden` int DEFAULT '0',
  `es_principal` tinyint DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tbl_prod_imagenes`
--

INSERT INTO `tbl_prod_imagenes` (`id_imagen`, `id_prod`, `id_color`, `path`, `orden`, `es_principal`) VALUES
(1, 1, 3, 'prod-001-rosa-1.jpg', 1, 1),
(2, 1, 4, 'prod-001-celeste-1.jpg', 1, 1),
(3, 2, 3, 'prod-002-rosa-1.jpg', 1, 1),
(4, 3, 7, 'prod-003-beige-1.jpg', 1, 1),
(5, 4, 1, 'prod-004-blanco-1.jpg', 1, 1),
(6, 5, 6, 'prod-005-verde-1.jpg', 1, 1),
(7, 6, NULL, 'prod-6-main.webp', 0, 1),
(8, 6, NULL, 'prod-6-gen-1781642237.jpg', 0, 1),
(9, 6, 4, 'prod-6-4-1781642901-0.jpg', 0, 1),
(22, 7, 11, 'prod-7-11-1782230798-0.jpg', 0, 1),
(23, 7, 11, 'prod-7-11-1782230798-1.jpg', 1, 0),
(24, 7, 11, 'prod-7-11-1782230798-2.jpg', 2, 0),
(25, 7, 12, 'prod-7-12-1782230883-0.jpg', 0, 1),
(26, 7, 12, 'prod-7-12-1782230883-1.jpg', 1, 0),
(27, 7, 12, 'prod-7-12-1782230883-2.jpg', 2, 0),
(28, 7, 13, 'prod-7-13-1782230965-0.jpg', 0, 1),
(29, 7, 13, 'prod-7-13-1782230965-1.jpg', 1, 0),
(30, 7, 13, 'prod-7-13-1782230965-2.jpg', 2, 0),
(31, 17, 15, 'prod-17-15-1782231439-0.jpg', 0, 1),
(32, 17, 15, 'prod-17-15-1782231439-1.jpg', 1, 0),
(33, 17, 15, 'prod-17-15-1782231439-2.jpg', 2, 0),
(34, 17, 15, 'prod-17-15-1782231439-3.jpg', 3, 0),
(35, 17, 15, 'prod-17-15-1782231439-4.jpg', 4, 0),
(36, 17, 15, 'prod-17-15-1782231439-5.jpg', 5, 0),
(37, 11, 16, 'prod-11-16-1782231562-0.jpg', 0, 1),
(38, 11, 16, 'prod-11-16-1782231562-1.jpg', 1, 0),
(39, 11, 16, 'prod-11-16-1782231562-2.jpg', 2, 0),
(40, 11, 17, 'prod-11-17-1782231642-0.jpg', 0, 1),
(41, 11, 17, 'prod-11-17-1782231642-1.jpg', 1, 0),
(42, 13, 14, 'prod-13-14-1782231749-0.jpg', 0, 1),
(43, 13, 14, 'prod-13-14-1782231749-1.jpg', 1, 0),
(44, 13, 14, 'prod-13-14-1782231749-2.jpg', 2, 0),
(45, 13, 14, 'prod-13-14-1782231749-3.jpg', 3, 0),
(46, 13, 15, 'prod-13-15-1782231828-0.jpg', 1, 1),
(47, 13, 15, 'prod-13-15-1782231828-1.jpg', 2, 0),
(48, 13, 15, 'prod-13-15-1782231828-2.jpg', 3, 0),
(49, 13, 15, 'prod-13-15-1782231828-3.jpg', 4, 0),
(50, 13, 3, 'prod-13-3-1782231900-0.jpg', 1, 1),
(51, 13, 3, 'prod-13-3-1782231900-1.jpg', 2, 0),
(52, 13, 3, 'prod-13-3-1782231900-2.jpg', 3, 0),
(53, 13, 3, 'prod-13-3-1782231900-3.jpg', 4, 0),
(54, 16, 11, 'prod-16-11-1782232065-0.jpg', 0, 1),
(55, 16, 11, 'prod-16-11-1782232065-1.jpg', 1, 0),
(56, 16, 11, 'prod-16-11-1782232065-2.jpg', 2, 0),
(57, 16, 11, 'prod-16-11-1782232065-3.jpg', 3, 0),
(58, 18, 7, 'prod-18-7-1782232111-0.jpg', 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_sessions`
--

CREATE TABLE `tbl_sessions` (
  `id` varchar(128) NOT NULL,
  `data` mediumblob NOT NULL,
  `last_access` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tbl_sessions`
--

INSERT INTO `tbl_sessions` (`id`, `data`, `last_access`) VALUES
('03c27e20edfbf6a684119166630d7dd3', 0x7075626c69635f637372665f746f6b656e7c733a36343a2261323963333139363762646332343063333265626134316634633931353031613335303339353537353362326266353438333732383134326434666134616163223b, 1782767811),
('0443c5f554b4d396033484d15ff0319e', 0x7075626c69635f637372665f746f6b656e7c733a36343a2230663337373035646238353135343131643838316236616332333361386166373937633264306634396238646134376264636132396263636437366364303665223b, 1782767713),
('15e6b54f3d5e3a43972e60ae85ed7d54', 0x7075626c69635f637372665f746f6b656e7c733a36343a2237393964613361303336353238373037326537383164646635343462383962353637643332653637383435393565323739613931363031373239336431636331223b, 1782822388),
('1f6821fa3f37fd716e8ee495028bbbbe', 0x7075626c69635f637372665f746f6b656e7c733a36343a2263386133343134306530623437656335313639343634623137653936373135633634643361626561353566643137393136626564653632396633353436616134223b, 1782767801),
('1fec6b4457dd028b5226366590307976', 0x7075626c69635f637372665f746f6b656e7c733a36343a2233386539663761636266326238373666366162303234616437373536333337663836323862653930376466653264383762363139643565326237643365383163223b, 1782768043),
('262c0c98fa86eb24e15cc6df36abd8b8', 0x7075626c69635f637372665f746f6b656e7c733a36343a2261383831623232633365346334333961646332313537323261303334393733343737663837346636373063366663333538326636653063326439666561323037223b, 1782767816),
('2c9436e94b17d590e1efcfcaff0005d4', 0x7075626c69635f637372665f746f6b656e7c733a36343a2237323566303765326362346665353930373630373932313638366465323634346165623763303061373238373566326664396632363234616361393962633361223b, 1782849122),
('2ef4469ed7e36e9cca4d6aa9a930655b', 0x7075626c69635f637372665f746f6b656e7c733a36343a2231613562393063353236316261313739656335373362623234363561326265373963636161333734646463383337643435613863353237356338353731643263223b, 1782827960),
('2fa1ad1110a1c4bcbb852e577b433665', 0x7075626c69635f637372665f746f6b656e7c733a36343a2265313364383066376132306330373666396131363833366564373230326237323339343836316635333833326361646630646534303135633931643062363266223b, 1782772654),
('400b6aa7b4616aa517e8c90ff46c7d82', 0x7075626c69635f637372665f746f6b656e7c733a36343a2261373266363861366133626134663036386434633962623737663266303165343364623564646539636339383534386437653062386361656664653861313332223b, 1782849486),
('406fa21fa272f5524d01b3fd0c581ff3', '', 1782767751),
('4702a7c1fd9d9ddd22c3bbab16dd1d45', 0x7075626c69635f637372665f746f6b656e7c733a36343a2262386664393435616561376634616134626566363263363566316535623437653838643663326434326339376539323061383232653431353639636238313036223b, 1782767845),
('4738f649a06724bdf37bf8590ed0b05c', 0x7075626c69635f637372665f746f6b656e7c733a36343a2231396139623539656661396435663930653566343165393938313461326163356436346264313730623763396362643563316363633066633034663636616539223b, 1782772657),
('4cd1f5f9103af391afd2dd79e3dda744', '', 1782767742),
('4d559eaae562eaa04320e559fbe5867e', 0x7075626c69635f637372665f746f6b656e7c733a36343a2266303064323665303438376433666534323864653565343431393731656131313864636231626231613633376438366431303230363661383239326633626265223b, 1782768021),
('51c194367c949e327a28a33716d6fa41', 0x7075626c69635f637372665f746f6b656e7c733a36343a2239303362613961646161646134313736356138336363303331633335323133333938303730656161373930373434623363313161653934613734326331613731223b, 1782767853),
('589367aeaf0b6c7e31e6dafcc63c29f4', 0x7075626c69635f637372665f746f6b656e7c733a36343a2236303364643636653832316532303035373239353335383838333038653765316532343231653837353337383764333764333035336332306235376663373661223b, 1782827996),
('5a5923846217208caaf8cb9474be743d', '', 1782767726),
('5b0e8251796c4288b4b6767e0686d89a', 0x7075626c69635f637372665f746f6b656e7c733a36343a2234396366346136363830386435613336306531613134336431636537393434346432316132313064396138393833663335306635363065666635363732613965223b, 1782772155),
('5d1a10593d9394b680063cb88f7362c1', 0x7075626c69635f637372665f746f6b656e7c733a36343a2232303736343038656265623564303130636333633535616334643932373232303064333266643637656564376666383737393532613535363536333366366238223b, 1782772658),
('5d3e446d9c1014a03c16e0479952b2f6', 0x7075626c69635f637372665f746f6b656e7c733a36343a2239633965353866313433393831663063363631363536376566353437373438363339653832336161343065373561346433353765333863646230303438326462223b, 1782850052),
('5e93d2133d57b01b8cbd1762f45bac34', 0x7075626c69635f637372665f746f6b656e7c733a36343a2264393365613930333038633234316538353165313832396633303538373937633165663765313063396266316663333062353134653830336664333861653539223b, 1782849386),
('5f1818600a4b1326dbfc3dae7e8f0f47', 0x7075626c69635f637372665f746f6b656e7c733a36343a2234653330363737316535653532626134396139333761626431383536373561333162643538333535363932306531656135663834376439383932653664616161223b, 1782825943),
('5f1cf38f59bc64726c1ad84676028d35', 0x7075626c69635f637372665f746f6b656e7c733a36343a2262376137303234396338646235346363333736663561373330333034653935383736656664373139306462643134336536313734666539623137613864356661223b, 1782764434),
('637244f22e03ed5112231bf40aefc7ec', 0x7075626c69635f637372665f746f6b656e7c733a36343a2231383935306230353563313431336230646335653432303066383236356666396635366139333337356335356336323331383963386538383635623133303461223b, 1782818426),
('66975f8517fab2bb1829934e3379288b', 0x7075626c69635f637372665f746f6b656e7c733a36343a2233386636393162363162343937656439633864626236336661653165386339646466366539613139303031356438316230343563653439373534343963643861223b, 1782767707),
('679ac4b67e68518e8fb5c435298757e8', 0x7075626c69635f637372665f746f6b656e7c733a36343a2262393461623065633164363565643633363639383764366233353335313834663634333339663637636261653663356261356165353939353461653062376232223b, 1782850116),
('67aa701f35ebce346b98bbf3e019dc97', 0x7075626c69635f637372665f746f6b656e7c733a36343a2239313463646364303334653835366533393035663235333035333062396331346534363162663865616563316661373933666161306361303962363838626132223b, 1782772660),
('68fc459efdb804200c50960f9e0e3919', 0x7075626c69635f637372665f746f6b656e7c733a36343a2265616631663834383534383337373765326337393365316239323832343131626364663435653730373661346133383264666465383931643937316562396137223b, 1782767721),
('6a6239935c8baff8a79d23153895381d', 0x7075626c69635f637372665f746f6b656e7c733a36343a2239653366373961666530366132313466613865356634396233613164346362646136623465623864653035326631666135623236346263333439306138646432223b, 1782822290),
('6c097690cc944222640e08afbb013257', 0x7075626c69635f637372665f746f6b656e7c733a36343a2233613936643537303531653034316334303335636334303036346130353133336131333132313139663736656230346634373630336632373164313066646166223b, 1782822338),
('726a6fdcc3023826c17f1510088882dc', 0x7075626c69635f637372665f746f6b656e7c733a36343a2231643062393636393933396665356231323532613031363230343337343236383365333965376333646430306634613638346531643939386664653766383462223b, 1782850114),
('7280a093792b6b5012731170e311cca0', 0x7075626c69635f637372665f746f6b656e7c733a36343a2235656135653234323833323865316233386265613537353237303662616236323430643538386532353466383064343534623661613038396535353736623838223b, 1782772661),
('73bdc2284d6005ee21a04d9899babc72', 0x7075626c69635f637372665f746f6b656e7c733a36343a2263326362666466663238393466373834373730393863373064653666386630336531623731656632323333663665626162376331346136663335313961336235223b, 1782850049),
('754e9758de4dc100e8b356eff17c8053', 0x7075626c69635f637372665f746f6b656e7c733a36343a2263613933383934343532386634313239356538396162633535396134346435616435663464383537336465303331356333663331303062636631663464356461223b, 1782772084),
('760fa36378ddf2b681b9d95be565a265', 0x7075626c69635f637372665f746f6b656e7c733a36343a2266616339393335336535336639653531613333306336666535363961316437663039303931333633656363393966613432316164333766383162313039623066223b, 1782765587),
('845d5540ecbbac5639359e51e66fdaf8', 0x7075626c69635f637372665f746f6b656e7c733a36343a2234613963346237323434643566646161313337316638343930366365396238333938383535646433393934353539616430336532306565393034333663666563223b, 1782849369),
('894cde19b42b6a801b5f727e50e762a4', 0x7075626c69635f637372665f746f6b656e7c733a36343a2236353061373064393237373564396630303638323334656435353361326233396464393730623832303139383530646465643164636466336566333837336433223b, 1782855592),
('8c3576cfe5229c79142ab777ab6a8fe5', 0x7075626c69635f637372665f746f6b656e7c733a36343a2266363735366165653033646432646436643633626666303032663162326663393261316436616136663666656663383166333334626464363837633366613035223b, 1782850115),
('93af289b0f11163623b08b72c958c8ce', 0x7075626c69635f637372665f746f6b656e7c733a36343a2233316634333962633363613838633366616632383961343338323131626663346262393237373365323836356437356233353464363064616564333865336134223b, 1782827991),
('94c69c386ceb84fc938466b3cf313e76', 0x7075626c69635f637372665f746f6b656e7c733a36343a2263343266653134326437366266333332313064316335653636636339343964633266383539343166643535393735346234666365613361326535346632613366223b, 1782850047),
('953c7c5711fd99f8cc480d10fa35ddb4', 0x7075626c69635f637372665f746f6b656e7c733a36343a2236393166373964376632396463636138376331653066623839346332363839366234386666346362366131643737653462323831343637363462316462653362223b, 1782771874),
('963aaea423b5000f5b1771f68dc4cb3e', 0x7075626c69635f637372665f746f6b656e7c733a36343a2238383863346363636161613834396435616461333862333435306133326661616566313733623762643865336236326334633533393461616333623536326131223b, 1782827997),
('9997b67e125bd1ae232ad2e7c568c359', 0x7075626c69635f637372665f746f6b656e7c733a36343a2262363132303339616534353366616130656235646166656463383864353735353363353231656633326137343933306133613961316563363666633439636538223b, 1782855582),
('9ddcb06b6a038d0ce62edc4dcd5e01f8', 0x7075626c69635f637372665f746f6b656e7c733a36343a2239306132393565613335643234343339633032666135653561306234333337396566636164366530396163366134353561616236336639373065666266333865223b, 1782849691),
('a06ac06192cd3c73a6c469f59616977a', 0x7075626c69635f637372665f746f6b656e7c733a36343a2235393631663363396539353533646331663536313339303166626331666361343838303532616130616163616431656235386630613463386437376162656538223b, 1782849968),
('a458f28ddb5d15a9b6cf012bdb248311', 0x7075626c69635f637372665f746f6b656e7c733a36343a2261366237383837373138373661333234323231383764336233326531343666306663376163316461313839613436363331633439346563326232333861383238223b, 1782767836),
('a45e77da29122ce82274d6dca0b891ab', 0x7075626c69635f637372665f746f6b656e7c733a36343a2233663837306437643864313837643862373739393533363633323935616266626231376532663537616531636231653466643437363064346263353135353234223b, 1782849634),
('a74951ff75e9aecf940d13982b1299bc', 0x7075626c69635f637372665f746f6b656e7c733a36343a2230353732386134366633333532313965326563376138396138316431313038393637333265363238636131323035376635323638626233343933373565343366223b, 1782818476),
('a7623ed9da3a367198b9ddbc9c778196', 0x7075626c69635f637372665f746f6b656e7c733a36343a2265646433393734653037333635316162333831666461666230326232326630656462666433336266623833343836663237376632643031343464643432336332223b, 1782775617),
('ade6a3220c33de0536bd2f20c3a3e195', 0x7075626c69635f637372665f746f6b656e7c733a36343a2262393564633631616165303265393036316633643039363631383861383137653336633232626266386439353030616131336136666163363737626334643963223b, 1782849611),
('ae3ca509b7dc47f1bb7d54005df3169a', 0x7075626c69635f637372665f746f6b656e7c733a36343a2239373239356435336563656635346664636662393261303433633036326366393035336263623539383631613633316464663463366234333934386237353266223b, 1782850051),
('af666d3f728d1ec5dd98c5c308ad82ca', '', 1782767741),
('b137dd6cbcb434c17f268d153bad272a', 0x7075626c69635f637372665f746f6b656e7c733a36343a2263356238633364313863316663616531366461613235616366393330376334363431653866376230396463663738633531393837646537383833313563623437223b, 1782765223),
('b9176d2e9766bc5cf80bca825715dc02', 0x7075626c69635f637372665f746f6b656e7c733a36343a2232366462303661386638313536346564343866396130343563326430663766626431373464353665333131376232653165303165363335343866383835366330223b, 1782772179),
('c16c2a577a286bccdfddc91c6f208f1b', 0x7075626c69635f637372665f746f6b656e7c733a36343a2230656562353566613430356331636235643439623531633661313663356538616339333132636130396334303638643531613163333933636534373630663064223b, 1782855277),
('c3f9da138c0318e14a5f900c060ea4f6', 0x7075626c69635f637372665f746f6b656e7c733a36343a2239343535626437376362373539363232383966306365313461663466633536626632366534376136326535663234666235633663356533303336623463653639223b, 1782765829),
('c6044319bfbe149b526f51c5fe89f288', 0x7075626c69635f637372665f746f6b656e7c733a36343a2236623031643936316439313630616635346139386131373637663965326530613566633334326239306230343332643437393730643763373364623132373837223b, 1782849045),
('c9950bbb01e875803af3ee1abfce8cb3', 0x7075626c69635f637372665f746f6b656e7c733a36343a2237376237613631393331316463326564336238326561373461396265303238396665386465376564393161653338623135323165366366373433326639626632223b, 1782767803),
('d713b3d4dfaebd4b5bed1ef26b2737cd', 0x7075626c69635f637372665f746f6b656e7c733a36343a2263396466383336633132613630656465303161333730306630656264373033623239343563643436663864663836663435393032346266633465353161353464223b, 1782767809),
('d732649a498890c720fdf1bd61d8bf0f', 0x7075626c69635f637372665f746f6b656e7c733a36343a2265383566333335623764366366383866353135616331663934643962316164303863386263316439316331363336336361373031666634333766653264353131223b, 1782828031),
('d79954f222a0569a8b716a9513fc81e1', 0x7075626c69635f637372665f746f6b656e7c733a36343a2261666632646232303233653037313539313863336635656335363539653630343735363139623933363433386232396337376239336434306565383936393264223b, 1782768039),
('dc4b89058c81969ba4d388ee175b18be', 0x7075626c69635f637372665f746f6b656e7c733a36343a2264333263343838346666326236393632323865396135323066333264663835336161313163656338643135663831666365323765663231363465613461323963223b, 1782849937),
('e3b4cc9577400cc5b05fe13f83eef3f5', 0x7075626c69635f637372665f746f6b656e7c733a36343a2262373437343664633931653237323739353466373536396662393265623033643534666364353066363239393237303631363636633163636133623034346532223b, 1782829515),
('e50a6367ce820563d2c07c4b9279c3ca', 0x7075626c69635f637372665f746f6b656e7c733a36343a2235636232643938643737343061343165633832633739333762353331616633363530373563303034643032643535353835316665633231653835613765383833223b, 1782767685),
('e598b8b36633db4c6542ce4b723f72aa', 0x7075626c69635f637372665f746f6b656e7c733a36343a2235663764353234303564306137633534343965633664343538623633386436383964646264306562343866333539333539636234353365653934313135666664223b, 1782768189),
('ea51c62d37139eaf263014e904cd6bd7', 0x7075626c69635f637372665f746f6b656e7c733a36343a2231336138656431653061366434346137653363326437336336303932636631643536393461323630353832393431656266323339366530363330616264613230223b, 1782827999),
('ee4ae5d8f39bcda15070725de9f11191', 0x7075626c69635f637372665f746f6b656e7c733a36343a2237613165393630333039623265646563646265353738346361353035363365636638363734643233363238316466393234333631613766313435383533396663223b, 1782814999),
('ee91a9844e89e1a8903deeb9ad6286e8', 0x7075626c69635f637372665f746f6b656e7c733a36343a2237656564326638396234313166313131353339653334366465643466653939343863633638386138303264316331333335366435323731373831346166336265223b, 1782768019),
('f0dd30cb6a0fd57dda22c9fba8abbe79', 0x7075626c69635f637372665f746f6b656e7c733a36343a2235623361353863376565336330333430626264393838356562386531343633656565333331653761343132343437643439396435643133393264393638373964223b, 1782849646),
('f108760002f2dcf439734c9c70bf2638', 0x7075626c69635f637372665f746f6b656e7c733a36343a2263333031396239613965393830613839383138323665376437626362636531656434363334323763653438613866316338396135633163353830663866656265223b, 1782849935),
('f1d886f9fb09aa74fd38b24f7dbd4e25', 0x7075626c69635f637372665f746f6b656e7c733a36343a2231316138313438303164373631363438303333616635396233633562393866353439613862636137343565613862643930653137336238636463636234626262223b, 1782849377),
('f7123d2bc7d9307be798b0b76f04985a', 0x7075626c69635f637372665f746f6b656e7c733a36343a2233663662626433356330373266323735306365303035656338616265616630386636663733633336663666613235376437613933623766653864646439653731223b, 1782774509),
('fa6059fbb02a3793ae87e5e65ba06c14', 0x7075626c69635f637372665f746f6b656e7c733a36343a2265323763346538633565633063353061383836613065383063663737646666666238636666376537313965356166313164666235393538383664646438326431223b, 1782767806),
('fcfac2811087c20070f29c05f00cef8b', '', 1782767753),
('fde01846fc0d5be1eb5f3a2d079763eb', 0x7075626c69635f637372665f746f6b656e7c733a36343a2235363037303137616161393931393639663237626364666231363032633162663864373163616439313734626162626131333130633962336437636237646137223b, 1782825894);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_shipping_config`
--

CREATE TABLE `tbl_shipping_config` (
  `id` int NOT NULL,
  `clave` varchar(100) NOT NULL,
  `valor` text NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tbl_shipping_config`
--

INSERT INTO `tbl_shipping_config` (`id`, `clave`, `valor`, `descripcion`, `fecha_actualizacion`, `fecha_creacion`) VALUES
(1, 'zipnova_enabled', '1', 'Si el envío vía Zipnova está activo en el checkout', NULL, '2026-06-22 19:06:27'),
(2, 'zipnova_label', 'Envío a domicilio', 'Texto visible para el cliente', '2026-06-22 19:18:08', '2026-06-22 19:06:27'),
(3, 'zipnova_eta_default', '3 a 6 días hábiles', 'ETA mostrado cuando la API no devuelve uno específico', '2026-06-22 19:17:48', '2026-06-22 19:06:27'),
(4, 'free_shipping_threshold', '150000', 'Monto mínimo de compra para envío gratis (0 = desactivado)', '2026-06-22 20:41:20', '2026-06-22 19:06:27'),
(5, 'pickup_enabled', '0', 'Si retiro en local está habilitado', NULL, '2026-06-22 19:06:27'),
(6, 'pickup_label', 'Retiro en local', 'Texto visible para el cliente', NULL, '2026-06-22 19:06:27'),
(7, 'pickup_address', '', 'Dirección de retiro mostrada al cliente', NULL, '2026-06-22 19:06:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_skus`
--

CREATE TABLE `tbl_skus` (
  `id_sku` int NOT NULL,
  `id_prod` int NOT NULL,
  `id_color` int NOT NULL,
  `id_talle` int NOT NULL,
  `codigo_sku` varchar(60) NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `stock_reservado` int NOT NULL DEFAULT '0',
  `precio_extra` decimal(10,2) DEFAULT '0.00',
  `activo` tinyint DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tbl_skus`
--

INSERT INTO `tbl_skus` (`id_sku`, `id_prod`, `id_color`, `id_talle`, `codigo_sku`, `stock`, `stock_reservado`, `precio_extra`, `activo`) VALUES
(1, 1, 3, 5, 'YF-001-ROSA-2A', 9, 0, 0.00, 1),
(2, 1, 3, 7, 'YF-001-ROSA-4A', 10, 0, 0.00, 1),
(3, 1, 3, 8, 'YF-001-ROSA-6A', 10, 0, 0.00, 1),
(4, 1, 4, 5, 'YF-001-CEL-2A', 10, 0, 0.00, 1),
(5, 1, 4, 7, 'YF-001-CEL-4A', 10, 0, 0.00, 1),
(6, 2, 3, 1, 'YF-002-ROSA-03M', 10, 0, 0.00, 1),
(7, 2, 3, 2, 'YF-002-ROSA-36M', 10, 0, 0.00, 1),
(8, 2, 3, 3, 'YF-002-ROSA-612M', 10, 0, 0.00, 1),
(9, 3, 7, 7, 'YF-003-BEI-4A', 10, 0, 0.00, 1),
(10, 3, 7, 8, 'YF-003-BEI-6A', 10, 0, 0.00, 1),
(11, 3, 7, 9, 'YF-003-BEI-8A', 10, 0, 0.00, 1),
(12, 4, 1, 5, 'YF-004-BLA-2A', 10, 0, 0.00, 1),
(13, 4, 1, 7, 'YF-004-BLA-4A', 10, 0, 0.00, 1),
(14, 5, 6, 7, 'YF-005-VER-4A', 10, 0, 0.00, 1),
(15, 5, 6, 8, 'YF-005-VER-6A', 10, 0, 0.00, 1),
(16, 5, 6, 9, 'YF-005-VER-8A', 10, 0, 0.00, 1),
(17, 6, 1, 1, 'YOFI-6-1-1', 10, 0, 0.00, 1),
(18, 6, 1, 2, 'YOFI-6-1-2', 10, 0, 0.00, 1),
(19, 6, 1, 3, 'YOFI-6-1-3', 10, 0, 0.00, 1),
(20, 6, 1, 4, 'YOFI-6-1-4', 10, 0, 0.00, 1),
(21, 6, 1, 5, 'YOFI-6-1-5', 10, 0, 0.00, 1),
(22, 6, 1, 6, 'YOFI-6-1-6', 10, 0, 0.00, 1),
(23, 6, 1, 7, 'YOFI-6-1-7', 10, 0, 0.00, 1),
(24, 6, 1, 8, 'YOFI-6-1-8', 10, 0, 0.00, 1),
(25, 6, 1, 9, 'YOFI-6-1-9', 10, 0, 0.00, 1),
(26, 6, 1, 10, 'YOFI-6-1-10', 10, 0, 0.00, 1),
(27, 6, 1, 11, 'YOFI-6-1-11', 10, 0, 0.00, 1),
(28, 6, 2, 1, 'YOFI-6-2-1', 10, 0, 0.00, 1),
(29, 6, 2, 2, 'YOFI-6-2-2', 10, 0, 0.00, 1),
(30, 6, 2, 3, 'YOFI-6-2-3', 10, 0, 0.00, 1),
(31, 6, 2, 4, 'YOFI-6-2-4', 10, 0, 0.00, 1),
(32, 6, 2, 5, 'YOFI-6-2-5', 10, 0, 0.00, 1),
(33, 6, 2, 6, 'YOFI-6-2-6', 10, 0, 0.00, 1),
(34, 6, 2, 7, 'YOFI-6-2-7', 10, 0, 0.00, 1),
(35, 6, 2, 8, 'YOFI-6-2-8', 10, 0, 0.00, 1),
(36, 6, 2, 9, 'YOFI-6-2-9', 10, 0, 0.00, 1),
(37, 6, 2, 10, 'YOFI-6-2-10', 10, 0, 0.00, 1),
(38, 6, 2, 11, 'YOFI-6-2-11', 10, 0, 0.00, 1),
(39, 6, 3, 1, 'YOFI-6-3-1', 10, 0, 0.00, 1),
(40, 6, 3, 2, 'YOFI-6-3-2', 10, 0, 0.00, 1),
(41, 6, 3, 3, 'YOFI-6-3-3', 10, 0, 0.00, 1),
(42, 6, 3, 4, 'YOFI-6-3-4', 10, 0, 0.00, 1),
(43, 6, 3, 5, 'YOFI-6-3-5', 10, 0, 0.00, 1),
(44, 6, 3, 6, 'YOFI-6-3-6', 10, 0, 0.00, 1),
(45, 6, 3, 7, 'YOFI-6-3-7', 10, 0, 0.00, 1),
(46, 6, 3, 8, 'YOFI-6-3-8', 10, 0, 0.00, 1),
(47, 6, 3, 9, 'YOFI-6-3-9', 10, 0, 0.00, 1),
(48, 6, 3, 10, 'YOFI-6-3-10', 10, 0, 0.00, 1),
(49, 6, 3, 11, 'YOFI-6-3-11', 10, 0, 0.00, 1),
(50, 6, 4, 1, 'YOFI-6-4-1', 10, 0, 0.00, 1),
(51, 6, 4, 2, 'YOFI-6-4-2', 10, 0, 0.00, 1),
(52, 6, 4, 3, 'YOFI-6-4-3', 10, 0, 0.00, 1),
(53, 6, 4, 4, 'YOFI-6-4-4', 10, 0, 0.00, 1),
(54, 6, 4, 5, 'YOFI-6-4-5', 10, 0, 0.00, 1),
(55, 6, 4, 6, 'YOFI-6-4-6', 10, 0, 0.00, 1),
(56, 6, 4, 7, 'YOFI-6-4-7', 10, 0, 0.00, 1),
(57, 6, 4, 8, 'YOFI-6-4-8', 10, 0, 0.00, 1),
(58, 6, 4, 9, 'YOFI-6-4-9', 10, 0, 0.00, 1),
(59, 6, 4, 10, 'YOFI-6-4-10', 10, 0, 0.00, 1),
(60, 6, 4, 11, 'YOFI-6-4-11', 10, 0, 0.00, 1),
(61, 6, 5, 1, 'YOFI-6-5-1', 10, 0, 0.00, 1),
(62, 6, 5, 2, 'YOFI-6-5-2', 10, 0, 0.00, 1),
(63, 6, 5, 3, 'YOFI-6-5-3', 10, 0, 0.00, 1),
(64, 6, 5, 4, 'YOFI-6-5-4', 10, 0, 0.00, 1),
(65, 6, 5, 5, 'YOFI-6-5-5', 10, 0, 0.00, 1),
(66, 6, 5, 6, 'YOFI-6-5-6', 10, 0, 0.00, 1),
(67, 6, 5, 7, 'YOFI-6-5-7', 10, 0, 0.00, 1),
(68, 6, 5, 8, 'YOFI-6-5-8', 10, 0, 0.00, 1),
(69, 6, 5, 9, 'YOFI-6-5-9', 10, 0, 0.00, 1),
(70, 6, 5, 10, 'YOFI-6-5-10', 10, 0, 0.00, 1),
(71, 6, 5, 11, 'YOFI-6-5-11', 10, 0, 0.00, 1),
(72, 6, 6, 1, 'YOFI-6-6-1', 10, 0, 0.00, 1),
(73, 6, 6, 2, 'YOFI-6-6-2', 10, 0, 0.00, 1),
(74, 6, 6, 3, 'YOFI-6-6-3', 10, 0, 0.00, 1),
(75, 6, 6, 4, 'YOFI-6-6-4', 10, 0, 0.00, 1),
(76, 6, 6, 5, 'YOFI-6-6-5', 10, 0, 0.00, 1),
(77, 6, 6, 6, 'YOFI-6-6-6', 10, 0, 0.00, 1),
(78, 6, 6, 7, 'YOFI-6-6-7', 10, 0, 0.00, 1),
(79, 6, 6, 8, 'YOFI-6-6-8', 10, 0, 0.00, 1),
(80, 6, 6, 9, 'YOFI-6-6-9', 10, 0, 0.00, 1),
(81, 6, 6, 10, 'YOFI-6-6-10', 10, 0, 0.00, 1),
(82, 6, 6, 11, 'YOFI-6-6-11', 10, 0, 0.00, 1),
(83, 6, 7, 1, 'YOFI-6-7-1', 10, 0, 0.00, 1),
(84, 6, 7, 2, 'YOFI-6-7-2', 10, 0, 0.00, 1),
(85, 6, 7, 3, 'YOFI-6-7-3', 10, 0, 0.00, 1),
(86, 6, 7, 4, 'YOFI-6-7-4', 10, 0, 0.00, 1),
(87, 6, 7, 5, 'YOFI-6-7-5', 10, 0, 0.00, 1),
(88, 6, 7, 6, 'YOFI-6-7-6', 10, 0, 0.00, 1),
(89, 6, 7, 7, 'YOFI-6-7-7', 10, 0, 0.00, 1),
(90, 6, 7, 8, 'YOFI-6-7-8', 10, 0, 0.00, 1),
(91, 6, 7, 9, 'YOFI-6-7-9', 10, 0, 0.00, 1),
(92, 6, 7, 10, 'YOFI-6-7-10', 10, 0, 0.00, 1),
(93, 6, 7, 11, 'YOFI-6-7-11', 10, 0, 0.00, 1),
(94, 6, 8, 1, 'YOFI-6-8-1', 10, 0, 0.00, 1),
(95, 6, 8, 2, 'YOFI-6-8-2', 10, 0, 0.00, 1),
(96, 6, 8, 3, 'YOFI-6-8-3', 10, 0, 0.00, 1),
(97, 6, 8, 4, 'YOFI-6-8-4', 10, 0, 0.00, 1),
(98, 6, 8, 5, 'YOFI-6-8-5', 10, 0, 0.00, 1),
(99, 6, 8, 6, 'YOFI-6-8-6', 10, 0, 0.00, 1),
(100, 6, 8, 7, 'YOFI-6-8-7', 10, 0, 0.00, 1),
(101, 6, 8, 8, 'YOFI-6-8-8', 10, 0, 0.00, 1),
(102, 6, 8, 9, 'YOFI-6-8-9', 10, 0, 0.00, 1),
(103, 6, 8, 10, 'YOFI-6-8-10', 10, 0, 0.00, 1),
(104, 6, 8, 11, 'YOFI-6-8-11', 10, 0, 0.00, 1),
(105, 6, 9, 1, 'YOFI-6-9-1', 10, 0, 0.00, 1),
(106, 6, 9, 2, 'YOFI-6-9-2', 10, 0, 0.00, 1),
(107, 6, 9, 3, 'YOFI-6-9-3', 10, 0, 0.00, 1),
(108, 6, 9, 4, 'YOFI-6-9-4', 10, 0, 0.00, 1),
(109, 6, 9, 5, 'YOFI-6-9-5', 10, 0, 0.00, 1),
(110, 6, 9, 6, 'YOFI-6-9-6', 10, 0, 0.00, 1),
(111, 6, 9, 7, 'YOFI-6-9-7', 10, 0, 0.00, 1),
(112, 6, 9, 8, 'YOFI-6-9-8', 10, 0, 0.00, 1),
(113, 6, 9, 9, 'YOFI-6-9-9', 10, 0, 0.00, 1),
(114, 6, 9, 10, 'YOFI-6-9-10', 10, 0, 0.00, 1),
(115, 6, 9, 11, 'YOFI-6-9-11', 10, 0, 0.00, 1),
(116, 6, 10, 1, 'YOFI-6-10-1', 10, 0, 0.00, 1),
(117, 6, 10, 2, 'YOFI-6-10-2', 10, 0, 0.00, 1),
(118, 6, 10, 3, 'YOFI-6-10-3', 10, 0, 0.00, 1),
(119, 6, 10, 4, 'YOFI-6-10-4', 10, 0, 0.00, 1),
(120, 6, 10, 5, 'YOFI-6-10-5', 10, 0, 0.00, 1),
(121, 6, 10, 6, 'YOFI-6-10-6', 10, 0, 0.00, 1),
(122, 6, 10, 7, 'YOFI-6-10-7', 10, 0, 0.00, 1),
(123, 6, 10, 8, 'YOFI-6-10-8', 10, 0, 0.00, 1),
(124, 6, 10, 9, 'YOFI-6-10-9', 10, 0, 0.00, 1),
(125, 6, 10, 10, 'YOFI-6-10-10', 10, 0, 0.00, 1),
(126, 6, 10, 11, 'YOFI-6-10-11', 10, 0, 0.00, 1),
(149, 7, 11, 12, 'YF-MINI-001-UNI', 10, 0, 0.00, 1),
(150, 7, 12, 12, 'YF-MINI-002-UNI', 10, 0, 0.00, 1),
(151, 7, 13, 12, 'YF-MINI-003-UNI', 10, 0, 0.00, 1),
(152, 7, 3, 12, 'YF-MINI-004-UNI', 0, 0, 0.00, 1),
(153, 11, 16, 12, 'YF-MINI-005-UNI', 9, 0, 0.00, 1),
(154, 11, 17, 12, 'YF-MINI-006-UNI', 10, 0, 0.00, 1),
(155, 13, 14, 12, 'YF-MINI-007-UNI', 10, 0, 0.00, 1),
(156, 13, 15, 12, 'YF-MINI-008-UNI', 10, 0, 0.00, 1),
(157, 13, 3, 12, 'YF-MINI-009-UNI', 10, 0, 0.00, 1),
(158, 16, 11, 12, 'YF-MINI-010-UNI', 10, 0, 0.00, 1),
(159, 17, 7, 12, 'YF-REG-001-UNI', 0, 0, 0.00, 1),
(160, 18, 7, 12, 'YF-REG-002-UNI', 10, 0, 0.00, 1),
(161, 7, 3, 1, 'YOFI-7-3-1', 0, 0, 0.00, 1),
(162, 7, 3, 2, 'YOFI-7-3-2', 0, 0, 0.00, 1),
(163, 7, 3, 3, 'YOFI-7-3-3', 0, 0, 0.00, 1),
(164, 7, 3, 4, 'YOFI-7-3-4', 0, 0, 0.00, 1),
(165, 7, 3, 5, 'YOFI-7-3-5', 0, 0, 0.00, 1),
(166, 7, 3, 6, 'YOFI-7-3-6', 0, 0, 0.00, 1),
(167, 7, 3, 7, 'YOFI-7-3-7', 0, 0, 0.00, 1),
(168, 7, 3, 8, 'YOFI-7-3-8', 0, 0, 0.00, 1),
(169, 7, 3, 9, 'YOFI-7-3-9', 0, 0, 0.00, 1),
(170, 7, 3, 10, 'YOFI-7-3-10', 0, 0, 0.00, 1),
(171, 7, 3, 11, 'YOFI-7-3-11', 0, 0, 0.00, 1),
(173, 17, 15, 1, 'YOFI-17-15-1', 0, 0, 0.00, 1),
(174, 17, 15, 2, 'YOFI-17-15-2', 0, 0, 0.00, 1),
(175, 17, 15, 3, 'YOFI-17-15-3', 0, 0, 0.00, 1),
(176, 17, 15, 4, 'YOFI-17-15-4', 0, 0, 0.00, 1),
(177, 17, 15, 5, 'YOFI-17-15-5', 0, 0, 0.00, 1),
(178, 17, 15, 6, 'YOFI-17-15-6', 0, 0, 0.00, 1),
(179, 17, 15, 7, 'YOFI-17-15-7', 0, 0, 0.00, 1),
(180, 17, 15, 8, 'YOFI-17-15-8', 0, 0, 0.00, 1),
(181, 17, 15, 9, 'YOFI-17-15-9', 0, 0, 0.00, 1),
(182, 17, 15, 10, 'YOFI-17-15-10', 0, 0, 0.00, 1),
(183, 17, 15, 11, 'YOFI-17-15-11', 0, 0, 0.00, 1),
(184, 17, 15, 12, 'YOFI-17-15-12', 10, 0, 0.00, 1),
(185, 17, 7, 1, 'YOFI-17-7-1', 0, 0, 0.00, 1),
(186, 17, 7, 2, 'YOFI-17-7-2', 0, 0, 0.00, 1),
(187, 17, 7, 3, 'YOFI-17-7-3', 0, 0, 0.00, 1),
(188, 17, 7, 4, 'YOFI-17-7-4', 0, 0, 0.00, 1),
(189, 17, 7, 5, 'YOFI-17-7-5', 0, 0, 0.00, 1),
(190, 17, 7, 6, 'YOFI-17-7-6', 0, 0, 0.00, 1),
(191, 17, 7, 7, 'YOFI-17-7-7', 0, 0, 0.00, 1),
(192, 17, 7, 8, 'YOFI-17-7-8', 0, 0, 0.00, 1),
(193, 17, 7, 9, 'YOFI-17-7-9', 0, 0, 0.00, 1),
(194, 17, 7, 10, 'YOFI-17-7-10', 0, 0, 0.00, 1),
(195, 17, 7, 11, 'YOFI-17-7-11', 0, 0, 0.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_slider`
--

CREATE TABLE `tbl_slider` (
  `id_slide` int NOT NULL,
  `imagen` varchar(250) NOT NULL,
  `imagen_mobile` varchar(250) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `orden` int DEFAULT '0',
  `activo` tinyint DEFAULT '1',
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tbl_slider`
--

INSERT INTO `tbl_slider` (`id_slide`, `imagen`, `imagen_mobile`, `link_url`, `orden`, `activo`, `fecha_creacion`) VALUES
(1, 'slide-1782174277.png', NULL, NULL, 1, 1, '2026-06-22 19:40:10'),
(3, 'slide-1782174332.png', NULL, NULL, 1, 1, '2026-06-22 21:25:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_stock_log`
--

CREATE TABLE `tbl_stock_log` (
  `id` int NOT NULL,
  `producto_id` int NOT NULL COMMENT 'Ref. lógica: tbl_productos.id_prod',
  `cantidad_anterior` int NOT NULL,
  `cantidad_nueva` int NOT NULL,
  `diferencia` int NOT NULL COMMENT 'Positivo ingreso, negativo egreso',
  `motivo` enum('venta','ajuste_manual','carga_inicial','carga_csv','devolucion','reserva','liberacion_reserva') NOT NULL,
  `orden_id` int DEFAULT NULL COMMENT 'Ref. lógica: tbl_ordenes.id_orden (ventas)',
  `usuario_admin` varchar(100) DEFAULT NULL,
  `nota` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tbl_stock_log`
--

INSERT INTO `tbl_stock_log` (`id`, `producto_id`, `cantidad_anterior`, `cantidad_nueva`, `diferencia`, `motivo`, `orden_id`, `usuario_admin`, `nota`, `created_at`) VALUES
(5, 11, 0, 1, 1, 'reserva', 13, NULL, 'Reserva checkout SKU 153', '2026-06-23 17:39:27'),
(6, 11, 0, 1, 1, 'reserva', 13, NULL, 'Reserva checkout SKU 154', '2026-06-23 17:39:27'),
(7, 13, 0, 1, 1, 'reserva', 13, NULL, 'Reserva checkout SKU 155', '2026-06-23 17:39:27'),
(8, 17, 0, 1, 1, 'reserva', 14, NULL, 'Reserva checkout SKU 184', '2026-06-23 18:01:27'),
(9, 11, 1, 2, 1, 'reserva', 15, NULL, 'Reserva checkout SKU 153', '2026-06-23 18:03:52'),
(10, 11, 10, 9, -1, 'venta', 15, 'MercadoPago', 'Venta confirmada SKU 153', '2026-06-23 18:05:27'),
(13, 11, 1, 0, -1, 'liberacion_reserva', 13, NULL, 'Reserva expirada (30 min) SKU 153', '2026-06-29 22:29:21'),
(16, 11, 1, 0, -1, 'liberacion_reserva', 13, NULL, 'Reserva expirada (30 min) SKU 154', '2026-06-29 22:29:21'),
(19, 13, 1, 0, -1, 'liberacion_reserva', 13, NULL, 'Reserva expirada (30 min) SKU 155', '2026-06-29 22:29:21'),
(22, 17, 1, 0, -1, 'liberacion_reserva', 14, NULL, 'Reserva expirada (30 min) SKU 184', '2026-06-29 22:29:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_talles`
--

CREATE TABLE `tbl_talles` (
  `id_talle` int NOT NULL,
  `nombre` varchar(20) NOT NULL,
  `orden` int DEFAULT '0',
  `activo` tinyint DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tbl_talles`
--

INSERT INTO `tbl_talles` (`id_talle`, `nombre`, `orden`, `activo`) VALUES
(1, '0-3M', 1, 1),
(2, '3-6M', 2, 1),
(3, '6-12M', 3, 1),
(4, '1A', 4, 1),
(5, '2A', 5, 1),
(6, '3A', 6, 1),
(7, '4A', 7, 1),
(8, '6A', 8, 1),
(9, '8A', 9, 1),
(10, '10A', 10, 1),
(11, '12A', 11, 1),
(12, 'Único', 99, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_usuarios`
--

CREATE TABLE `tbl_usuarios` (
  `id_usuario` int NOT NULL,
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
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `is_guest` tinyint(1) NOT NULL DEFAULT '1',
  `email_verificado` tinyint(1) NOT NULL DEFAULT '0',
  `token_verificacion` varchar(100) DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_ultimo_acceso` datetime DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tbl_usuarios`
--

INSERT INTO `tbl_usuarios` (`id_usuario`, `email`, `password_hash`, `nombre`, `apellido`, `telefono`, `direccion`, `ciudad`, `provincia`, `codigo_postal`, `dni`, `activo`, `is_guest`, `email_verificado`, `token_verificacion`, `fecha_registro`, `fecha_ultimo_acceso`, `fecha_actualizacion`) VALUES
(2, 'checkout-user-1782233599@yofi.local', '$2y$10$D/9UcRxSDpFwZITxsQxMcOVi6tonXkIaQ7QHclH/UkRzH9NkXEGD2', 'Logueado', 'Test', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 1, NULL, '2026-06-23 13:53:19', NULL, NULL),
(4, 'gonzalo.mattia@hotmail.com', '$2y$10$CtzGBUhU9NsC8jGqr39G.uDcro.mlupOniVrTR.16R7SSgyQDh3g2', 'gonzalo', 'mattia', '01160436765', NULL, NULL, NULL, NULL, '31632308', 1, 0, 1, NULL, '2026-06-23 14:04:29', '2026-06-23 14:06:16', '2026-06-23 14:06:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_usuarios_direcciones`
--

CREATE TABLE `tbl_usuarios_direcciones` (
  `id_direccion` int NOT NULL,
  `usuario_id` int NOT NULL,
  `calle` varchar(150) NOT NULL,
  `numero` varchar(20) NOT NULL DEFAULT '',
  `depto` varchar(30) DEFAULT NULL,
  `ciudad` varchar(100) NOT NULL,
  `provincia` varchar(100) NOT NULL,
  `cp` varchar(20) NOT NULL,
  `predeterminada` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_wishlist`
--

CREATE TABLE `tbl_wishlist` (
  `id_wishlist` int NOT NULL,
  `usuario_id` int NOT NULL,
  `producto_id` int NOT NULL,
  `fecha_agregado` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tbl_wishlist`
--

INSERT INTO `tbl_wishlist` (`id_wishlist`, `usuario_id`, `producto_id`, `fecha_agregado`) VALUES
(17, 4, 7, '2026-06-23 14:21:44');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuadmin` (`usuadmin`);

--
-- Indices de la tabla `tbl_banners`
--
ALTER TABLE `tbl_banners`
  ADD PRIMARY KEY (`id_banner`);

--
-- Indices de la tabla `tbl_categorias`
--
ALTER TABLE `tbl_categorias`
  ADD PRIMARY KEY (`id_cate`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indices de la tabla `tbl_colores`
--
ALTER TABLE `tbl_colores`
  ADD PRIMARY KEY (`id_color`);

--
-- Indices de la tabla `tbl_config_empresa`
--
ALTER TABLE `tbl_config_empresa`
  ADD PRIMARY KEY (`clave`);

--
-- Indices de la tabla `tbl_home_edad_banners`
--
ALTER TABLE `tbl_home_edad_banners`
  ADD PRIMARY KEY (`id_edad_banner`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indices de la tabla `tbl_metodos_pago`
--
ALTER TABLE `tbl_metodos_pago`
  ADD PRIMARY KEY (`id_metodo`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `tbl_mp_payments`
--
ALTER TABLE `tbl_mp_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_id` (`payment_id`),
  ADD KEY `idx_payment_id` (`payment_id`),
  ADD KEY `idx_preference_id` (`preference_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indices de la tabla `tbl_mp_preferences`
--
ALTER TABLE `tbl_mp_preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `preference_id` (`preference_id`),
  ADD KEY `idx_preference_id` (`preference_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indices de la tabla `tbl_ordenes`
--
ALTER TABLE `tbl_ordenes`
  ADD PRIMARY KEY (`id_orden`),
  ADD UNIQUE KEY `numero_orden` (`numero_orden`),
  ADD KEY `idx_ordenes_reserva_expira` (`estado`,`reserva_activa`,`reserva_expira_at`);

--
-- Indices de la tabla `tbl_ordenes_audit`
--
ALTER TABLE `tbl_ordenes_audit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_orden_evento` (`id_orden`,`evento`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indices de la tabla `tbl_ordenes_historial`
--
ALTER TABLE `tbl_ordenes_historial`
  ADD PRIMARY KEY (`id_historial`),
  ADD KEY `idx_id_orden` (`id_orden`),
  ADD KEY `idx_estado_nuevo` (`estado_nuevo`),
  ADD KEY `idx_fecha_cambio` (`fecha_cambio`);

--
-- Indices de la tabla `tbl_password_tokens`
--
ALTER TABLE `tbl_password_tokens`
  ADD PRIMARY KEY (`id_token`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_usuario_id` (`usuario_id`),
  ADD KEY `idx_expires_at` (`expires_at`),
  ADD KEY `idx_used` (`used`),
  ADD KEY `idx_usuario_used` (`usuario_id`,`used`);

--
-- Indices de la tabla `tbl_productos`
--
ALTER TABLE `tbl_productos`
  ADD PRIMARY KEY (`id_prod`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `id_cate` (`id_cate`);

--
-- Indices de la tabla `tbl_prod_imagenes`
--
ALTER TABLE `tbl_prod_imagenes`
  ADD PRIMARY KEY (`id_imagen`),
  ADD KEY `id_prod` (`id_prod`),
  ADD KEY `id_color` (`id_color`);

--
-- Indices de la tabla `tbl_sessions`
--
ALTER TABLE `tbl_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_last_access` (`last_access`);

--
-- Indices de la tabla `tbl_shipping_config`
--
ALTER TABLE `tbl_shipping_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clave` (`clave`);

--
-- Indices de la tabla `tbl_skus`
--
ALTER TABLE `tbl_skus`
  ADD PRIMARY KEY (`id_sku`),
  ADD UNIQUE KEY `variante_unica` (`id_prod`,`id_color`,`id_talle`),
  ADD KEY `id_color` (`id_color`),
  ADD KEY `id_talle` (`id_talle`);

--
-- Indices de la tabla `tbl_slider`
--
ALTER TABLE `tbl_slider`
  ADD PRIMARY KEY (`id_slide`);

--
-- Indices de la tabla `tbl_stock_log`
--
ALTER TABLE `tbl_stock_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_stock_log_producto` (`producto_id`),
  ADD KEY `idx_stock_log_orden` (`orden_id`),
  ADD KEY `idx_stock_log_created` (`created_at`);

--
-- Indices de la tabla `tbl_talles`
--
ALTER TABLE `tbl_talles`
  ADD PRIMARY KEY (`id_talle`);

--
-- Indices de la tabla `tbl_usuarios`
--
ALTER TABLE `tbl_usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_activo` (`activo`),
  ADD KEY `idx_fecha_registro` (`fecha_registro`);

--
-- Indices de la tabla `tbl_usuarios_direcciones`
--
ALTER TABLE `tbl_usuarios_direcciones`
  ADD PRIMARY KEY (`id_direccion`),
  ADD KEY `idx_usuario_direcciones` (`usuario_id`);

--
-- Indices de la tabla `tbl_wishlist`
--
ALTER TABLE `tbl_wishlist`
  ADD PRIMARY KEY (`id_wishlist`),
  ADD UNIQUE KEY `uq_wishlist_usuario_producto` (`usuario_id`,`producto_id`),
  ADD KEY `idx_wishlist_usuario` (`usuario_id`),
  ADD KEY `idx_wishlist_producto` (`producto_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tbl_banners`
--
ALTER TABLE `tbl_banners`
  MODIFY `id_banner` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tbl_categorias`
--
ALTER TABLE `tbl_categorias`
  MODIFY `id_cate` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tbl_colores`
--
ALTER TABLE `tbl_colores`
  MODIFY `id_color` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `tbl_home_edad_banners`
--
ALTER TABLE `tbl_home_edad_banners`
  MODIFY `id_edad_banner` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tbl_metodos_pago`
--
ALTER TABLE `tbl_metodos_pago`
  MODIFY `id_metodo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tbl_mp_payments`
--
ALTER TABLE `tbl_mp_payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tbl_mp_preferences`
--
ALTER TABLE `tbl_mp_preferences`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tbl_ordenes`
--
ALTER TABLE `tbl_ordenes`
  MODIFY `id_orden` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `tbl_ordenes_audit`
--
ALTER TABLE `tbl_ordenes_audit`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tbl_ordenes_historial`
--
ALTER TABLE `tbl_ordenes_historial`
  MODIFY `id_historial` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tbl_password_tokens`
--
ALTER TABLE `tbl_password_tokens`
  MODIFY `id_token` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tbl_productos`
--
ALTER TABLE `tbl_productos`
  MODIFY `id_prod` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `tbl_prod_imagenes`
--
ALTER TABLE `tbl_prod_imagenes`
  MODIFY `id_imagen` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT de la tabla `tbl_shipping_config`
--
ALTER TABLE `tbl_shipping_config`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `tbl_skus`
--
ALTER TABLE `tbl_skus`
  MODIFY `id_sku` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=197;

--
-- AUTO_INCREMENT de la tabla `tbl_slider`
--
ALTER TABLE `tbl_slider`
  MODIFY `id_slide` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tbl_stock_log`
--
ALTER TABLE `tbl_stock_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `tbl_talles`
--
ALTER TABLE `tbl_talles`
  MODIFY `id_talle` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `tbl_usuarios`
--
ALTER TABLE `tbl_usuarios`
  MODIFY `id_usuario` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tbl_usuarios_direcciones`
--
ALTER TABLE `tbl_usuarios_direcciones`
  MODIFY `id_direccion` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tbl_wishlist`
--
ALTER TABLE `tbl_wishlist`
  MODIFY `id_wishlist` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tbl_mp_payments`
--
ALTER TABLE `tbl_mp_payments`
  ADD CONSTRAINT `tbl_mp_payments_ibfk_1` FOREIGN KEY (`preference_id`) REFERENCES `tbl_mp_preferences` (`preference_id`);

--
-- Filtros para la tabla `tbl_ordenes_historial`
--
ALTER TABLE `tbl_ordenes_historial`
  ADD CONSTRAINT `tbl_ordenes_historial_ibfk_1` FOREIGN KEY (`id_orden`) REFERENCES `tbl_ordenes` (`id_orden`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tbl_password_tokens`
--
ALTER TABLE `tbl_password_tokens`
  ADD CONSTRAINT `tbl_password_tokens_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `tbl_usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tbl_productos`
--
ALTER TABLE `tbl_productos`
  ADD CONSTRAINT `tbl_productos_ibfk_1` FOREIGN KEY (`id_cate`) REFERENCES `tbl_categorias` (`id_cate`);

--
-- Filtros para la tabla `tbl_prod_imagenes`
--
ALTER TABLE `tbl_prod_imagenes`
  ADD CONSTRAINT `tbl_prod_imagenes_ibfk_1` FOREIGN KEY (`id_prod`) REFERENCES `tbl_productos` (`id_prod`),
  ADD CONSTRAINT `tbl_prod_imagenes_ibfk_2` FOREIGN KEY (`id_color`) REFERENCES `tbl_colores` (`id_color`);

--
-- Filtros para la tabla `tbl_skus`
--
ALTER TABLE `tbl_skus`
  ADD CONSTRAINT `tbl_skus_ibfk_1` FOREIGN KEY (`id_prod`) REFERENCES `tbl_productos` (`id_prod`),
  ADD CONSTRAINT `tbl_skus_ibfk_2` FOREIGN KEY (`id_color`) REFERENCES `tbl_colores` (`id_color`),
  ADD CONSTRAINT `tbl_skus_ibfk_3` FOREIGN KEY (`id_talle`) REFERENCES `tbl_talles` (`id_talle`);

--
-- Filtros para la tabla `tbl_usuarios_direcciones`
--
ALTER TABLE `tbl_usuarios_direcciones`
  ADD CONSTRAINT `fk_usuario_direcciones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `tbl_usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tbl_wishlist`
--
ALTER TABLE `tbl_wishlist`
  ADD CONSTRAINT `fk_wishlist_producto` FOREIGN KEY (`producto_id`) REFERENCES `tbl_productos` (`id_prod`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_wishlist_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `tbl_usuarios` (`id_usuario`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
