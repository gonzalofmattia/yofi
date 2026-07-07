-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 03-07-2026 a las 13:40:28
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
  `banner_img` varchar(250) DEFAULT NULL,
  `seo_title` varchar(250) DEFAULT NULL,
  `seo_description` text,
  `orden` int DEFAULT '0',
  `publicado` tinyint DEFAULT '1',
  `destacado_home` tinyint DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tbl_categorias`
--

INSERT INTO `tbl_categorias` (`id_cate`, `id_cate_padre`, `nombre`, `slug`, `descripcion`, `imagen`, `banner_img`, `seo_title`, `seo_description`, `orden`, `publicado`, `destacado_home`) VALUES
(1, NULL, 'Abrigos', 'abrigos', 'Abrigos, camperas y abrigos', 'categoria-1782176049.png', NULL, NULL, NULL, 1, 1, 1),
(2, NULL, 'Buzos y Cardigans', 'buzos-y-cardigans', 'Buzos, sweaters y cardigans', 'categoria-1782176920.png', NULL, NULL, NULL, 2, 1, 1),
(3, NULL, 'Pantalones', 'pantalones', 'Pantalones y joggers', 'categoria-1782176603.png', NULL, NULL, NULL, 3, 1, 1),
(4, NULL, 'Accesorios', 'accesorios', '', 'categoria-1782176793.png', NULL, NULL, NULL, 6, 1, 1),
(7, NULL, 'Remeras', 'remeras', 'Remeras y tops', 'categoria-1782176510.png', NULL, NULL, NULL, 4, 1, 1),
(9, NULL, 'Mini Ánima Invierno', 'mini-nima-invierno', 'Accesorios tejidos de invierno Mini Ánima', 'categoria-1782175186.png', NULL, NULL, NULL, 8, 1, 1),
(10, NULL, 'Regalos', 'regalos', 'Regalos y productos especiales', 'categoria-1782175349.png', NULL, NULL, NULL, 9, 1, 1);

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
-- Estructura de tabla para la tabla `tbl_login_otp`
--

CREATE TABLE `tbl_login_otp` (
  `id_otp` int NOT NULL,
  `usuario_id` int NOT NULL,
  `code_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `attempts` int NOT NULL DEFAULT '0',
  `consumed_at` datetime DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tbl_login_otp`
--

INSERT INTO `tbl_login_otp` (`id_otp`, `usuario_id`, `code_hash`, `expires_at`, `attempts`, `consumed_at`, `fecha_creacion`) VALUES
(1, 4, '$2y$12$q4WauP18MW5sFvTwVBdlOOrIebLfTmUDGCZoSxPlm0gcah/AIma4G', '2026-07-01 00:26:26', 0, '2026-07-01 00:16:42', '2026-07-01 00:16:26'),
(4, 4, '$2y$12$8/1R1IlazcnVHEK6SZ96PuEChzQgKcJzzjaiuPSizPs/PQE9PJWaW', '2026-07-01 00:27:39', 0, '2026-07-01 00:18:57', '2026-07-01 00:17:39'),
(7, 4, '$2y$12$FhJgF2zvFOxjMsMKjJSYFeBhBdYLIYovZED/2yKMsXXhwBYevwM6W', '2026-07-01 00:28:57', 0, '2026-07-01 00:20:47', '2026-07-01 00:18:57'),
(10, 4, '$2y$12$opAOmAYbv//Y4.Yid2bICOZ5aVqmUiubKqHHwDI4wkkOZX4toExfu', '2026-07-01 00:30:47', 0, '2026-07-01 00:21:06', '2026-07-01 00:20:47'),
(13, 4, '$2y$12$ExFW3hr5vEXzSpKaRA8m1eV8UvMVrvIlYHT2P3l3xL7i45CziKosu', '2026-07-02 11:25:34', 0, '2026-07-02 11:16:07', '2026-07-02 11:15:34'),
(16, 4, '$2y$12$hBRl5IKM.ynO8.C/LOK6zOfCkihBYUpq4iBnH8ZOp3.HvFtSlSTue', '2026-07-02 14:32:31', 0, '2026-07-02 14:22:42', '2026-07-02 14:22:31'),
(19, 4, '$2y$12$30hHndR8zzsL72QrdFjzGON0VMOafmC3OCOki9ZlMfwHRmOXXwEAm', '2026-07-03 13:41:55', 0, '2026-07-03 13:32:15', '2026-07-03 13:31:55');

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
(1, 'mercadopago', 'Mercado Pago', 'Tarjetas, transferencia y dinero en cuenta vía Mercado Pago', 1, 1),
(4, 'transferencia', 'Transferencia bancaria', '', 1, 1);

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
(1, '165478695282', '3494486768-1a49478c-8727-4670-8cdb-a789363a12b3', 'approved', 'accredited', 'credit_card', 'master', 496006.00, '2026-06-23 15:05:27', '2026-06-23 15:06:12'),
(7, '167016809710', '3476725621-c7bfabee-23ab-4b89-9487-c5b5ac22c616', 'approved', 'accredited', 'account_money', 'account_money', 1.00, '2026-07-03 13:33:43', '2026-07-03 13:34:46');

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
(2, '3494486768-1a49478c-8727-4670-8cdb-a789363a12b3', '[{\"id\":\"153\",\"title\":\"Bandana tejida — Chocolate Único\",\"quantity\":1,\"unit_price\":5000,\"currency_id\":\"ARS\"},{\"id\":\"shipping\",\"title\":\"Envío\",\"quantity\":1,\"unit_price\":491006,\"currency_id\":\"ARS\"}]', '{\"order_id\":15,\"numero_orden\":\"ORD-20260623-82508A\",\"customer\":{\"email\":\"gonzalo.mattia@hotmail.com\",\"firstName\":\"gonzalo\",\"lastName\":\"mattia\"},\"shipping\":{\"address\":\"Fred Aden 406\",\"city\":\"Villa Sarmiento\",\"province\":\"Buenos Aires\",\"zip\":\"1706\",\"notes\":\"\"},\"mp_currency\":\"ARS\"}', 'confirmado', '2026-06-23 15:03:53', '2026-06-23 15:06:12'),
(4, '3476725621-c7bfabee-23ab-4b89-9487-c5b5ac22c616', '[{\"id\":\"199\",\"title\":\"test 01gfm — Amarillo 0-3M\",\"quantity\":1,\"unit_price\":1,\"currency_id\":\"ARS\"}]', '{\"order_id\":22,\"numero_orden\":\"ORD-20260703-3BC42D\",\"customer\":{\"email\":\"gonzalo.mattia@hotmail.com\",\"firstName\":\"gonzalo\",\"lastName\":\"mattia\"},\"shipping\":{\"address\":\"Fred Aden 406\",\"city\":\"Villa Sarmiento\",\"province\":\"Buenos Aires\",\"zip\":\"1706\",\"notes\":\"\"},\"mp_currency\":\"ARS\"}', 'confirmado', '2026-07-03 13:33:03', '2026-07-03 13:34:46');

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
(4, 'TEST-GUEST-1782233599', 'confirmado', 'Invitado', 'Test', 'checkout-guest-1782233599@yofi.local', NULL, '1100000000', 'Calle 1', 'CABA', 'CABA', '1406', NULL, 'transferencia', NULL, NULL, NULL, NULL, NULL, NULL, 1000.00, 500.00, 1500.00, '[]', '2026-06-23 13:53:19', '2026-07-02 14:19:48', NULL, 0, NULL),
(5, 'TEST-USER-1782233599', 'pendiente', 'Logueado', 'Test', 'checkout-user-1782233599@yofi.local', NULL, '1100000000', 'Calle 2', 'CABA', 'CABA', '1406', NULL, 'transferencia', NULL, NULL, NULL, NULL, NULL, NULL, 2000.00, 0.00, 2000.00, '[]', '2026-06-23 13:53:19', NULL, NULL, 0, NULL),
(13, 'ORD-20260623-F1B477', 'cancelado', 'gonzalo', 'mattia', 'gonzalo.mattia@hotmail.com', 4, '01160436765', 'Ruta Nacional 6 km 149.5', 'General Rodriguez', 'Buenos Aires', '1748', '', 'mercadopago', 'standard_delivery', 'OCA', '6 a 10 días hábiles', '{\"service\": \"Entrega a domicilio\", \"carrier_id\": 208, \"logistic_type\": \"xd_dropoff\"}', NULL, NULL, 25590.00, 491379.00, 516969.00, '[{\"id_sku\": 155, \"imagen\": \"http://localhost/yofi/imgprod/prod-13-14-1782231749-0.jpg\", \"nombre\": \"Gorro aspen\", \"id_prod\": 13, \"cantidad\": 1, \"color_nombre\": \"Natural\", \"talle_nombre\": \"Único\", \"precio_unitario\": 15590}, {\"id_sku\": 153, \"imagen\": \"http://localhost/yofi/imgprod/prod-11-16-1782231562-0.jpg\", \"nombre\": \"Bandana tejida\", \"id_prod\": 11, \"cantidad\": 1, \"color_nombre\": \"Chocolate\", \"talle_nombre\": \"Único\", \"precio_unitario\": 5000}, {\"id_sku\": 154, \"imagen\": \"http://localhost/yofi/imgprod/prod-11-17-1782231642-0.jpg\", \"nombre\": \"Bandana tejida\", \"id_prod\": 11, \"cantidad\": 1, \"color_nombre\": \"Gris topo\", \"talle_nombre\": \"Único\", \"precio_unitario\": 5000}]', '2026-06-23 14:39:27', '2026-06-29 19:29:21', '2026-06-23 15:09:27', 0, NULL),
(14, 'ORD-20260623-77204D', 'cancelado', 'gonzalo', 'mattia', 'gonzalo.mattia@hotmail.com', 4, '+541160436765', 'Fred Aden 406', 'Villa Sarmiento', 'Buenos Aires', '1706', '', 'mercadopago', 'standard_delivery', 'OCA', '7 a 12 días hábiles', '{\"service\": \"Entrega a domicilio\", \"carrier_id\": 208, \"logistic_type\": \"xd_dropoff\"}', NULL, NULL, 40000.00, 491641.00, 531641.00, '[{\"id_sku\": 184, \"imagen\": \"/yofi/imgprod/prod-17-15-1782231439-0.jpg\", \"nombre\": \"Delantal vintage sin bordar\", \"id_prod\": 17, \"cantidad\": 1, \"color_nombre\": \"Verde antiguo\", \"talle_nombre\": \"Único\", \"precio_unitario\": 40000}]', '2026-06-23 15:01:27', '2026-06-29 19:29:21', '2026-06-23 15:31:27', 0, NULL),
(15, 'ORD-20260623-82508A', 'confirmado', 'gonzalo', 'mattia', 'gonzalo.mattia@hotmail.com', 4, '+541160436765', 'Fred Aden 406', 'Villa Sarmiento', 'Buenos Aires', '1706', '', 'mercadopago', 'standard_delivery', 'OCA', '7 a 12 días hábiles', '{\"service\": \"Entrega a domicilio\", \"carrier_id\": 208, \"logistic_type\": \"xd_dropoff\"}', '27817827', 'https://app.zipnova.com.ar/track/1CcLeoqibws7Ep93fZfiWV', 5000.00, 491006.00, 496006.00, '[{\"id_sku\": 153, \"imagen\": \"/yofi/imgprod/prod-11-16-1782231562-0.jpg\", \"nombre\": \"Bandana tejida\", \"id_prod\": 11, \"cantidad\": 1, \"color_nombre\": \"Chocolate\", \"talle_nombre\": \"Único\", \"precio_unitario\": 5000}]', '2026-06-23 15:03:52', '2026-07-02 13:37:24', '2026-06-23 15:33:52', 0, NULL),
(16, 'ORD-20260701-8ADC93', 'confirmado', 'Haras', 'Pablo', 'gonzalo.mattia@hotmail.com', 4, '+541160436765', 'Ruta Nacional 6 km 149.5', 'General Rodriguez', 'Buenos Aires', '1748', '', 'transferencia', 'standard_delivery', 'OCA', '6 a 10 días hábiles', '{\"service\": \"Entrega a domicilio\", \"carrier_id\": 208, \"logistic_type\": \"xd_dropoff\"}', NULL, NULL, 40000.00, 491641.00, 531641.00, '[{\"id_sku\": 184, \"imagen\": \"/imgprod/prod-17-15-1782231439-0.jpg\", \"nombre\": \"Delantal vintage sin bordar\", \"id_prod\": 17, \"cantidad\": 1, \"color_nombre\": \"Verde antiguo\", \"talle_nombre\": \"Único\", \"precio_unitario\": 40000}]', '2026-07-01 00:19:20', '2026-07-01 00:23:56', '2026-07-01 00:49:20', 1, NULL),
(19, 'ORD-20260702-2BDA59', 'entregado', 'gonzalo', 'mattia', 'gonzalo.mattia@hotmail.com', 4, '01160436765', 'Fred Aden 406', 'Villa Sarmiento', 'Buenos Aires', '1706', '', 'transferencia', 'standard_delivery', 'OCA', '6 a 11 días hábiles', '{\"service\": \"Entrega a domicilio\", \"carrier_id\": 208, \"logistic_type\": \"xd_dropoff\"}', NULL, '2132164984651361', 20000.00, 493764.00, 513764.00, '[{\"id_sku\": 151, \"imagen\": \"/imgprod/prod-7-13-1782230965-0.jpg\", \"nombre\": \"Infinito tejido\", \"id_prod\": 7, \"cantidad\": 1, \"color_nombre\": \"Azul\", \"talle_nombre\": \"Único\", \"precio_unitario\": 15000}, {\"id_sku\": 153, \"imagen\": \"http://yofi.com.ar/imgprod/prod-11-16-1782231562-0.jpg\", \"nombre\": \"Bandana tejida\", \"id_prod\": 11, \"cantidad\": 1, \"color_nombre\": \"Chocolate\", \"talle_nombre\": \"Único\", \"precio_unitario\": 5000}]', '2026-07-02 11:20:02', '2026-07-02 14:21:40', '2026-07-02 11:50:02', 1, NULL),
(22, 'ORD-20260703-3BC42D', 'enviado', 'gonzalo', 'mattia', 'gonzalo.mattia@hotmail.com', 4, '01160436765', 'Fred Aden 406', 'Villa Sarmiento', 'Buenos Aires', '1706', '', 'mercadopago', 'pickup', 'Retiro en local', 'Retiro en el local', '{\"service\": \"Retiro en local\", \"carrier_id\": null, \"logistic_type\": \"pickup\"}', NULL, '32513213213213251', 1.00, 0.00, 1.00, '[{\"id_sku\": 199, \"imagen\": \"/imgprod/placeholder.jpg\", \"nombre\": \"test 01gfm\", \"id_prod\": 22, \"cantidad\": 1, \"color_nombre\": \"Amarillo\", \"talle_nombre\": \"0-3M\", \"precio_unitario\": 1}]', '2026-07-03 13:32:51', '2026-07-03 13:34:52', '2026-07-03 14:02:51', 0, NULL);

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
(2, 15, 'confirmado', 'preparando_envio', '2026-06-23 15:19:10', 'Zipnova API', 'Envío creado en Zipnova', 'https://app.zipnova.com.ar/track/1CcLeoqibws7Ep93fZfiWV', NULL),
(4, 16, 'pendiente', 'confirmado', '2026-07-01 00:23:56', 'admin', 'Cambio manual desde admin', NULL, NULL),
(7, 19, 'pendiente', 'confirmado', '2026-07-02 11:55:42', 'admin', 'Cambio manual desde admin', NULL, NULL),
(10, 19, 'confirmado', 'preparando_envio', '2026-07-02 11:56:17', 'admin', 'Cambio manual desde admin', NULL, NULL),
(13, 19, 'preparando_envio', 'enviado', '2026-07-02 11:56:44', 'admin', 'Cambio manual desde admin', NULL, NULL),
(16, 4, 'pendiente', 'confirmado', '2026-07-02 14:19:48', 'admin', 'Cambio manual desde admin', NULL, NULL),
(19, 19, 'enviado', 'entregado', '2026-07-02 14:21:40', 'admin', 'Cambio manual desde admin', NULL, NULL),
(22, 22, 'pendiente', 'confirmado', '2026-07-03 13:33:43', 'MercadoPago', 'accredited', NULL, NULL),
(25, 22, 'confirmado', 'enviado', '2026-07-03 13:34:52', 'admin', 'Cambio manual desde admin', NULL, NULL);

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
(1, 4, '4e36ef3faff57715e4ae0468ee0e93e27b337b586348e685e6586f27dcfabdd4', '2026-06-23 18:05:21', 1, '2026-06-23 14:05:21'),
(4, 4, '69d6628774b463a0992ae9040d4dd26d76fc1d1d0ae486818f85eda7de361f76', '2026-07-01 01:21:18', 1, '2026-07-01 00:21:18');

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
(18, 10, 'Bordado a pedido', 'bordado-a-pedido', 'YF-REG-002', 8000.00, NULL, '', '', '', 0.00, 0.00, 0.00, 0.00, 1, 0, 0, '', 0, '2026-06-22 21:22:49', '2026-06-23 13:28:31'),
(19, 4, 'Test GFM', 'test-gfm', 'gfm', 2.00, 1.00, 'test de prueba', '', '', 0.50, 20.00, 20.00, 5.00, 0, 0, 0, '', 1, '2026-07-03 12:55:26', '2026-07-03 13:31:00'),
(22, 4, 'test 01gfm', 'test-01gfm', 'test-01gfm', 2.00, 1.00, 'test', '', '', 0.50, 20.00, 20.00, 5.00, 1, 0, 0, '', 0, '2026-07-03 13:31:23', NULL);

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
('0096f5618233744df55ce46b4fe6f08a', 0x7075626c69635f637372665f746f6b656e7c733a36343a2266333035396462373339316137393165376361303533346232656461316631316130643339663165316636383761636634653763313432393435363362366666223b, 1782889259),
('00a68af3e946b388e630fdb4aabd6222', 0x7075626c69635f637372665f746f6b656e7c733a36343a2235393738323166613735323064643134633731303939666537643233306639386438363964333237316433663832393863303836333637376135343632333334223b, 1783074931),
('014116b3e17860b9743d183072217200', 0x7075626c69635f637372665f746f6b656e7c733a36343a2264383533383662336530353836336233313561346461646465393437643139653761353666313362333436646536363562373138393166383037393532373534223b, 1782906527),
('01f2b7bc6b81eae71c7f3852a76cf62c', 0x7075626c69635f637372665f746f6b656e7c733a36343a2262626262366166393962656333336365663636616163383331303933303636356130366535303234656365663636343064356666316162373930376238663936223b, 1783074750),
('02ba104f9a314ad3fc4254c3012b83ec', 0x7075626c69635f637372665f746f6b656e7c733a36343a2235626435633961626564623865383464383161636663373538363637343764623661646131353562336330366332356239346134393665373330383965373238223b, 1782881808),
('03c27e20edfbf6a684119166630d7dd3', 0x7075626c69635f637372665f746f6b656e7c733a36343a2261323963333139363762646332343063333265626134316634633931353031613335303339353537353362326266353438333732383134326434666134616163223b, 1782767811),
('0443c5f554b4d396033484d15ff0319e', 0x7075626c69635f637372665f746f6b656e7c733a36343a2230663337373035646238353135343131643838316236616332333361386166373937633264306634396238646134376264636132396263636437366364303665223b, 1782767713),
('046e90e46306e839660ab8ac5e357dfd', 0x7075626c69635f637372665f746f6b656e7c733a36343a2239383834353233306235666263626362363065363931343066326638616135633535343662363332376539323539646630656230313866333434653336373237223b, 1782886261),
('0669716921af0e47a9906d23cc989b90', 0x7075626c69635f637372665f746f6b656e7c733a36343a2262333237656532303732383330393861333437633130336337323734633563356262323034313433323633363563353735326433353531373439376638343230223b, 1782953860),
('08b6dace73022fd1d5212c0452e5f707', 0x7075626c69635f637372665f746f6b656e7c733a36343a2266356337353830356235336662633935623031396436383832353662613432376565633765333561656463313939336635393263346539356638656630343261223b, 1782874303),
('0d558ab67f3e1330dd7f112ed4b24ff6', 0x7075626c69635f637372665f746f6b656e7c733a36343a2237353736666462303737363139303961333032623331323735656664383830333363366365636230386665623863343365653134396364373161333833656536223b757365725f69647c693a343b757365725f656d61696c7c733a32363a22676f6e7a616c6f2e6d617474696140686f746d61696c2e636f6d223b757365725f6e616d657c733a31343a22676f6e7a616c6f206d6174746961223b757365725f6c6f676765645f696e7c623a313b6e656564735f70617373776f72645f73657475707c623a303b656d61696c5f7665726966696361646f7c623a313b, 1783012980),
('0dc1661064b34a25cefc03574d10ba5c', 0x7075626c69635f637372665f746f6b656e7c733a36343a2263333564396133373564663962623736653431373863656232323031633762363731386662393735633032333262653161376262626464326562633861343762223b, 1782891374),
('0fb56ce7dbf3d6b2c627aa0aed9529b9', 0x7075626c69635f637372665f746f6b656e7c733a36343a2237313236373062376632663066626536303037663934386333633862376166323731393537616534333134316465363135663737383431643332613333343239223b, 1782885935),
('138618d7e2b4f00c7a6043b4210c74db', 0x7075626c69635f637372665f746f6b656e7c733a36343a2264663230353531386166636635363931346231633238336561616165393061326564303362643730303664633330326635316438663730323338623362356665223b, 1782859000),
('14775015f3ff1b244bbb168a79c852fb', 0x7075626c69635f637372665f746f6b656e7c733a36343a2238316333616531306235623133336631633233386235353633363564303134626266376436326232386466376662666238303061313936393632386666613234223b, 1782889139),
('1575791fa33abb459216f57acdee5ff4', 0x7075626c69635f637372665f746f6b656e7c733a36343a2230343263346361383034303061613031346562663461626665343231333764616136346366313866356533666436316138616435346461306463353936336636223b, 1783084988),
('15e6b54f3d5e3a43972e60ae85ed7d54', 0x7075626c69635f637372665f746f6b656e7c733a36343a2237393964613361303336353238373037326537383164646635343462383962353637643332653637383435393565323739613931363031373239336431636331223b, 1782822388),
('165f734a0ff729e5eefe6655ef164d5b', 0x7075626c69635f637372665f746f6b656e7c733a36343a2237643637356339386230613936383361383439636636323132636663663033633431656161623439346235626130303062306561656265383133326433656366223b, 1783076907),
('16e5264c0f8fbb1aab642e7f1918d4b1', 0x7075626c69635f637372665f746f6b656e7c733a36343a2265393762326265306634356236623861353730323461353735313134303737643438666461386464663835623233306263326561663161393164396132303239223b, 1782885980),
('17e548b8455e2a7211484dfb5b586173', 0x7075626c69635f637372665f746f6b656e7c733a36343a2230316638623039643761616335366235343836323434656530646235616137346435613135303765316335383033636431383365613962333635323866363134223b, 1783074887),
('1867dc7b24ed2f0d988422fa6c2c7a5b', 0x7075626c69635f637372665f746f6b656e7c733a36343a2238383637383736343238663536383463373531646462623730643033396536313636613630383937393135626136663335623732376631326566333338653030223b, 1782977443),
('1881a6d9628bce68b69e814450fe273d', 0x7075626c69635f637372665f746f6b656e7c733a36343a2231343330356565616535383839623030623265653531353830626663653931316434316362663762303638346266343539313564623238663932313434656531223b, 1783074697),
('18ff4534e8455f7b5a54bc214685ada9', 0x7075626c69635f637372665f746f6b656e7c733a36343a2265653731623636653635373065316230303631366136376135343630613432643262333766386338353266373439643466393762393436393963613865326262223b, 1782885981),
('190f694284cfe84b39acae92ce3b0e0d', 0x7075626c69635f637372665f746f6b656e7c733a36343a2230663931626262646661336665656638633835326663323230613166323234353236363434333666643865383531616634333135333934326534323538323935223b, 1782875803),
('1977e7eb402c6a222e7e8890cc0abaf1', 0x7075626c69635f637372665f746f6b656e7c733a36343a2266366437616637373032393965393134393933343037633032613931623132303730383662333532326138643839643663303034386336316636623163623966223b, 1782906020),
('1b2f593a3dc19259592aa02f3c11da43', 0x7075626c69635f637372665f746f6b656e7c733a36343a2261613530643833613364393066303438323534366335393265306366356534313133363763663163663935343038333538373061386530346565656163366531223b, 1782887704),
('1b7c758c08b3bac95be6e255fce1bded', 0x7075626c69635f637372665f746f6b656e7c733a36343a2265633731396533306338333930306439383632373261646138343936393462663563613333623931326162333735616336313466326534353432646432336431223b, 1782878719),
('1cb17e62f2ea9b3dd6bfd9abfffaed2d', 0x7075626c69635f637372665f746f6b656e7c733a36343a2230336234313363633435386365653363313866666439363261393738383635633138653566623266316239313230636638636661393634656334616533646364223b, 1782889261),
('1cf4013573a3430c19517ad637b75c40', 0x7075626c69635f637372665f746f6b656e7c733a36343a2262666163323234636337396139373436306139366233643462313662643535626233636130656439666335383230313562613366333264316531626633626463223b, 1782885774),
('1d2f16a91707d24455aecf8bb2fef151', 0x7075626c69635f637372665f746f6b656e7c733a36343a2234653063666632376233306566623233336464393638643030346435373263376166336431363437396163346639623464396336373263623764353065363563223b, 1782932835),
('1f6821fa3f37fd716e8ee495028bbbbe', 0x7075626c69635f637372665f746f6b656e7c733a36343a2263386133343134306530623437656335313639343634623137653936373135633634643361626561353566643137393136626564653632396633353436616134223b, 1782767801),
('1fec6b4457dd028b5226366590307976', 0x7075626c69635f637372665f746f6b656e7c733a36343a2233386539663761636266326238373666366162303234616437373536333337663836323862653930376466653264383762363139643565326237643365383163223b, 1782768043),
('20e9b11b751655c3193a5f720f1c1660', 0x7075626c69635f637372665f746f6b656e7c733a36343a2264313233343234393262353361353466353638353631353130386233623437656535303235613530303633633139333564366231333466393164656336366165223b, 1782886414),
('21a66af109374bfa9752dbaf89e072b8', 0x7075626c69635f637372665f746f6b656e7c733a36343a2231333339623436323931656633346530663639366231396430363139643265343261663737343731393134336561396562663930653230323236376465356232223b, 1782920224),
('226624f8a938a12889e17a64541bf9e5', 0x7075626c69635f637372665f746f6b656e7c733a36343a2233643832343832353936363639393065353233343035363430613236643530393262636163646465653262303666363165383366336533373337373463373231223b, 1782885881),
('229d55cf46ab7ca9ed82aea682b3c0c9', 0x7075626c69635f637372665f746f6b656e7c733a36343a2230626238323938316236333764303666323466616132376638323231353737623431346663353737353331623739336132663636363939303937643639613036223b, 1782889422),
('237fda553f631d91ee5cbb395644479f', 0x7075626c69635f637372665f746f6b656e7c733a36343a2238353632326262393564363033626564346131623736393537636235633338383366653830613665376561396466343533303162343063393263376535656466223b, 1782891339),
('24961ebf9bab93f26cb88d0bc3235394', 0x7075626c69635f637372665f746f6b656e7c733a36343a2263333162356162323932356562376463393834366338333432613633363036343561653064306434326230346261313334616264613136393765643832303438223b, 1782996347),
('25c40ec55fa98340264022945075bc28', 0x7075626c69635f637372665f746f6b656e7c733a36343a2231373961623330613834633730636562363539303538393530353061623532323963316637663864643335376535653234393537323164636163623662326633223b, 1782894535),
('262c0c98fa86eb24e15cc6df36abd8b8', 0x7075626c69635f637372665f746f6b656e7c733a36343a2261383831623232633365346334333961646332313537323261303334393733343737663837346636373063366663333538326636653063326439666561323037223b, 1782767816),
('281bc3f55c4543aafb4a5aaf49ecd57c', 0x7075626c69635f637372665f746f6b656e7c733a36343a2234616263316430363037613165343339643534306238646131326264643530356530636435613432616137313564396533316162333833326263353739333837223b, 1782886153),
('2c9436e94b17d590e1efcfcaff0005d4', 0x7075626c69635f637372665f746f6b656e7c733a36343a2237323566303765326362346665353930373630373932313638366465323634346165623763303061373238373566326664396632363234616361393962633361223b, 1782849122),
('2e536f9592a2365172405502cce3571f', 0x7075626c69635f637372665f746f6b656e7c733a36343a2235306363356262386663343063613432623833366535653963393532666462613966303861656231643031333135326437343063613561393632613964616639223b, 1782923237),
('2ef4469ed7e36e9cca4d6aa9a930655b', 0x7075626c69635f637372665f746f6b656e7c733a36343a2231613562393063353236316261313739656335373362623234363561326265373963636161333734646463383337643435613863353237356338353731643263223b, 1782827960),
('2fa1ad1110a1c4bcbb852e577b433665', 0x7075626c69635f637372665f746f6b656e7c733a36343a2265313364383066376132306330373666396131363833366564373230326237323339343836316635333833326361646630646534303135633931643062363266223b, 1782772654),
('2faa0b757cc2fc6b589992d2ad7dbc8f', 0x7075626c69635f637372665f746f6b656e7c733a36343a2261363963626339333137316231656236326230383231346538366362393435663138383461633762643161326139346566393864343937333933343066616232223b, 1782953856),
('3193bf4a0dc4cc3cc33d77a019fa1f9d', 0x7075626c69635f637372665f746f6b656e7c733a36343a2234656533393065333261346363323535613231393138306266366633346334656366336562343535363566343033303864333130363334623732643431633462223b, 1783016513),
('323c3c344c586de0150ed41a9b81e9d2', 0x7075626c69635f637372665f746f6b656e7c733a36343a2239323263366265666462383933306666313064626561343636623637613030653563623539343935373964613564316562313466326331353235333738336366223b, 1782878869),
('36867deb7fb79e42239f52f8354b62de', 0x7075626c69635f637372665f746f6b656e7c733a36343a2263356336343830336266313437646361623838353830363739393534336530646234623862613030646236306534366162363933323165343834343432353630223b757365725f69647c693a343b757365725f656d61696c7c733a32363a22676f6e7a616c6f2e6d617474696140686f746d61696c2e636f6d223b757365725f6e616d657c733a31343a22676f6e7a616c6f206d6174746961223b757365725f6c6f676765645f696e7c623a313b6e656564735f70617373776f72645f73657475707c623a303b656d61696c5f7665726966696361646f7c623a313b, 1782877564),
('395cd56f789365ca0fc5edf79a5c6e63', '', 1783001706),
('39ef32a0595641b31a20d02b601e8562', 0x7075626c69635f637372665f746f6b656e7c733a36343a2261316331386466353366643266336364366236343866366331356663343465306564373631306339613833633530396533633138616336336334616333643761223b, 1782923240),
('3c921370937534740357aab755bc2151', 0x7075626c69635f637372665f746f6b656e7c733a36343a2262643965323965663761363664663239323566383262346261393661373435356436633863326637373633663762626163653831343531663965343166306663223b, 1782940140),
('3f91cc593da86fef9b91488251ac6bcc', 0x7075626c69635f637372665f746f6b656e7c733a36343a2231633664613961623033626562643939633261393764303362626632373762633033336665623538316233616639663138373833323532316438336566393965223b, 1782887705),
('400b6aa7b4616aa517e8c90ff46c7d82', 0x7075626c69635f637372665f746f6b656e7c733a36343a2261373266363861366133626134663036386434633962623737663266303165343364623564646539636339383534386437653062386361656664653861313332223b, 1782849486),
('406fa21fa272f5524d01b3fd0c581ff3', '', 1782767751),
('4180e61e5127e6b4feedc9f7a9de8598', 0x7075626c69635f637372665f746f6b656e7c733a36343a2266633164366563356538363064343565363365353732376130616535663535346364393839633138653234376436336636373863323836613136363161623535223b, 1782906515),
('424c3389f853454aafc00880f7d1c1c5', 0x7075626c69635f637372665f746f6b656e7c733a36343a2263303934636135316265303238313030353637373537626533303037303431396431306665303661653261646639393063306566356233646536613931373432223b, 1782886095),
('42adbc4ffb6dd010ee3330169257c675', 0x7075626c69635f637372665f746f6b656e7c733a36343a2265363434373338373934333236356237633333363431623265323463636265353538626231356666616465323133366430643265666265313863313535393730223b, 1782886315),
('4484ee61cf9d2ab709bd4964fc9238a6', 0x7075626c69635f637372665f746f6b656e7c733a36343a2232383034663264613131653330326336656234653438326531353066323937343632366330626437633638393763363463393964346434313338306636633937223b, 1782943795),
('452dbcaeff4d81ac33b0ca1a9c8c56af', '', 1782885829),
('46e829e5c3112afd5406d6b53875cb9b', 0x7075626c69635f637372665f746f6b656e7c733a36343a2235396639363662616132666232656561376461353361313938323633373835383663666636653561623637653530646262383464613538363636646231303735223b, 1783003188),
('4702a7c1fd9d9ddd22c3bbab16dd1d45', 0x7075626c69635f637372665f746f6b656e7c733a36343a2262386664393435616561376634616134626566363263363566316535623437653838643663326434326339376539323061383232653431353639636238313036223b, 1782767845),
('4738f649a06724bdf37bf8590ed0b05c', 0x7075626c69635f637372665f746f6b656e7c733a36343a2231396139623539656661396435663930653566343165393938313461326163356436346264313730623763396362643563316363633066633034663636616539223b, 1782772657),
('4a2068584d67cc07cf70cfaab70428d6', 0x7075626c69635f637372665f746f6b656e7c733a36343a2262323533623330306137616438393964323939396632333236306437303763663033633836613764623737643636393532303239396436333564393035366139223b, 1782885881),
('4ac057c9de600be1013bc222f7afd187', 0x7075626c69635f637372665f746f6b656e7c733a36343a2235303431646430313237613537303835663762306463343035613866323432616337643435633833653239366330313539343135613262613864656337323262223b, 1782892798),
('4c543c928a315937fe7a9e7772a201b6', 0x7075626c69635f637372665f746f6b656e7c733a36343a2263356633643430663438306161323634366233666261633439393864616433396261626464333561346531383135663335336134643035383836376235643161223b, 1782879738),
('4cd1f5f9103af391afd2dd79e3dda744', '', 1782767742),
('4d559eaae562eaa04320e559fbe5867e', 0x7075626c69635f637372665f746f6b656e7c733a36343a2266303064323665303438376433666534323864653565343431393731656131313864636231626231613633376438366431303230363661383239326633626265223b, 1782768021),
('4eb85b0352d0c84383bbe020e37687b7', '', 1782874180),
('4f6a90f4e6a82259d4916ac29487df1b', 0x7075626c69635f637372665f746f6b656e7c733a36343a2233353935303931303837366134333330653137373132663930666565613733623537323637396435393630356634616538373239393038636131363461633539223b, 1782879370),
('4ffbbc661f5c042c33d5b903d2bf2403', 0x7075626c69635f637372665f746f6b656e7c733a36343a2236353430633132643433643938363864633736663430663530623664353833653237616434393030616163663935633732613336396362336236356664313437223b, 1782879140),
('51c194367c949e327a28a33716d6fa41', 0x7075626c69635f637372665f746f6b656e7c733a36343a2239303362613961646161646134313736356138336363303331633335323133333938303730656161373930373434623363313161653934613734326331613731223b, 1782767853),
('525a39120bedd4eb95b72a51a84ae4b0', 0x7075626c69635f637372665f746f6b656e7c733a36343a2266626231323831366336656230343262336338316434363135363038623635333863616366343565663061353431323331373630626163383563393861626562223b, 1782879371),
('537b3cc8d3c8c9f4c189b722748b545a', 0x7075626c69635f637372665f746f6b656e7c733a36343a2238323334363732323339313866396537306533326665353461663933366238323965363533353735323636333063616539643564613933326531336661636432223b, 1782874434),
('548dea42753430fc1fa164e2bc3c2241', 0x7075626c69635f637372665f746f6b656e7c733a36343a2237336362323663316537346339336132616338396436616330323330623439313034363638333434333034663636306339386237643739393066643834346130223b, 1782879737),
('589367aeaf0b6c7e31e6dafcc63c29f4', 0x7075626c69635f637372665f746f6b656e7c733a36343a2236303364643636653832316532303035373239353335383838333038653765316532343231653837353337383764333764333035336332306235376663373661223b, 1782827996),
('590f74bf9982e6541adf6c9bd836b03f', 0x7075626c69635f637372665f746f6b656e7c733a36343a2238396137363538613863633637353632613439386433393236353765313435323664336330396563383162636334313866366238353761656265326230386637223b, 1782885913),
('5a5923846217208caaf8cb9474be743d', '', 1782767726),
('5abaad4d419b3b66e4f316c66f7b7589', 0x7075626c69635f637372665f746f6b656e7c733a36343a2236343064343863633131356165643030663131373261663435653132633731316238633366616638376635373635613034316664386336313239316130356339223b, 1782886380),
('5b0e8251796c4288b4b6767e0686d89a', 0x7075626c69635f637372665f746f6b656e7c733a36343a2234396366346136363830386435613336306531613134336431636537393434346432316132313064396138393833663335306635363065666635363732613965223b, 1782772155),
('5b9994ee2fd4fe2053cda29dcb6747a2', 0x7075626c69635f637372665f746f6b656e7c733a36343a2234323066346333616636643564366665653561373837663632636537623131306261376539383837326263316461323530386431326235333130623833376264223b, 1782866102),
('5bb710dfa406a92c208f7d23b757d6d3', 0x7075626c69635f637372665f746f6b656e7c733a36343a2263333965333333646565666231323561613763323730656433363635623936366362373230633962363139666464393133396361656634326434623934636138223b, 1782878869),
('5d1a10593d9394b680063cb88f7362c1', 0x7075626c69635f637372665f746f6b656e7c733a36343a2232303736343038656265623564303130636333633535616334643932373232303064333266643637656564376666383737393532613535363536333366366238223b, 1782772658),
('5d3e446d9c1014a03c16e0479952b2f6', 0x7075626c69635f637372665f746f6b656e7c733a36343a2239633965353866313433393831663063363631363536376566353437373438363339653832336161343065373561346433353765333863646230303438326462223b, 1782850052),
('5df3c0b2695eb37eb49c7e77e4672e30', 0x7075626c69635f637372665f746f6b656e7c733a36343a2232306237626139633565376531646635656139383534303731323433346663623733363065616462346261613161323339396564623630316462613730613737223b, 1782885774),
('5e7e105c870a46864faf0cca4b940e98', 0x7075626c69635f637372665f746f6b656e7c733a36343a2263373636626466333135663335663864313534646562333034323632373534333337613837383338633632666132363836333930383035353735356234383431223b, 1783075098),
('5e93d2133d57b01b8cbd1762f45bac34', 0x7075626c69635f637372665f746f6b656e7c733a36343a2264393365613930333038633234316538353165313832396633303538373937633165663765313063396266316663333062353134653830336664333861653539223b, 1782849386),
('5edb9bd3b1d0278d1061ee0370e9eaa2', 0x7075626c69635f637372665f746f6b656e7c733a36343a2266626265326561303766376435666533396431323164633237366163343565333366323633303833323738373363313063356164366239346636663733316534223b, 1782891350),
('5f1818600a4b1326dbfc3dae7e8f0f47', 0x7075626c69635f637372665f746f6b656e7c733a36343a2234653330363737316535653532626134396139333761626431383536373561333162643538333535363932306531656135663834376439383932653664616161223b, 1782825943),
('5f1cf38f59bc64726c1ad84676028d35', 0x7075626c69635f637372665f746f6b656e7c733a36343a2262376137303234396338646235346363333736663561373330333034653935383736656664373139306462643134336536313734666539623137613864356661223b, 1782764434),
('61e53898958fce3122fbc67e52a2da41', 0x7075626c69635f637372665f746f6b656e7c733a36343a2264323166303031373938313735616663656334653161663463336336316630333438646132646435393835616665306434613565663062366535333564613036223b, 1782889274),
('637244f22e03ed5112231bf40aefc7ec', 0x7075626c69635f637372665f746f6b656e7c733a36343a2231383935306230353563313431336230646335653432303066383236356666396635366139333337356335356336323331383963386538383635623133303461223b, 1782818426),
('647bbaeaf97a29e128d149da6f4583e3', 0x7075626c69635f637372665f746f6b656e7c733a36343a2234343230346534366462353432663837393861646466653431303038383836343333316238386234626432363964326638653637363466396539313761353762223b, 1782933830),
('66975f8517fab2bb1829934e3379288b', 0x7075626c69635f637372665f746f6b656e7c733a36343a2233386636393162363162343937656439633864626236336661653165386339646466366539613139303031356438316230343563653439373534343963643861223b, 1782767707),
('6709eec8a70fcb1ca9a0045760182f62', 0x7075626c69635f637372665f746f6b656e7c733a36343a2264373036306231363562666237346662356632356132373461623830346235326265383232636162356531336363666538396131626536653334313936383333223b, 1783075079),
('676035d7aa2dcdd41b03d1fc98a1f0b7', 0x7075626c69635f637372665f746f6b656e7c733a36343a2236326264396432393037333836303433666237323866346635613962353335383065653734643039326264363134343166343037393366633137363232343562223b, 1783074922),
('679ac4b67e68518e8fb5c435298757e8', 0x7075626c69635f637372665f746f6b656e7c733a36343a2262393461623065633164363565643633363639383764366233353335313834663634333339663637636261653663356261356165353939353461653062376232223b, 1782850116),
('67aa701f35ebce346b98bbf3e019dc97', 0x7075626c69635f637372665f746f6b656e7c733a36343a2239313463646364303334653835366533393035663235333035333062396331346534363162663865616563316661373933666161306361303962363838626132223b, 1782772660),
('67c31fc0b06cb204364231b6e4a29fc1', 0x7075626c69635f637372665f746f6b656e7c733a36343a2232653537383831633438653033383263396537386266623536376334656265336561666161373261363364383332323164633762363237636132656634326633223b, 1782880637),
('68a77b9aa8f7c0a8a99bbf5c0b5dab94', 0x7075626c69635f637372665f746f6b656e7c733a36343a2233323535366436376633613530653238306234373939333163386362643163333035663363613865626333333935633530626264336135663332373039613063223b, 1782888836),
('68fc459efdb804200c50960f9e0e3919', 0x7075626c69635f637372665f746f6b656e7c733a36343a2265616631663834383534383337373765326337393365316239323832343131626364663435653730373661346133383264666465383931643937316562396137223b, 1782767721),
('692c925c6ac866d3ac31edda196466e3', 0x7075626c69635f637372665f746f6b656e7c733a36343a2236653964646435363562383331613739306632653031643833303432616335646562303133343862343038366365353535646139396362346665336165373462223b, 1782908047),
('694dc14b2be805e05ab364d623a37efa', 0x7075626c69635f637372665f746f6b656e7c733a36343a2236303163613532383539363364663032663864653464356534393039303163646538636137356663303333316535363039653762306632323136323530303939223b, 1782885894),
('6a6239935c8baff8a79d23153895381d', 0x7075626c69635f637372665f746f6b656e7c733a36343a2239653366373961666530366132313466613865356634396233613164346362646136623465623864653035326631666135623236346263333439306138646432223b, 1782822290),
('6b228a5a2402aef88ec3314b452c94c4', 0x7075626c69635f637372665f746f6b656e7c733a36343a2261303131393837643032613134643337636136636139333265623534623237616236363333363133383036633364666561306263643930613733376339636137223b, 1782875769),
('6c097690cc944222640e08afbb013257', 0x7075626c69635f637372665f746f6b656e7c733a36343a2233613936643537303531653034316334303335636334303036346130353133336131333132313139663736656230346634373630336632373164313066646166223b, 1782822338),
('6c44802a2a34cc4e0b8225c0d676917a', 0x7075626c69635f637372665f746f6b656e7c733a36343a2230396663343133666365656232663636383234666230383865666334303064353135373433633066653066346163616339376533313062353161376637323032223b, 1782874734),
('6e6664fcfa8a4b10be568eae96da2c64', 0x7075626c69635f637372665f746f6b656e7c733a36343a2261636236393039633464343639643733383034323836303163333766343733363335666563306439303463666365663864613361386533396464666130393830223b, 1782886267),
('6f68417a1ee9f65788e01591632e9adf', 0x7075626c69635f637372665f746f6b656e7c733a36343a2231336231643165653633623061666339306164313666346434363366396231356334306266373538623761393036396234373636643137353066616639393035223b, 1783001715),
('6fa0130372816d040b1739a7fabf0c69', 0x7075626c69635f637372665f746f6b656e7c733a36343a2264616230306138656233313632663234613833323066383530616236373162383365343034366430646566656635313563366231613430633064383333386264223b, 1782886176),
('6fade9f1b29d4451870138037f2b2f22', 0x7075626c69635f637372665f746f6b656e7c733a36343a2238366138626630306166353265326533353561376139323261383162343235376237383162376233356163633536626230333166613635636631393465653130223b, 1782889878),
('70f5d91526af040c6fad5d741f5f562d', 0x7075626c69635f637372665f746f6b656e7c733a36343a2261343034613863333562303935636163636463353933616461343838623263383037313334363032663237393738356434353836356562363362336632343066223b, 1782866055),
('7123d6785e646fb4be561d347f860272', 0x7075626c69635f637372665f746f6b656e7c733a36343a2236643163376539303165653761653366373962623134356430666337393830373665353835343461633037373638326635396262653735316563353964363031223b, 1782878642),
('726a6fdcc3023826c17f1510088882dc', 0x7075626c69635f637372665f746f6b656e7c733a36343a2231643062393636393933396665356231323532613031363230343337343236383365333965376333646430306634613638346531643939386664653766383462223b, 1782850114),
('7280a093792b6b5012731170e311cca0', 0x7075626c69635f637372665f746f6b656e7c733a36343a2235656135653234323833323865316233386265613537353237303662616236323430643538386532353466383064343534623661613038396535353736623838223b, 1782772661),
('73bdc2284d6005ee21a04d9899babc72', 0x7075626c69635f637372665f746f6b656e7c733a36343a2263326362666466663238393466373834373730393863373064653666386630336531623731656632323333663665626162376331346136663335313961336235223b, 1782850049),
('754e9758de4dc100e8b356eff17c8053', 0x7075626c69635f637372665f746f6b656e7c733a36343a2263613933383934343532386634313239356538396162633535396134346435616435663464383537336465303331356333663331303062636631663464356461223b, 1782772084),
('760fa36378ddf2b681b9d95be565a265', 0x7075626c69635f637372665f746f6b656e7c733a36343a2266616339393335336535336639653531613333306336666535363961316437663039303931333633656363393966613432316164333766383162313039623066223b, 1782765587),
('782440090805a568a87713e7abb89161', 0x7075626c69635f637372665f746f6b656e7c733a36343a2263383763363831313534363935353730386436386561396335643835666166643334643061326138363538336262613363393665333263303863663730363062223b, 1782878643),
('784c0325a5d7b4e57a70ebb0099859e3', '', 1782877611),
('7b2a4e4d5f55ceb1ea8373bdbb415897', 0x7075626c69635f637372665f746f6b656e7c733a36343a2232626564663130336664323636316237663264353533643233356332656135393632376435613031316361626537303736313431613764313061313963643034223b, 1782878689),
('7d342af0fd296b33508e0d4d7cb6030a', 0x7075626c69635f637372665f746f6b656e7c733a36343a2238623435313666633234623530383333356365306238663265393339336635653339316566623962636635623866623932336265343265646264306439383735223b, 1782886263),
('7d8411521e800dd32a12673832a8de1f', 0x7075626c69635f637372665f746f6b656e7c733a36343a2232343634313664613532363934643932646266336333353730326635323963623433633463633735333836306135326331333636303937323566326265343062223b, 1782887688),
('8340a729f7a500dd920c401ea607f68c', 0x7075626c69635f637372665f746f6b656e7c733a36343a2239303364376263646139626133383863366339363031343033653766366130336665626161626635316537643766613232313438306337633264663933666534223b, 1782878871),
('83815ac57a5da379b52ac1295c546cc9', 0x7075626c69635f637372665f746f6b656e7c733a36343a2232303733623531383035623432333934663064313932316162353331323637643064306630333539653862353938363664366233656230353263613362303363223b, 1782874185),
('845d5540ecbbac5639359e51e66fdaf8', 0x7075626c69635f637372665f746f6b656e7c733a36343a2234613963346237323434643566646161313337316638343930366365396238333938383535646433393934353539616430336532306565393034333663666563223b, 1782849369),
('852b2d57c19f48afc739c58a99a65e86', 0x7075626c69635f637372665f746f6b656e7c733a36343a2239663731653031343738616535613262613531323835333964623366376131626366363662353864656236386436356534393566633965653532313130636632223b, 1782886089),
('871ba418d83c8d437115460b37723849', 0x7075626c69635f637372665f746f6b656e7c733a36343a2238646662393665653833353038313436393635326666353433643136666135663764363762303461643962363133303334386130373936353233396464386232223b, 1782886218),
('873c80365c850d9cc787acfed548463f', 0x7075626c69635f637372665f746f6b656e7c733a36343a2234336162306361663436333063336566623931366535396161653761323131316437656665353038306562356539653965663063313739326432613333653962223b, 1783074866),
('8788e99344acc0346b8606ec4b4be6bb', '', 1782874180),
('894cde19b42b6a801b5f727e50e762a4', 0x7075626c69635f637372665f746f6b656e7c733a36343a2236353061373064393237373564396630303638323334656435353361326233396464393730623832303139383530646465643164636466336566333837336433223b, 1782855592),
('8b472270d3a615c9fb3878c49893496c', 0x7075626c69635f637372665f746f6b656e7c733a36343a2263613239653838626634336266386335353866313431383666663936633736376237613131386565353938396566373932623739356161396436316335616438223b, 1782886444),
('8bbf2accc1ad08afc69e48c4c40a06a0', 0x7075626c69635f637372665f746f6b656e7c733a36343a2232373930666263656535653465376234313163306665383763666361396463626430646161633364356634333433343032383931393033303063313566613232223b, 1782881771),
('8bcae86d81e83c6a73e474564e0c995c', 0x7075626c69635f637372665f746f6b656e7c733a36343a2230616437396464323937383039613539316563643138313534376132396461623264353663353361386335313736396662633132653366346130396665303063223b, 1782885755),
('8c3576cfe5229c79142ab777ab6a8fe5', 0x7075626c69635f637372665f746f6b656e7c733a36343a2266363735366165653033646432646436643633626666303032663162326663393261316436616136663666656663383166333334626464363837633366613035223b, 1782850115),
('8c95a183ce3a3f1091ce2e6aed91eaac', 0x7075626c69635f637372665f746f6b656e7c733a36343a2264623232343333626133653530653938346538323232623962326432323838333865623436326334356431633164613336373335343836666633303066356663223b, 1782899046),
('8f600871614dd90a6e624e6d5d29f514', 0x7075626c69635f637372665f746f6b656e7c733a36343a2266666664333930643635363264376438303164623532626364663462653731333766396563616538336137373564393963663264616131353233393536616662223b, 1782885802),
('8ffd5cf9eaba7edfd3ea8ffd417e958b', 0x7075626c69635f637372665f746f6b656e7c733a36343a2230336664646362366439663136366533666134356461636134616361636537393036353937663764303130346332636431336466363538633931346163316233223b, 1783020468),
('921d9324ceaa2f13463098daf6c3ca7f', 0x7075626c69635f637372665f746f6b656e7c733a36343a2232343438623031383536396330616439386137313434373264313732306532393538376461336432326639313362616431376435363939616438386439616164223b, 1782886038),
('93af289b0f11163623b08b72c958c8ce', 0x7075626c69635f637372665f746f6b656e7c733a36343a2233316634333962633363613838633366616632383961343338323131626663346262393237373365323836356437356233353464363064616564333865336134223b, 1782827991),
('94c69c386ceb84fc938466b3cf313e76', 0x7075626c69635f637372665f746f6b656e7c733a36343a2263343266653134326437366266333332313064316335653636636339343964633266383539343166643535393735346234666365613361326535346632613366223b, 1782850047),
('953c7c5711fd99f8cc480d10fa35ddb4', 0x7075626c69635f637372665f746f6b656e7c733a36343a2236393166373964376632396463636138376331653066623839346332363839366234386666346362366131643737653462323831343637363462316462653362223b, 1782771874),
('95957690ba85303ab617a26d0b5447ca', 0x7075626c69635f637372665f746f6b656e7c733a36343a2231336163613230633434616563326633623836363266663264373030633366323761356131336536623731633733623533366131313234616134353935666635223b, 1782933831),
('963aaea423b5000f5b1771f68dc4cb3e', 0x7075626c69635f637372665f746f6b656e7c733a36343a2238383863346363636161613834396435616461333862333435306133326661616566313733623762643865336236326334633533393461616333623536326131223b, 1782827997),
('97e2c9addcff477ca812e10aff2fd796', 0x7075626c69635f637372665f746f6b656e7c733a36343a2265613638376236316566626137323762626337396135303639383366336531393637646364343631356438363536663931353061313730623237376432336163223b, 1782874171),
('9997b67e125bd1ae232ad2e7c568c359', 0x7075626c69635f637372665f746f6b656e7c733a36343a2262363132303339616534353366616130656235646166656463383864353735353363353231656633326137343933306133613961316563363666633439636538223b, 1782855582),
('9ddcb06b6a038d0ce62edc4dcd5e01f8', 0x7075626c69635f637372665f746f6b656e7c733a36343a2239306132393565613335643234343339633032666135653561306234333337396566636164366530396163366134353561616236336639373065666266333865223b, 1782849691),
('a06ac06192cd3c73a6c469f59616977a', 0x7075626c69635f637372665f746f6b656e7c733a36343a2235393631663363396539353533646331663536313339303166626331666361343838303532616130616163616431656235386630613463386437376162656538223b, 1782849968),
('a0e6670506ef26f4438f29f862a2a65e', 0x7075626c69635f637372665f746f6b656e7c733a36343a2230313736623062333837656432303137383535383230643835343530303230663335363435343239386536346538666432633433373036373331326639366431223b, 1782877583),
('a14bde58c23d8604dac4958f2693bd7d', 0x7075626c69635f637372665f746f6b656e7c733a36343a2231313862633762656434373865383237633230613236393063653362366534333739353931396332323131363934663461623534653036626263636338633539223b, 1782891362),
('a25092206fdd9389e323aee78a0a3998', 0x7075626c69635f637372665f746f6b656e7c733a36343a2233613330393664343836633038393437313438353330323161333238373864336462663233383161613138366565343337306138633133393565616136333130223b, 1782874445),
('a458f28ddb5d15a9b6cf012bdb248311', 0x7075626c69635f637372665f746f6b656e7c733a36343a2261366237383837373138373661333234323231383764336233326531343666306663376163316461313839613436363331633439346563326232333861383238223b, 1782767836),
('a45e77da29122ce82274d6dca0b891ab', 0x7075626c69635f637372665f746f6b656e7c733a36343a2233663837306437643864313837643862373739393533363633323935616266626231376532663537616531636231653466643437363064346263353135353234223b, 1782849634),
('a55e0c14ab4dbf43bd7699acc96be4e9', 0x7075626c69635f637372665f746f6b656e7c733a36343a2235366166633364633966633737303364353364616534353634383336303438616338303337306431346638663039346137646338393662313963366361633931223b, 1783084850),
('a5a8af0829501fd0226347822df52851', '', 1782917628),
('a74951ff75e9aecf940d13982b1299bc', 0x7075626c69635f637372665f746f6b656e7c733a36343a2230353732386134366633333532313965326563376138396138316431313038393637333265363238636131323035376635323638626233343933373565343366223b, 1782818476),
('a7623ed9da3a367198b9ddbc9c778196', 0x7075626c69635f637372665f746f6b656e7c733a36343a2265646433393734653037333635316162333831666461666230326232326630656462666433336266623833343836663237376632643031343464643432336332223b, 1782775617),
('a77e11a136e21404b7874b2c5cff5f45', 0x7075626c69635f637372665f746f6b656e7c733a36343a2232393065366432303839393565666432663733643037323738323165343565643462376339643438656631333863333861386133393962326139306364393063223b, 1782931298),
('a90e0ab9d5be42c30085dcb1d677f83a', 0x7075626c69635f637372665f746f6b656e7c733a36343a2233383138623061306437666537343631626338633931333134383236326262666530386636316437396632666163646133616538393261393338376663303631223b, 1782886269),
('aa8f08aaeffbe3c113a46c6e1cd4c7fe', 0x7075626c69635f637372665f746f6b656e7c733a36343a2233373766663166643861643633316331336339343931666663326232646230376635623063316331366566623638333562653930653732346266663261383032223b, 1783096728),
('ab46d99a83caa8d9b3803667988720ab', 0x7075626c69635f637372665f746f6b656e7c733a36343a2261396439313533323736373831616661636537396238373436383530376565313062356131376261633634383530323438663133663431643531326138353633223b, 1782880684),
('abfa96ad46d313a2af6a98e1dfa4dcf4', 0x7075626c69635f637372665f746f6b656e7c733a36343a2263613538663336303565313935373061323035376234646466343630623638366239653631373731363630653366316334326364646164323064363637663031223b, 1782886040),
('ac438619f76c110e3506bc84c292ef6d', 0x7075626c69635f637372665f746f6b656e7c733a36343a2235376333616435636433323438613439633130646438373438303030646235326339366239323138626137653232636163323165653931376166613933313461223b, 1782995325),
('ade6a3220c33de0536bd2f20c3a3e195', 0x7075626c69635f637372665f746f6b656e7c733a36343a2262393564633631616165303265393036316633643039363631383861383137653336633232626266386439353030616131336136666163363737626334643963223b, 1782849611),
('ae3ca509b7dc47f1bb7d54005df3169a', 0x7075626c69635f637372665f746f6b656e7c733a36343a2239373239356435336563656635346664636662393261303433633036326366393035336263623539383631613633316464663463366234333934386237353266223b, 1782850051),
('aee7def7716820ef34f511fd75ad9bc9', 0x7075626c69635f637372665f746f6b656e7c733a36343a2238393430393939303465326636346133366632333732303666626132323631393066646563363038343137646633356466356666396636303736363463373030223b, 1782888984),
('af33e6deda91fc193af526957a6f2f63', 0x7075626c69635f637372665f746f6b656e7c733a36343a2266353932346530636632363035366137643632393132383633343839643038666439333763333633306339646337616330323530376165323134363436323337223b757365725f69647c693a343b757365725f656d61696c7c733a32363a22676f6e7a616c6f2e6d617474696140686f746d61696c2e636f6d223b757365725f6e616d657c733a31343a22676f6e7a616c6f206d6174746961223b757365725f6c6f676765645f696e7c623a313b6e656564735f70617373776f72645f73657475707c623a303b656d61696c5f7665726966696361646f7c623a313b, 1783096684),
('af666d3f728d1ec5dd98c5c308ad82ca', '', 1782767741),
('b011e3586030863259c831435f22b094', 0x7075626c69635f637372665f746f6b656e7c733a36343a2230356466663231306335396633666662306530626264313366643462663833316632656561623563396531653036366331633963313231343463346330336634223b, 1782878708),
('b01dec933dc47144df298a4daeb178d6', 0x7075626c69635f637372665f746f6b656e7c733a36343a2238613837333064633337643861386437343465366330313930366166356136613061306430666563353863326634613732633734633161633035353638316636223b, 1783074888),
('b023ddcdca938189620cb7c13cce18a9', 0x7075626c69635f637372665f746f6b656e7c733a36343a2230343565323633306336653436636232623331646439636233303936666630636530613233313265333366383366313731323061363564336239316533336662223b, 1782917624),
('b137dd6cbcb434c17f268d153bad272a', 0x7075626c69635f637372665f746f6b656e7c733a36343a2263356238633364313863316663616531366461613235616366393330376334363431653866376230396463663738633531393837646537383833313563623437223b, 1782765223),
('b180a999658aa51b7c94bd3ac5a77aa5', 0x7075626c69635f637372665f746f6b656e7c733a36343a2264646139633936636436306335663334353766383666626139346231623538633337326362376432623333326464306138616539663766643839363165323266223b, 1782885914),
('b469da51f17401f505ea9e517d359a17', 0x7075626c69635f637372665f746f6b656e7c733a36343a2266343530383861313138323335636630653061633337336562306463633731663039343866323266613333666138633566656334653634386365306334376436223b, 1783096728),
('b6e240184c24726563176df9b9fa03bc', 0x7075626c69635f637372665f746f6b656e7c733a36343a2233356565313839313236613431333132623537636364623138613366633832333836343361306262303164393238326539333166333137363165346532613634223b, 1782874169),
('b7f48a301c4b0206e5aa7667c56c57a7', 0x7075626c69635f637372665f746f6b656e7c733a36343a2232313936643032653737343464666431316631633064666561383832613065633137333738623832653132613935373334383235386138396537633532343530223b, 1782874304),
('b87930dc768b553b80c4ceffa9aa6b47', 0x7075626c69635f637372665f746f6b656e7c733a36343a2234313234333332323537623061346365386432633036343538343933613738353733346562353733316165393765373837376364396262396438303864383535223b, 1783094032),
('b9176d2e9766bc5cf80bca825715dc02', 0x7075626c69635f637372665f746f6b656e7c733a36343a2232366462303661386638313536346564343866396130343563326430663766626431373464353665333131376232653165303165363335343866383835366330223b, 1782772179),
('b95e6e304ea52e4a6443817e36ddcc55', 0x7075626c69635f637372665f746f6b656e7c733a36343a2233663734623832396133366536336538326562396431316161343738616234303138363265323138626366666633373032306434383961393366656565386366223b, 1782886263),
('ba6cd9b945e8cb3bf03264605aff4a46', '', 1782885772),
('bc099bfb48071aa477954fb7493f559f', 0x7075626c69635f637372665f746f6b656e7c733a36343a2230396132643361613736356337313139626437373666346135366136626661353533326537666166393862366439326136366562313035643463353832346134223b, 1782886218),
('bd15d136691ebe8b94e78e5c1241bcac', 0x7075626c69635f637372665f746f6b656e7c733a36343a2232393134656662343537393535623039393930333565653165623530386265336663373033326532343565346436343365326537396563616534346531366361223b, 1782879140),
('bdeb390954c744755c277cfc991eca45', 0x7075626c69635f637372665f746f6b656e7c733a36343a2234336261313238643263636630326462666139363839343934333334376365646632333633386338303437393532333238376465646163646334373236373837223b, 1782878882),
('bf0f42105396bca9d906acd7ad914824', 0x7075626c69635f637372665f746f6b656e7c733a36343a2266396630383838356663616239626565366463643037353037366333313639336634323632666336363636616631306630653965636437626237343563623836223b, 1782866053),
('bfbf5b9b9cd8c670a0b0c31ff49c1c0f', 0x7075626c69635f637372665f746f6b656e7c733a36343a2235333966373935666561663164653138323165323739336531623132393730333433396365366233636338326635613935386265666561613966373836303635223b, 1783096728),
('c0db4ec0965290b0c9d49dc80f01aee9', 0x7075626c69635f637372665f746f6b656e7c733a36343a2232316563393263326661346265326332336662303365643532366634646630646361333264373432323034653532363931626263366436613066373566393731223b, 1782885935),
('c16c2a577a286bccdfddc91c6f208f1b', 0x7075626c69635f637372665f746f6b656e7c733a36343a2230656562353566613430356331636235643439623531633661313663356538616339333132636130396334303638643531613163333933636534373630663064223b, 1782855277),
('c1c8f52439bb45f702c6a437c71634f0', 0x7075626c69635f637372665f746f6b656e7c733a36343a2233383534353234616163366234333062643335643366393438633632313465373739623861376236303265613966343839633939343531303135636234316366223b, 1782886153),
('c3f9da138c0318e14a5f900c060ea4f6', 0x7075626c69635f637372665f746f6b656e7c733a36343a2239343535626437376362373539363232383966306365313461663466633536626632366534376136326535663234666235633663356533303336623463653639223b, 1782765829),
('c406d7bb2c32ebbace4f14d6c0c29566', 0x7075626c69635f637372665f746f6b656e7c733a36343a2263616531623333663465343834346534373530393230343062366362323136343934656337656431646437336162643438373535343261393333323663393539223b, 1782886095),
('c5a3aef4a72308193b03e0a6eb295d2e', '', 1782981073),
('c6044319bfbe149b526f51c5fe89f288', 0x7075626c69635f637372665f746f6b656e7c733a36343a2236623031643936316439313630616635346139386131373637663965326530613566633334326239306230343332643437393730643763373364623132373837223b, 1782849045),
('c6a024aef49a87372250fd724ea34c12', 0x7075626c69635f637372665f746f6b656e7c733a36343a2239313636653262323635633562346436343337356133326465613336663338656565653330666432353934393963393063376138636162623631343630346637223b, 1783075057),
('c72b8d17e046532153850549f73fc84a', 0x7075626c69635f637372665f746f6b656e7c733a36343a2235343663346666333563616663333765633233653862656530613861333739393966333039313132376135656462376232643133633934613538336639383538223b, 1782885762),
('c971d694d95fbbf343ec0f90ecb86373', 0x7075626c69635f637372665f746f6b656e7c733a36343a2237613935346138643439383236346135333037343537653132623966663137663137636566383631386535663633333237393435613063376238336662306461223b, 1782886421),
('c9950bbb01e875803af3ee1abfce8cb3', 0x7075626c69635f637372665f746f6b656e7c733a36343a2237376237613631393331316463326564336238326561373461396265303238396665386465376564393161653338623135323165366366373433326639626632223b, 1782767803),
('ca7952c20c16e05275ac5a839ff8ac21', '', 1782874314),
('cb1c969d4774db8e1e186a6bd2d6f637', 0x7075626c69635f637372665f746f6b656e7c733a36343a2238623163323735303139313664653634633033623530656137393332633436636166663332613535376362313136373432396133656265653666613633643535223b, 1783002654),
('cd4882cffd3e092eddadffdddd19c454', 0x7075626c69635f637372665f746f6b656e7c733a36343a2263616464383836303231346537633265353635323964666362663162623365343863663335306566346137393134663237343063633466356337636238313131223b, 1782885799),
('cf16d52d2549b547c70ad20c00a3ca2f', 0x7075626c69635f637372665f746f6b656e7c733a36343a2262656232633235346261636435316263313963386233393663613632336161373161353163336234623635303138356234616462343836333431306639333031223b, 1782886421),
('d064f3c7451d297bf8a2e48b1dc30918', 0x7075626c69635f637372665f746f6b656e7c733a36343a2238656639383638643861333438393037386637346236393330653634336563616466613363343138323337653235626335316264316633336463326339626131223b, 1782874432),
('d1b13bb430efa3655c4ff9b66b94e776', 0x7075626c69635f637372665f746f6b656e7c733a36343a2237373830326431326264616639326235366530366263633538633439326233373839626238633638626534633563353634363765386630313231613133313933223b, 1782943523),
('d1b39aeddb233a059ea7884e1f9b659d', 0x7075626c69635f637372665f746f6b656e7c733a36343a2264363036336434393262626361303835366666623232336139373634653239336130396633333834663465626132356463616462363263323563656237313036223b, 1782994932),
('d33462ecbf159207569bbd8ce794066b', '', 1782917679),
('d3918dafed5462130dd0b41f988725a7', 0x7075626c69635f637372665f746f6b656e7c733a36343a2266373136303861666562303632643435656430396433666162336331323339396661363765303434666435343632333162346333313739333039316566643634223b, 1783074738),
('d65a92f3e514461bc0ee20b9541a6ed8', 0x7075626c69635f637372665f746f6b656e7c733a36343a2239656631306164653463343037633535323634653034386233643039376135323133326461313637356562346134613965376635343466303861303134623334223b, 1783074709),
('d713b3d4dfaebd4b5bed1ef26b2737cd', 0x7075626c69635f637372665f746f6b656e7c733a36343a2263396466383336633132613630656465303161333730306630656264373033623239343563643436663864663836663435393032346266633465353161353464223b, 1782767809),
('d732649a498890c720fdf1bd61d8bf0f', 0x7075626c69635f637372665f746f6b656e7c733a36343a2265383566333335623764366366383866353135616331663934643962316164303863386263316439316331363336336361373031666634333766653264353131223b, 1782828031),
('d79954f222a0569a8b716a9513fc81e1', 0x7075626c69635f637372665f746f6b656e7c733a36343a2261666632646232303233653037313539313863336635656335363539653630343735363139623933363433386232396337376239336434306565383936393264223b, 1782768039),
('d7f4e2b7ad9b82192fe1dbc92e8e49e4', '', 1782980949),
('dc4b89058c81969ba4d388ee175b18be', 0x7075626c69635f637372665f746f6b656e7c733a36343a2264333263343838346666326236393632323865396135323066333264663835336161313163656338643135663831666365323765663231363465613461323963223b, 1782849937),
('e001989f65dae2ea7fa57b0dd5bd2262', 0x7075626c69635f637372665f746f6b656e7c733a36343a2236613764356531326135623337356630633630653339356261643038346530326635623666373435653533323138643363366438363737613963636635323438223b, 1782886050),
('e111ac7e4ecef386eded6cc6e6b17715', 0x7075626c69635f637372665f746f6b656e7c733a36343a2231323731393533613334386437333030363862646361373737396466376530326533666437303064336262653730663861316561623563633765623433386639223b757365725f69647c693a343b757365725f656d61696c7c733a32363a22676f6e7a616c6f2e6d617474696140686f746d61696c2e636f6d223b757365725f6e616d657c733a31343a22676f6e7a616c6f206d6174746961223b757365725f6c6f676765645f696e7c623a313b6e656564735f70617373776f72645f73657475707c623a303b656d61696c5f7665726966696361646f7c623a313b, 1782875827),
('e1efe0db9eec5887de04506d7b62805d', 0x7075626c69635f637372665f746f6b656e7c733a36343a2236376664643033356132353664323131653566376339613837323435333030633433323066313566663962623535396232373332336531636139386337393836223b, 1782885744),
('e22786fe5b659793c7a1ac4a642ed6ff', 0x7075626c69635f637372665f746f6b656e7c733a36343a2235383962346330333831376337656438653738636166383163393261313730656630653764333837386661343832353035633039656430663466643137636533223b, 1782888837),
('e3b4cc9577400cc5b05fe13f83eef3f5', 0x7075626c69635f637372665f746f6b656e7c733a36343a2262373437343664633931653237323739353466373536396662393265623033643534666364353066363239393237303631363636633163636133623034346532223b, 1782829515),
('e50a6367ce820563d2c07c4b9279c3ca', 0x7075626c69635f637372665f746f6b656e7c733a36343a2235636232643938643737343061343165633832633739333762353331616633363530373563303034643032643535353835316665633231653835613765383833223b, 1782767685),
('e598b8b36633db4c6542ce4b723f72aa', 0x7075626c69635f637372665f746f6b656e7c733a36343a2235663764353234303564306137633534343965633664343538623633386436383964646264306562343866333539333539636234353365653934313135666664223b, 1782768189),
('e6294eecb4e66721da503cfdd21e522f', 0x7075626c69635f637372665f746f6b656e7c733a36343a2265306562326164333839623437386236333561316432386230396536396164643064626334323536373936653264643335363037316162393937366236393636223b, 1782866095),
('e6e81711b920c73b769bf26015d49258', 0x7075626c69635f637372665f746f6b656e7c733a36343a2234666663663339643033623832323034623331366338356561616432373635663266346333373135363764666333613733383235636333663863393936353765223b, 1782894532),
('e6fe88755a83b1f8735c8da532014a1e', 0x7075626c69635f637372665f746f6b656e7c733a36343a2234396365653366383737626131383134663138303663363163373265326661343565343361326638373433326236353230616639393236363030663061656334223b, 1783025087);
INSERT INTO `tbl_sessions` (`id`, `data`, `last_access`) VALUES
('e7ce56f9c6502c797f82547e4b9d197c', 0x7075626c69635f637372665f746f6b656e7c733a36343a2237646561396430366138663563666563343966386565393938343963393635663665303065303465663261643434306466623734366635626238396566343630223b, 1782892797),
('e87bfa39acdacb2dd1b76d598aa4de1c', 0x7075626c69635f637372665f746f6b656e7c733a36343a2263636438313238336638636166656364613331376534353438336433653134346261643262363134396165313065383939383538343762316464373237646366223b, 1782878708),
('e8fdd83adb34b953099ee55998617c98', 0x7075626c69635f637372665f746f6b656e7c733a36343a2263653433326437346638613763383433343464373634363838636638363939396466303962623835306365643063616535663862656561356533666664316537223b, 1782899048),
('e947d0262048b975d6390fe2f42a171b', 0x7075626c69635f637372665f746f6b656e7c733a36343a2235663536656235303866663363623938306231313930376532663239363338646136363835633366383233353039313330333638636431343461373032393131223b, 1782885933),
('e9957ff01016ec94b19544e103342d72', 0x7075626c69635f637372665f746f6b656e7c733a36343a2237363661353062636435633461643438666532626432376561373833306364306536333531353632616332356131353330393965383461646434343637393962223b, 1782874174),
('ea51c62d37139eaf263014e904cd6bd7', 0x7075626c69635f637372665f746f6b656e7c733a36343a2231336138656431653061366434346137653363326437336336303932636631643536393461323630353832393431656266323339366530363330616264613230223b, 1782827999),
('eb026587c0f33dc24f93dbdd1b4b49e5', 0x7075626c69635f637372665f746f6b656e7c733a36343a2232303964366230366161646232303237356666633361643239343636376533636236386432643030333838393037613866343730313539313163373030623535223b, 1782875098),
('ebe13747383b57fd441f9316fa1d5708', '', 1782874189),
('ebe6e078dc4b9b40a721561d80dea565', 0x7075626c69635f637372665f746f6b656e7c733a36343a2236643932653263393235303034353236356530323062333265343062633265653637626463353431333864366539326535653838336637313862636162653766223b, 1782886078),
('ee4ae5d8f39bcda15070725de9f11191', 0x7075626c69635f637372665f746f6b656e7c733a36343a2237613165393630333039623265646563646265353738346361353035363365636638363734643233363238316466393234333631613766313435383533396663223b, 1782814999),
('ee91a9844e89e1a8903deeb9ad6286e8', 0x7075626c69635f637372665f746f6b656e7c733a36343a2237656564326638396234313166313131353339653334366465643466653939343863633638386138303264316331333335366435323731373831346166336265223b, 1782768019),
('f0dd30cb6a0fd57dda22c9fba8abbe79', 0x7075626c69635f637372665f746f6b656e7c733a36343a2235623361353863376565336330333430626264393838356562386531343633656565333331653761343132343437643439396435643133393264393638373964223b, 1782849646),
('f108760002f2dcf439734c9c70bf2638', 0x7075626c69635f637372665f746f6b656e7c733a36343a2263333031396239613965393830613839383138323665376437626362636531656434363334323763653438613866316338396135633163353830663866656265223b, 1782849935),
('f15a023ae13f55674c5ec328f70df9b5', 0x7075626c69635f637372665f746f6b656e7c733a36343a2239646266393533613466353739663938343338393834386162306131333134623132323436663835653337646538393964356639633238396363616438313737223b, 1782891338),
('f1d886f9fb09aa74fd38b24f7dbd4e25', 0x7075626c69635f637372665f746f6b656e7c733a36343a2231316138313438303164373631363438303333616635396233633562393866353439613862636137343565613862643930653137336238636463636234626262223b, 1782849377),
('f213937a36c4f9009e4f2fc47abba2be', 0x7075626c69635f637372665f746f6b656e7c733a36343a2235623630623333643732353637303964623062366361663131666630306231666264333230376538386163663137383832306337333031343962636665616537223b, 1782887722),
('f414ca72cf8d2c092d40989dd8d98e3b', 0x7075626c69635f637372665f746f6b656e7c733a36343a2234623232376434613230663836353630653164623037356237653762393636633433633233393434623738326130613463623130346335353731643838643638223b, 1782886078),
('f7123d2bc7d9307be798b0b76f04985a', 0x7075626c69635f637372665f746f6b656e7c733a36343a2233663662626433356330373266323735306365303035656338616265616630386636663733633336663666613235376437613933623766653864646439653731223b, 1782774509),
('f9461598e49fe25955833bef205ac6ac', 0x7075626c69635f637372665f746f6b656e7c733a36343a2239373161316462663034316530306166333236663334366164346464336166303331316133376433306239656465383132373334383036393166333761366165223b, 1782891364),
('f949607843b2f562799ea9d5f4871cfd', 0x7075626c69635f637372665f746f6b656e7c733a36343a2265663665343966626237396362323861353163613166643136636665393036303264393666366465386138643931663531323764306464353163666133306439223b, 1782885979),
('fa6059fbb02a3793ae87e5e65ba06c14', 0x7075626c69635f637372665f746f6b656e7c733a36343a2265323763346538633565633063353061383836613065383063663737646666666238636666376537313965356166313164666235393538383664646438326431223b, 1782767806),
('fcfac2811087c20070f29c05f00cef8b', '', 1782767753),
('fdac7bcc91c7e0f4400be4cdac782c36', '', 1782874189),
('fde01846fc0d5be1eb5f3a2d079763eb', 0x7075626c69635f637372665f746f6b656e7c733a36343a2235363037303137616161393931393639663237626364666231363032633162663864373163616439313734626162626131333130633962336437636237646137223b, 1782825894),
('ffcc9b7062a16612b33440981d770e02', 0x7075626c69635f637372665f746f6b656e7c733a36343a2236353133376631353834316365636434666139306638646165303234643764666666393733636539643134393432636361323038353634326130356438323533223b757365725f69647c693a343b757365725f656d61696c7c733a32363a22676f6e7a616c6f2e6d617474696140686f746d61696c2e636f6d223b757365725f6e616d657c733a31343a22676f6e7a616c6f206d6174746961223b757365725f6c6f676765645f696e7c623a313b6e656564735f70617373776f72645f73657475707c623a303b656d61696c5f7665726966696361646f7c623a313b, 1782877574);

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
(5, 'pickup_enabled', '1', 'Si retiro en local está habilitado', '2026-07-03 12:54:40', '2026-06-22 19:06:27'),
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
(151, 7, 13, 12, 'YF-MINI-003-UNI', 10, 1, 0.00, 1),
(152, 7, 3, 12, 'YF-MINI-004-UNI', 0, 0, 0.00, 1),
(153, 11, 16, 12, 'YF-MINI-005-UNI', 9, 1, 0.00, 1),
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
(184, 17, 15, 12, 'YOFI-17-15-12', 10, 1, 0.00, 1),
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
(195, 17, 7, 11, 'YOFI-17-7-11', 0, 0, 0.00, 1),
(199, 22, 10, 1, 'YOFI-22-10-1', 4, 0, 0.00, 1),
(202, 22, 10, 2, 'YOFI-22-10-2', 5, 0, 0.00, 1),
(205, 22, 10, 3, 'YOFI-22-10-3', 0, 0, 0.00, 1),
(208, 22, 10, 4, 'YOFI-22-10-4', 0, 0, 0.00, 1),
(211, 22, 10, 5, 'YOFI-22-10-5', 0, 0, 0.00, 1),
(214, 22, 10, 6, 'YOFI-22-10-6', 0, 0, 0.00, 1),
(217, 22, 10, 7, 'YOFI-22-10-7', 0, 0, 0.00, 1),
(220, 22, 10, 8, 'YOFI-22-10-8', 0, 0, 0.00, 1),
(223, 22, 10, 9, 'YOFI-22-10-9', 0, 0, 0.00, 1),
(226, 22, 10, 10, 'YOFI-22-10-10', 0, 0, 0.00, 1),
(229, 22, 10, 11, 'YOFI-22-10-11', 0, 0, 0.00, 1),
(232, 22, 10, 12, 'YOFI-22-10-12', 0, 0, 0.00, 1);

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
(22, 17, 1, 0, -1, 'liberacion_reserva', 14, NULL, 'Reserva expirada (30 min) SKU 184', '2026-06-29 22:29:21'),
(25, 17, 0, 1, 1, 'reserva', 16, NULL, 'Reserva checkout SKU 184', '2026-07-01 03:19:20'),
(28, 7, 0, 1, 1, 'reserva', 19, NULL, 'Reserva checkout SKU 151', '2026-07-02 14:20:02'),
(31, 11, 0, 1, 1, 'reserva', 19, NULL, 'Reserva checkout SKU 153', '2026-07-02 14:20:02'),
(34, 22, 0, 1, 1, 'reserva', 22, NULL, 'Reserva checkout SKU 199', '2026-07-03 16:32:51'),
(37, 22, 5, 4, -1, 'venta', 22, 'MercadoPago', 'Venta confirmada SKU 199', '2026-07-03 16:33:43');

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
(4, 'gonzalo.mattia@hotmail.com', '$2y$12$Smu8EYHa7eVGl6KW1c6FZeb8exr3yT04x8RgcJJ4mYaagDUNzZy7G', 'gonzalo', 'mattia', '01160436765', NULL, NULL, NULL, NULL, '31632308', 1, 0, 1, NULL, '2026-06-23 14:04:29', '2026-07-03 13:32:15', '2026-07-03 13:32:15');

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

--
-- Volcado de datos para la tabla `tbl_usuarios_direcciones`
--

INSERT INTO `tbl_usuarios_direcciones` (`id_direccion`, `usuario_id`, `calle`, `numero`, `depto`, `ciudad`, `provincia`, `cp`, `predeterminada`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 4, 'Fred Aden', '406', NULL, 'Villa Sarmiento', 'Buenos Aires', '1706', 1, '2026-07-01 00:21:58', NULL);

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
-- Indices de la tabla `tbl_login_otp`
--
ALTER TABLE `tbl_login_otp`
  ADD PRIMARY KEY (`id_otp`),
  ADD KEY `idx_login_otp_usuario` (`usuario_id`),
  ADD KEY `idx_login_otp_expires` (`expires_at`);

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
-- AUTO_INCREMENT de la tabla `tbl_login_otp`
--
ALTER TABLE `tbl_login_otp`
  MODIFY `id_otp` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `tbl_metodos_pago`
--
ALTER TABLE `tbl_metodos_pago`
  MODIFY `id_metodo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tbl_mp_payments`
--
ALTER TABLE `tbl_mp_payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `tbl_mp_preferences`
--
ALTER TABLE `tbl_mp_preferences`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tbl_ordenes`
--
ALTER TABLE `tbl_ordenes`
  MODIFY `id_orden` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `tbl_ordenes_audit`
--
ALTER TABLE `tbl_ordenes_audit`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tbl_ordenes_historial`
--
ALTER TABLE `tbl_ordenes_historial`
  MODIFY `id_historial` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `tbl_password_tokens`
--
ALTER TABLE `tbl_password_tokens`
  MODIFY `id_token` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tbl_productos`
--
ALTER TABLE `tbl_productos`
  MODIFY `id_prod` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `tbl_prod_imagenes`
--
ALTER TABLE `tbl_prod_imagenes`
  MODIFY `id_imagen` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT de la tabla `tbl_shipping_config`
--
ALTER TABLE `tbl_shipping_config`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT de la tabla `tbl_skus`
--
ALTER TABLE `tbl_skus`
  MODIFY `id_sku` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=235;

--
-- AUTO_INCREMENT de la tabla `tbl_slider`
--
ALTER TABLE `tbl_slider`
  MODIFY `id_slide` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tbl_stock_log`
--
ALTER TABLE `tbl_stock_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

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
  MODIFY `id_direccion` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tbl_wishlist`
--
ALTER TABLE `tbl_wishlist`
  MODIFY `id_wishlist` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tbl_login_otp`
--
ALTER TABLE `tbl_login_otp`
  ADD CONSTRAINT `fk_login_otp_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `tbl_usuarios` (`id_usuario`) ON DELETE CASCADE;

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
