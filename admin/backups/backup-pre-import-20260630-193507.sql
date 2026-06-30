-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: yofi
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tbl_admin`
--

DROP TABLE IF EXISTS `tbl_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_admin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuadmin` varchar(50) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `publicado` int NOT NULL DEFAULT '1',
  `username` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuadmin` (`usuadmin`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_admin`
--

LOCK TABLES `tbl_admin` WRITE;
/*!40000 ALTER TABLE `tbl_admin` DISABLE KEYS */;
INSERT INTO `tbl_admin` VALUES (1,'admin','$2y$10$PbDjWFu6sk0137SznR411uQa5sfsb9gXn457jJdvC27g2TN6i3Lya',1,'admin','$2y$10$PbDjWFu6sk0137SznR411uQa5sfsb9gXn457jJdvC27g2TN6i3Lya','admin@yofi.local');
/*!40000 ALTER TABLE `tbl_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_banners`
--

DROP TABLE IF EXISTS `tbl_banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_banners` (
  `id_banner` int NOT NULL AUTO_INCREMENT,
  `eyebrow` varchar(150) DEFAULT NULL,
  `titulo` varchar(150) DEFAULT NULL,
  `subtitulo` varchar(150) DEFAULT NULL,
  `texto_boton` varchar(100) DEFAULT NULL,
  `imagen` varchar(250) NOT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `posicion` varchar(50) DEFAULT NULL,
  `orden` int DEFAULT '0',
  `activo` tinyint DEFAULT '1',
  PRIMARY KEY (`id_banner`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_banners`
--

LOCK TABLES `tbl_banners` WRITE;
/*!40000 ALTER TABLE `tbl_banners` DISABLE KEYS */;
INSERT INTO `tbl_banners` VALUES (1,'Solo por tiempo limitado','2X1','EN  PRODUCTOS SELECCIONADOS','COMPRAR','banner-3x2.jpg','index.php?p=catalogo&categoria=ofertas','home_secundario',1,1);
/*!40000 ALTER TABLE `tbl_banners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_categorias`
--

DROP TABLE IF EXISTS `tbl_categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_categorias` (
  `id_cate` int NOT NULL AUTO_INCREMENT,
  `id_cate_padre` int DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `descripcion` text,
  `imagen` varchar(250) DEFAULT NULL,
  `seo_title` varchar(250) DEFAULT NULL,
  `seo_description` text,
  `orden` int DEFAULT '0',
  `publicado` tinyint DEFAULT '1',
  `destacado_home` tinyint DEFAULT '0',
  PRIMARY KEY (`id_cate`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_categorias`
--

LOCK TABLES `tbl_categorias` WRITE;
/*!40000 ALTER TABLE `tbl_categorias` DISABLE KEYS */;
INSERT INTO `tbl_categorias` VALUES (1,NULL,'Abrigos','abrigos','Abrigos, camperas y abrigos','categoria-1782176049.png',NULL,NULL,1,1,1),(2,NULL,'Buzos y Cardigans','buzos-y-cardigans','Buzos, sweaters y cardigans','categoria-1782176920.png',NULL,NULL,2,1,1),(3,NULL,'Pantalones','pantalones','Pantalones y joggers','categoria-1782176603.png',NULL,NULL,3,1,1),(4,NULL,'Accesorios','accesorios','','categoria-1782176793.png',NULL,NULL,6,1,1),(7,NULL,'Remeras','remeras','Remeras y tops','categoria-1782176510.png',NULL,NULL,4,1,1),(10,NULL,'Regalos','regalos','Regalos y productos especiales','categoria-1782175349.png',NULL,NULL,9,1,1),(11,NULL,'Mini Ánima Invierno','mini-anima-invierno','Accesorios tejidos de invierno Mini Ánima',NULL,NULL,NULL,8,1,0);
/*!40000 ALTER TABLE `tbl_categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_colores`
--

DROP TABLE IF EXISTS `tbl_colores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_colores` (
  `id_color` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(60) NOT NULL,
  `hex_code` varchar(7) NOT NULL,
  `activo` tinyint DEFAULT '1',
  PRIMARY KEY (`id_color`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_colores`
--

LOCK TABLES `tbl_colores` WRITE;
/*!40000 ALTER TABLE `tbl_colores` DISABLE KEYS */;
INSERT INTO `tbl_colores` VALUES (1,'Blanco','#FFFFFF',1),(2,'Negro','#1A1A1A',1),(3,'Rosa','#F4A7B9',1),(4,'Celeste','#96AFC8',1),(5,'Rojo','#E1644B',1),(6,'Verde','#7D9B6E',1),(7,'Beige','#FAE1C8',1),(8,'Lila','#C4A8D4',1),(9,'Naranja','#FAAF7D',1),(10,'Amarillo','#F9E784',1),(11,'Bordó','#722F37',1),(12,'Gris','#9E9E9E',1),(13,'Azul','#4A6FA5',1),(14,'Natural','#E8DCC8',1),(15,'Verde antiguo','#6B7F5E',1),(16,'Chocolate','#5C4033',1),(17,'Gris topo','#8B8680',1);
/*!40000 ALTER TABLE `tbl_colores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_config_empresa`
--

DROP TABLE IF EXISTS `tbl_config_empresa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_config_empresa` (
  `clave` varchar(100) NOT NULL,
  `valor` text,
  PRIMARY KEY (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_config_empresa`
--

LOCK TABLES `tbl_config_empresa` WRITE;
/*!40000 ALTER TABLE `tbl_config_empresa` DISABLE KEYS */;
INSERT INTO `tbl_config_empresa` VALUES ('direccion','Once - Ciudad Autónoma de Buenos Aires'),('email_contacto','hola@yofi.com.ar'),('facebook',''),('horario_atencion',''),('instagram','https://www.instagram.com/batia.valls/'),('telefono','+54 9 11 2527-9502'),('whatsapp','+54 9 11 2527-9502');
/*!40000 ALTER TABLE `tbl_config_empresa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_home_edad_banners`
--

DROP TABLE IF EXISTS `tbl_home_edad_banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_home_edad_banners` (
  `id_edad_banner` int NOT NULL AUTO_INCREMENT,
  `slug` varchar(20) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `subtitulo` varchar(255) DEFAULT NULL,
  `imagen` varchar(250) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `orden` int DEFAULT '0',
  `activo` tinyint DEFAULT '1',
  PRIMARY KEY (`id_edad_banner`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_home_edad_banners`
--

LOCK TABLES `tbl_home_edad_banners` WRITE;
/*!40000 ALTER TABLE `tbl_home_edad_banners` DISABLE KEYS */;
INSERT INTO `tbl_home_edad_banners` VALUES (1,'mini','MINI',NULL,'edad-banner-1782175472.png','index.php?p=catalogo&edad=mini',1,1),(2,'1-a-4','1 A 4 AÑOS',NULL,'edad-banner-1782175547.png','index.php?p=catalogo&edad=1-a-4',2,1),(3,'4-a-12','4 A 12 AÑOS',NULL,'edad-banner-1782175702.png','index.php?p=catalogo&edad=4-a-12',3,1);
/*!40000 ALTER TABLE `tbl_home_edad_banners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_metodos_pago`
--

DROP TABLE IF EXISTS `tbl_metodos_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_metodos_pago` (
  `id_metodo` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `activo` tinyint DEFAULT '1',
  `orden` int DEFAULT '0',
  PRIMARY KEY (`id_metodo`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_metodos_pago`
--

LOCK TABLES `tbl_metodos_pago` WRITE;
/*!40000 ALTER TABLE `tbl_metodos_pago` DISABLE KEYS */;
INSERT INTO `tbl_metodos_pago` VALUES (1,'mercadopago','Mercado Pago','Tarjetas, transferencia y dinero en cuenta vía Mercado Pago',1,1);
/*!40000 ALTER TABLE `tbl_metodos_pago` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_mp_payments`
--

DROP TABLE IF EXISTS `tbl_mp_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_mp_payments`
--

LOCK TABLES `tbl_mp_payments` WRITE;
/*!40000 ALTER TABLE `tbl_mp_payments` DISABLE KEYS */;
INSERT INTO `tbl_mp_payments` VALUES (1,'165478695282','3494486768-1a49478c-8727-4670-8cdb-a789363a12b3','approved','accredited','credit_card','master',496006.00,'2026-06-23 15:05:27','2026-06-23 15:06:12');
/*!40000 ALTER TABLE `tbl_mp_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_mp_preferences`
--

DROP TABLE IF EXISTS `tbl_mp_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_mp_preferences`
--

LOCK TABLES `tbl_mp_preferences` WRITE;
/*!40000 ALTER TABLE `tbl_mp_preferences` DISABLE KEYS */;
INSERT INTO `tbl_mp_preferences` VALUES (1,'3494486768-a5e27ae8-b050-496e-a6ae-0e8362b9a193','[{\"id\":\"184\",\"title\":\"Delantal vintage sin bordar — Verde antiguo Único\",\"quantity\":1,\"unit_price\":40000,\"currency_id\":\"ARS\"},{\"id\":\"shipping\",\"title\":\"Envío\",\"quantity\":1,\"unit_price\":491641,\"currency_id\":\"ARS\"}]','{\"order_id\":14,\"numero_orden\":\"ORD-20260623-77204D\",\"customer\":{\"email\":\"gonzalo.mattia@hotmail.com\",\"firstName\":\"gonzalo\",\"lastName\":\"mattia\"},\"shipping\":{\"address\":\"Fred Aden 406\",\"city\":\"Villa Sarmiento\",\"province\":\"Buenos Aires\",\"zip\":\"1706\",\"notes\":\"\"},\"mp_currency\":\"ARS\"}','pending','2026-06-23 15:01:28',NULL),(2,'3494486768-1a49478c-8727-4670-8cdb-a789363a12b3','[{\"id\":\"153\",\"title\":\"Bandana tejida — Chocolate Único\",\"quantity\":1,\"unit_price\":5000,\"currency_id\":\"ARS\"},{\"id\":\"shipping\",\"title\":\"Envío\",\"quantity\":1,\"unit_price\":491006,\"currency_id\":\"ARS\"}]','{\"order_id\":15,\"numero_orden\":\"ORD-20260623-82508A\",\"customer\":{\"email\":\"gonzalo.mattia@hotmail.com\",\"firstName\":\"gonzalo\",\"lastName\":\"mattia\"},\"shipping\":{\"address\":\"Fred Aden 406\",\"city\":\"Villa Sarmiento\",\"province\":\"Buenos Aires\",\"zip\":\"1706\",\"notes\":\"\"},\"mp_currency\":\"ARS\"}','confirmado','2026-06-23 15:03:53','2026-06-23 15:06:12');
/*!40000 ALTER TABLE `tbl_mp_preferences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_ordenes`
--

DROP TABLE IF EXISTS `tbl_ordenes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
  `envio` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL,
  `items` json NOT NULL,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `reserva_expira_at` datetime DEFAULT NULL,
  `reserva_activa` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=stock reservado pendiente de pago',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_orden`),
  UNIQUE KEY `numero_orden` (`numero_orden`),
  KEY `idx_ordenes_reserva_expira` (`estado`,`reserva_activa`,`reserva_expira_at`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_ordenes`
--

LOCK TABLES `tbl_ordenes` WRITE;
/*!40000 ALTER TABLE `tbl_ordenes` DISABLE KEYS */;
INSERT INTO `tbl_ordenes` VALUES (1,'TEST-0001','confirmado','Cliente','Prueba','prueba1@test.com',NULL,'1122334455','Calle Falsa 123','CABA','Buenos Aires','1000','Orden de prueba para dashboard','mercadopago',NULL,NULL,NULL,NULL,NULL,NULL,14900.00,0.00,14900.00,'[{\"id_sku\": 2, \"imagen\": \"\", \"nombre\": \"Vestido Floral Romántico\", \"id_prod\": 1, \"cantidad\": 1, \"color_nombre\": \"Rosa\", \"talle_nombre\": \"4A\", \"precio_unitario\": 13900.00}]','2026-06-22 18:30:33',NULL,NULL,0,NULL),(2,'TEST-0002','enviado','Cliente','Dos','prueba2@test.com',NULL,'1122334456','Av. Siempreviva 742','La Plata','Buenos Aires','1900','Orden de prueba para dashboard','mercadopago',NULL,NULL,NULL,NULL,NULL,NULL,25800.00,0.00,25800.00,'[{\"id_sku\": 6, \"imagen\": \"\", \"nombre\": \"Mameluco Tejido Bebé\", \"id_prod\": 2, \"cantidad\": 2, \"color_nombre\": \"Rosa\", \"talle_nombre\": \"0-3M\", \"precio_unitario\": 12900.00}]','2026-06-22 18:30:33',NULL,NULL,0,NULL),(3,'TEST-0003','entregado','Cliente','Tres','prueba3@test.com',NULL,'1122334457','Mitre 50','Mar del Plata','Buenos Aires','7600','Orden de prueba para dashboard','mercadopago',NULL,NULL,NULL,NULL,NULL,NULL,79500.00,0.00,79500.00,'[{\"id_sku\": 9, \"imagen\": \"\", \"nombre\": \"Buzo Canguro Niño\", \"id_prod\": 3, \"cantidad\": 5, \"color_nombre\": \"Beige\", \"talle_nombre\": \"4A\", \"precio_unitario\": 15900.00}]','2026-06-22 18:30:33',NULL,NULL,0,NULL),(4,'TEST-GUEST-1782233599','pendiente','Invitado','Test','checkout-guest-1782233599@yofi.local',NULL,'1100000000','Calle 1','CABA','CABA','1406',NULL,'transferencia',NULL,NULL,NULL,NULL,NULL,NULL,1000.00,500.00,1500.00,'[]','2026-06-23 13:53:19',NULL,NULL,0,NULL),(5,'TEST-USER-1782233599','pendiente','Logueado','Test','checkout-user-1782233599@yofi.local',NULL,'1100000000','Calle 2','CABA','CABA','1406',NULL,'transferencia',NULL,NULL,NULL,NULL,NULL,NULL,2000.00,0.00,2000.00,'[]','2026-06-23 13:53:19',NULL,NULL,0,NULL),(13,'ORD-20260623-F1B477','pendiente','gonzalo','mattia','gonzalo.mattia@hotmail.com',4,'01160436765','Ruta Nacional 6 km 149.5','General Rodriguez','Buenos Aires','1748','','mercadopago','standard_delivery','OCA','6 a 10 días hábiles','{\"service\": \"Entrega a domicilio\", \"carrier_id\": 208, \"logistic_type\": \"xd_dropoff\"}',NULL,NULL,25590.00,491379.00,516969.00,'[{\"id_sku\": 155, \"imagen\": \"http://localhost/yofi/imgprod/prod-13-14-1782231749-0.jpg\", \"nombre\": \"Gorro aspen\", \"id_prod\": 13, \"cantidad\": 1, \"color_nombre\": \"Natural\", \"talle_nombre\": \"Único\", \"precio_unitario\": 15590}, {\"id_sku\": 153, \"imagen\": \"http://localhost/yofi/imgprod/prod-11-16-1782231562-0.jpg\", \"nombre\": \"Bandana tejida\", \"id_prod\": 11, \"cantidad\": 1, \"color_nombre\": \"Chocolate\", \"talle_nombre\": \"Único\", \"precio_unitario\": 5000}, {\"id_sku\": 154, \"imagen\": \"http://localhost/yofi/imgprod/prod-11-17-1782231642-0.jpg\", \"nombre\": \"Bandana tejida\", \"id_prod\": 11, \"cantidad\": 1, \"color_nombre\": \"Gris topo\", \"talle_nombre\": \"Único\", \"precio_unitario\": 5000}]','2026-06-23 14:39:27','2026-06-23 14:39:27','2026-06-23 15:09:27',1,NULL),(14,'ORD-20260623-77204D','pendiente','gonzalo','mattia','gonzalo.mattia@hotmail.com',4,'+541160436765','Fred Aden 406','Villa Sarmiento','Buenos Aires','1706','','mercadopago','standard_delivery','OCA','7 a 12 días hábiles','{\"service\": \"Entrega a domicilio\", \"carrier_id\": 208, \"logistic_type\": \"xd_dropoff\"}',NULL,NULL,40000.00,491641.00,531641.00,'[{\"id_sku\": 184, \"imagen\": \"/yofi/imgprod/prod-17-15-1782231439-0.jpg\", \"nombre\": \"Delantal vintage sin bordar\", \"id_prod\": 17, \"cantidad\": 1, \"color_nombre\": \"Verde antiguo\", \"talle_nombre\": \"Único\", \"precio_unitario\": 40000}]','2026-06-23 15:01:27','2026-06-23 15:16:49','2026-06-23 15:31:27',1,NULL),(15,'ORD-20260623-82508A','preparando_envio','gonzalo','mattia','gonzalo.mattia@hotmail.com',4,'+541160436765','Fred Aden 406','Villa Sarmiento','Buenos Aires','1706','','mercadopago','standard_delivery','OCA','7 a 12 días hábiles','{\"service\": \"Entrega a domicilio\", \"carrier_id\": 208, \"logistic_type\": \"xd_dropoff\"}','27817827','https://app.zipnova.com.ar/track/1CcLeoqibws7Ep93fZfiWV',5000.00,491006.00,496006.00,'[{\"id_sku\": 153, \"imagen\": \"/yofi/imgprod/prod-11-16-1782231562-0.jpg\", \"nombre\": \"Bandana tejida\", \"id_prod\": 11, \"cantidad\": 1, \"color_nombre\": \"Chocolate\", \"talle_nombre\": \"Único\", \"precio_unitario\": 5000}]','2026-06-23 15:03:52','2026-06-23 15:19:10','2026-06-23 15:33:52',0,NULL);
/*!40000 ALTER TABLE `tbl_ordenes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_ordenes_audit`
--

DROP TABLE IF EXISTS `tbl_ordenes_audit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_ordenes_audit`
--

LOCK TABLES `tbl_ordenes_audit` WRITE;
/*!40000 ALTER TABLE `tbl_ordenes_audit` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_ordenes_audit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_ordenes_historial`
--

DROP TABLE IF EXISTS `tbl_ordenes_historial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_ordenes_historial`
--

LOCK TABLES `tbl_ordenes_historial` WRITE;
/*!40000 ALTER TABLE `tbl_ordenes_historial` DISABLE KEYS */;
INSERT INTO `tbl_ordenes_historial` VALUES (1,15,'pendiente','confirmado','2026-06-23 15:05:27','MercadoPago','accredited',NULL,NULL),(2,15,'confirmado','preparando_envio','2026-06-23 15:19:10','Zipnova API','Envío creado en Zipnova','https://app.zipnova.com.ar/track/1CcLeoqibws7Ep93fZfiWV',NULL);
/*!40000 ALTER TABLE `tbl_ordenes_historial` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_password_tokens`
--

DROP TABLE IF EXISTS `tbl_password_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_password_tokens` (
  `id_token` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_token`),
  UNIQUE KEY `token` (`token`),
  KEY `idx_token` (`token`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_expires_at` (`expires_at`),
  KEY `idx_used` (`used`),
  KEY `idx_usuario_used` (`usuario_id`,`used`),
  CONSTRAINT `tbl_password_tokens_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `tbl_usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_password_tokens`
--

LOCK TABLES `tbl_password_tokens` WRITE;
/*!40000 ALTER TABLE `tbl_password_tokens` DISABLE KEYS */;
INSERT INTO `tbl_password_tokens` VALUES (1,4,'4e36ef3faff57715e4ae0468ee0e93e27b337b586348e685e6586f27dcfabdd4','2026-06-23 18:05:21',1,'2026-06-23 14:05:21');
/*!40000 ALTER TABLE `tbl_password_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_prod_imagenes`
--

DROP TABLE IF EXISTS `tbl_prod_imagenes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_prod_imagenes` (
  `id_imagen` int NOT NULL AUTO_INCREMENT,
  `id_prod` int NOT NULL,
  `id_color` int DEFAULT NULL,
  `path` varchar(250) NOT NULL,
  `orden` int DEFAULT '0',
  `es_principal` tinyint DEFAULT '0',
  PRIMARY KEY (`id_imagen`),
  KEY `id_prod` (`id_prod`),
  KEY `id_color` (`id_color`),
  CONSTRAINT `tbl_prod_imagenes_ibfk_1` FOREIGN KEY (`id_prod`) REFERENCES `tbl_productos` (`id_prod`),
  CONSTRAINT `tbl_prod_imagenes_ibfk_2` FOREIGN KEY (`id_color`) REFERENCES `tbl_colores` (`id_color`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_prod_imagenes`
--

LOCK TABLES `tbl_prod_imagenes` WRITE;
/*!40000 ALTER TABLE `tbl_prod_imagenes` DISABLE KEYS */;
INSERT INTO `tbl_prod_imagenes` VALUES (1,1,3,'prod-001-rosa-1.jpg',1,1),(2,1,4,'prod-001-celeste-1.jpg',1,1),(3,2,3,'prod-002-rosa-1.jpg',1,1),(4,3,7,'prod-003-beige-1.jpg',1,1),(5,4,1,'prod-004-blanco-1.jpg',1,1),(6,5,6,'prod-005-verde-1.jpg',1,1),(7,6,NULL,'prod-6-main.webp',0,1),(8,6,NULL,'prod-6-gen-1781642237.jpg',0,1),(9,6,4,'prod-6-4-1781642901-0.jpg',0,1),(59,19,11,'placeholder.jpg',1,1),(60,19,12,'placeholder.jpg',1,1),(61,19,13,'placeholder.jpg',1,1),(62,19,3,'placeholder.jpg',1,1),(63,20,16,'placeholder.jpg',1,1),(64,20,17,'placeholder.jpg',1,1),(65,21,14,'placeholder.jpg',1,1),(66,21,15,'placeholder.jpg',1,1),(67,21,3,'placeholder.jpg',1,1),(68,22,11,'placeholder.jpg',1,1),(69,23,7,'placeholder.jpg',1,1),(70,24,7,'placeholder.jpg',1,1);
/*!40000 ALTER TABLE `tbl_prod_imagenes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_productos`
--

DROP TABLE IF EXISTS `tbl_productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_prod`),
  UNIQUE KEY `slug` (`slug`),
  KEY `id_cate` (`id_cate`),
  CONSTRAINT `tbl_productos_ibfk_1` FOREIGN KEY (`id_cate`) REFERENCES `tbl_categorias` (`id_cate`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_productos`
--

LOCK TABLES `tbl_productos` WRITE;
/*!40000 ALTER TABLE `tbl_productos` DISABLE KEYS */;
INSERT INTO `tbl_productos` VALUES (1,1,'Vestido Floral Romántico','vestido-floral-rom-antico','YF-001',18500.00,13900.00,'Vestido de algodón importado con estampado floral.','','',0.30,40.00,35.00,2.00,0,0,0,'',1,'2026-06-16 13:46:57','2026-06-22 22:07:07'),(2,1,'Mameluco Tejido Bebé','mameluco-tejido-bebe','YF-002',12900.00,NULL,'Mameluco tejido importado, suave al tacto.',NULL,NULL,0.20,35.00,30.00,2.00,0,0,0,NULL,0,'2026-06-16 13:46:57','2026-06-22 21:27:10'),(3,2,'Buzo Canguro Niño','buzo-canguro-nino','YF-003',15900.00,NULL,'Buzo canguro de algodón importado.',NULL,NULL,0.35,45.00,40.00,3.00,0,0,0,NULL,0,'2026-06-16 13:46:57','2026-06-22 21:27:10'),(4,3,'Conjunto Primavera','conjunto-primavera','YF-004',22000.00,17500.00,'Conjunto de dos piezas, top y pantalón.',NULL,NULL,0.40,45.00,38.00,3.00,0,0,1,'3x2',0,'2026-06-16 13:46:57','2026-06-22 21:27:10'),(5,1,'Campera Acolchada Niña','campera-acolchada-nina','YF-005',28500.00,NULL,'Campera acolchada importada, abrigada y liviana.',NULL,NULL,0.50,48.00,42.00,5.00,0,0,0,NULL,1,'2026-06-16 13:46:57','2026-06-22 22:09:03'),(6,1,'Body Mini Argentina campeon','body-mini-argentina-campeon','I26M8806',26900.00,NULL,'El body mini Argentina, hecho en jersey con estampa.Tiene broche en escote y entre piernas. Para armar un look futbolero.\r\nComposición: 100% algodón\r\nTalles: del S al XXXL','','',0.50,20.00,20.00,5.00,0,1,1,'2x1',1,'2026-06-16 17:36:03','2026-06-22 22:17:17'),(19,11,'Infinito tejido','infinito-tejido','YF-MINI-INF',15000.00,NULL,'Fabricados en Acrílico Premium: Elegimos los mejores i...',NULL,NULL,0.00,0.00,0.00,0.00,1,0,0,NULL,0,'2026-06-29 17:36:05',NULL),(20,11,'Bandana tejida','bandana-tejida','YF-MINI-BAN',5000.00,NULL,'Nuestros nuevos y exclusivos cuellitos tipo bandana teji...',NULL,NULL,0.00,0.00,0.00,0.00,1,0,0,NULL,0,'2026-06-29 17:36:05',NULL),(21,11,'Gorro aspen','gorro-aspen','YF-MINI-ASP',15590.00,NULL,'Fabricados en Acrílico Premium: Elegimos los mejores i...',NULL,NULL,0.00,0.00,0.00,0.00,1,0,0,NULL,0,'2026-06-29 17:36:05',NULL),(22,11,'Gorro pompón bordó','gorro-pompon-bordo','YF-MINI-010',15590.00,NULL,'- Fabricados en Acrílico Premium: Elegimos los mejores...',NULL,NULL,0.00,0.00,0.00,0.00,1,0,0,NULL,0,'2026-06-29 17:36:05',NULL),(23,10,'Delantal vintage sin bordar','delantal-vintage-sin-bordar','YF-REG-001',40000.00,NULL,'Delantal estilo romantico Fabricado artesanalmente, edic...',NULL,NULL,0.00,0.00,0.00,0.00,1,0,0,NULL,0,'2026-06-29 17:36:05',NULL),(24,10,'Bordado a pedido','bordado-a-pedido','YF-REG-002',8000.00,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,1,0,0,NULL,0,'2026-06-29 17:36:05',NULL);
/*!40000 ALTER TABLE `tbl_productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_sessions`
--

DROP TABLE IF EXISTS `tbl_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_sessions` (
  `id` varchar(128) NOT NULL,
  `data` mediumblob NOT NULL,
  `last_access` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_last_access` (`last_access`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Sesiones PHP almacenadas en BD';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_sessions`
--

LOCK TABLES `tbl_sessions` WRITE;
/*!40000 ALTER TABLE `tbl_sessions` DISABLE KEYS */;
INSERT INTO `tbl_sessions` VALUES ('1o67k624jk1b184h6fkhhbt65n',_binary 'public_csrf_token|s:64:\"df181cadf25466ad09ab9f30e02a092c6092c8bd4840b1d5b3fbdd8c5b94151c\";',1782858863),('ksi1atkq1rsfvua99ofgjes4p5',_binary 'public_csrf_token|s:64:\"60bcb4e9f00798b953e9c6dfa88e53b91d759d9fba11f824236fc0c2d33b4e4d\";',1782237933),('lnmkotvebfkp3o4t4ikhpi8tn8',_binary 'public_csrf_token|s:64:\"3dd0cfbd68438d01f6ad3388c54c1b46b9fe97e4cc6a33be424aba377d61154f\";',1782237688);
/*!40000 ALTER TABLE `tbl_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_shipping_config`
--

DROP TABLE IF EXISTS `tbl_shipping_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_shipping_config` (
  `id` int NOT NULL AUTO_INCREMENT,
  `clave` varchar(100) NOT NULL,
  `valor` text NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clave` (`clave`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_shipping_config`
--

LOCK TABLES `tbl_shipping_config` WRITE;
/*!40000 ALTER TABLE `tbl_shipping_config` DISABLE KEYS */;
INSERT INTO `tbl_shipping_config` VALUES (1,'zipnova_enabled','1','Si el envío vía Zipnova está activo en el checkout',NULL,'2026-06-22 19:06:27'),(2,'zipnova_label','Envío a domicilio','Texto visible para el cliente','2026-06-22 19:18:08','2026-06-22 19:06:27'),(3,'zipnova_eta_default','3 a 6 días hábiles','ETA mostrado cuando la API no devuelve uno específico','2026-06-22 19:17:48','2026-06-22 19:06:27'),(4,'free_shipping_threshold','150000','Monto mínimo de compra para envío gratis (0 = desactivado)','2026-06-22 20:41:20','2026-06-22 19:06:27'),(5,'pickup_enabled','0','Si retiro en local está habilitado',NULL,'2026-06-22 19:06:27'),(6,'pickup_label','Retiro en local','Texto visible para el cliente',NULL,'2026-06-22 19:06:27'),(7,'pickup_address','','Dirección de retiro mostrada al cliente',NULL,'2026-06-22 19:06:27');
/*!40000 ALTER TABLE `tbl_shipping_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_skus`
--

DROP TABLE IF EXISTS `tbl_skus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_skus` (
  `id_sku` int NOT NULL AUTO_INCREMENT,
  `id_prod` int NOT NULL,
  `id_color` int NOT NULL,
  `id_talle` int NOT NULL,
  `codigo_sku` varchar(60) NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `stock_reservado` int NOT NULL DEFAULT '0',
  `precio_extra` decimal(10,2) DEFAULT '0.00',
  `activo` tinyint DEFAULT '1',
  PRIMARY KEY (`id_sku`),
  UNIQUE KEY `variante_unica` (`id_prod`,`id_color`,`id_talle`),
  KEY `id_color` (`id_color`),
  KEY `id_talle` (`id_talle`),
  CONSTRAINT `tbl_skus_ibfk_1` FOREIGN KEY (`id_prod`) REFERENCES `tbl_productos` (`id_prod`),
  CONSTRAINT `tbl_skus_ibfk_2` FOREIGN KEY (`id_color`) REFERENCES `tbl_colores` (`id_color`),
  CONSTRAINT `tbl_skus_ibfk_3` FOREIGN KEY (`id_talle`) REFERENCES `tbl_talles` (`id_talle`)
) ENGINE=InnoDB AUTO_INCREMENT=209 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_skus`
--

LOCK TABLES `tbl_skus` WRITE;
/*!40000 ALTER TABLE `tbl_skus` DISABLE KEYS */;
INSERT INTO `tbl_skus` VALUES (1,1,3,5,'YF-001-ROSA-2A',9,0,0.00,1),(2,1,3,7,'YF-001-ROSA-4A',10,0,0.00,1),(3,1,3,8,'YF-001-ROSA-6A',10,0,0.00,1),(4,1,4,5,'YF-001-CEL-2A',10,0,0.00,1),(5,1,4,7,'YF-001-CEL-4A',10,0,0.00,1),(6,2,3,1,'YF-002-ROSA-03M',10,0,0.00,1),(7,2,3,2,'YF-002-ROSA-36M',10,0,0.00,1),(8,2,3,3,'YF-002-ROSA-612M',10,0,0.00,1),(9,3,7,7,'YF-003-BEI-4A',10,0,0.00,1),(10,3,7,8,'YF-003-BEI-6A',10,0,0.00,1),(11,3,7,9,'YF-003-BEI-8A',10,0,0.00,1),(12,4,1,5,'YF-004-BLA-2A',10,0,0.00,1),(13,4,1,7,'YF-004-BLA-4A',10,0,0.00,1),(14,5,6,7,'YF-005-VER-4A',10,0,0.00,1),(15,5,6,8,'YF-005-VER-6A',10,0,0.00,1),(16,5,6,9,'YF-005-VER-8A',10,0,0.00,1),(17,6,1,1,'YOFI-6-1-1',10,0,0.00,1),(18,6,1,2,'YOFI-6-1-2',10,0,0.00,1),(19,6,1,3,'YOFI-6-1-3',10,0,0.00,1),(20,6,1,4,'YOFI-6-1-4',10,0,0.00,1),(21,6,1,5,'YOFI-6-1-5',10,0,0.00,1),(22,6,1,6,'YOFI-6-1-6',10,0,0.00,1),(23,6,1,7,'YOFI-6-1-7',10,0,0.00,1),(24,6,1,8,'YOFI-6-1-8',10,0,0.00,1),(25,6,1,9,'YOFI-6-1-9',10,0,0.00,1),(26,6,1,10,'YOFI-6-1-10',10,0,0.00,1),(27,6,1,11,'YOFI-6-1-11',10,0,0.00,1),(28,6,2,1,'YOFI-6-2-1',10,0,0.00,1),(29,6,2,2,'YOFI-6-2-2',10,0,0.00,1),(30,6,2,3,'YOFI-6-2-3',10,0,0.00,1),(31,6,2,4,'YOFI-6-2-4',10,0,0.00,1),(32,6,2,5,'YOFI-6-2-5',10,0,0.00,1),(33,6,2,6,'YOFI-6-2-6',10,0,0.00,1),(34,6,2,7,'YOFI-6-2-7',10,0,0.00,1),(35,6,2,8,'YOFI-6-2-8',10,0,0.00,1),(36,6,2,9,'YOFI-6-2-9',10,0,0.00,1),(37,6,2,10,'YOFI-6-2-10',10,0,0.00,1),(38,6,2,11,'YOFI-6-2-11',10,0,0.00,1),(39,6,3,1,'YOFI-6-3-1',10,0,0.00,1),(40,6,3,2,'YOFI-6-3-2',10,0,0.00,1),(41,6,3,3,'YOFI-6-3-3',10,0,0.00,1),(42,6,3,4,'YOFI-6-3-4',10,0,0.00,1),(43,6,3,5,'YOFI-6-3-5',10,0,0.00,1),(44,6,3,6,'YOFI-6-3-6',10,0,0.00,1),(45,6,3,7,'YOFI-6-3-7',10,0,0.00,1),(46,6,3,8,'YOFI-6-3-8',10,0,0.00,1),(47,6,3,9,'YOFI-6-3-9',10,0,0.00,1),(48,6,3,10,'YOFI-6-3-10',10,0,0.00,1),(49,6,3,11,'YOFI-6-3-11',10,0,0.00,1),(50,6,4,1,'YOFI-6-4-1',10,0,0.00,1),(51,6,4,2,'YOFI-6-4-2',10,0,0.00,1),(52,6,4,3,'YOFI-6-4-3',10,0,0.00,1),(53,6,4,4,'YOFI-6-4-4',10,0,0.00,1),(54,6,4,5,'YOFI-6-4-5',10,0,0.00,1),(55,6,4,6,'YOFI-6-4-6',10,0,0.00,1),(56,6,4,7,'YOFI-6-4-7',10,0,0.00,1),(57,6,4,8,'YOFI-6-4-8',10,0,0.00,1),(58,6,4,9,'YOFI-6-4-9',10,0,0.00,1),(59,6,4,10,'YOFI-6-4-10',10,0,0.00,1),(60,6,4,11,'YOFI-6-4-11',10,0,0.00,1),(61,6,5,1,'YOFI-6-5-1',10,0,0.00,1),(62,6,5,2,'YOFI-6-5-2',10,0,0.00,1),(63,6,5,3,'YOFI-6-5-3',10,0,0.00,1),(64,6,5,4,'YOFI-6-5-4',10,0,0.00,1),(65,6,5,5,'YOFI-6-5-5',10,0,0.00,1),(66,6,5,6,'YOFI-6-5-6',10,0,0.00,1),(67,6,5,7,'YOFI-6-5-7',10,0,0.00,1),(68,6,5,8,'YOFI-6-5-8',10,0,0.00,1),(69,6,5,9,'YOFI-6-5-9',10,0,0.00,1),(70,6,5,10,'YOFI-6-5-10',10,0,0.00,1),(71,6,5,11,'YOFI-6-5-11',10,0,0.00,1),(72,6,6,1,'YOFI-6-6-1',10,0,0.00,1),(73,6,6,2,'YOFI-6-6-2',10,0,0.00,1),(74,6,6,3,'YOFI-6-6-3',10,0,0.00,1),(75,6,6,4,'YOFI-6-6-4',10,0,0.00,1),(76,6,6,5,'YOFI-6-6-5',10,0,0.00,1),(77,6,6,6,'YOFI-6-6-6',10,0,0.00,1),(78,6,6,7,'YOFI-6-6-7',10,0,0.00,1),(79,6,6,8,'YOFI-6-6-8',10,0,0.00,1),(80,6,6,9,'YOFI-6-6-9',10,0,0.00,1),(81,6,6,10,'YOFI-6-6-10',10,0,0.00,1),(82,6,6,11,'YOFI-6-6-11',10,0,0.00,1),(83,6,7,1,'YOFI-6-7-1',10,0,0.00,1),(84,6,7,2,'YOFI-6-7-2',10,0,0.00,1),(85,6,7,3,'YOFI-6-7-3',10,0,0.00,1),(86,6,7,4,'YOFI-6-7-4',10,0,0.00,1),(87,6,7,5,'YOFI-6-7-5',10,0,0.00,1),(88,6,7,6,'YOFI-6-7-6',10,0,0.00,1),(89,6,7,7,'YOFI-6-7-7',10,0,0.00,1),(90,6,7,8,'YOFI-6-7-8',10,0,0.00,1),(91,6,7,9,'YOFI-6-7-9',10,0,0.00,1),(92,6,7,10,'YOFI-6-7-10',10,0,0.00,1),(93,6,7,11,'YOFI-6-7-11',10,0,0.00,1),(94,6,8,1,'YOFI-6-8-1',10,0,0.00,1),(95,6,8,2,'YOFI-6-8-2',10,0,0.00,1),(96,6,8,3,'YOFI-6-8-3',10,0,0.00,1),(97,6,8,4,'YOFI-6-8-4',10,0,0.00,1),(98,6,8,5,'YOFI-6-8-5',10,0,0.00,1),(99,6,8,6,'YOFI-6-8-6',10,0,0.00,1),(100,6,8,7,'YOFI-6-8-7',10,0,0.00,1),(101,6,8,8,'YOFI-6-8-8',10,0,0.00,1),(102,6,8,9,'YOFI-6-8-9',10,0,0.00,1),(103,6,8,10,'YOFI-6-8-10',10,0,0.00,1),(104,6,8,11,'YOFI-6-8-11',10,0,0.00,1),(105,6,9,1,'YOFI-6-9-1',10,0,0.00,1),(106,6,9,2,'YOFI-6-9-2',10,0,0.00,1),(107,6,9,3,'YOFI-6-9-3',10,0,0.00,1),(108,6,9,4,'YOFI-6-9-4',10,0,0.00,1),(109,6,9,5,'YOFI-6-9-5',10,0,0.00,1),(110,6,9,6,'YOFI-6-9-6',10,0,0.00,1),(111,6,9,7,'YOFI-6-9-7',10,0,0.00,1),(112,6,9,8,'YOFI-6-9-8',10,0,0.00,1),(113,6,9,9,'YOFI-6-9-9',10,0,0.00,1),(114,6,9,10,'YOFI-6-9-10',10,0,0.00,1),(115,6,9,11,'YOFI-6-9-11',10,0,0.00,1),(116,6,10,1,'YOFI-6-10-1',10,0,0.00,1),(117,6,10,2,'YOFI-6-10-2',10,0,0.00,1),(118,6,10,3,'YOFI-6-10-3',10,0,0.00,1),(119,6,10,4,'YOFI-6-10-4',10,0,0.00,1),(120,6,10,5,'YOFI-6-10-5',10,0,0.00,1),(121,6,10,6,'YOFI-6-10-6',10,0,0.00,1),(122,6,10,7,'YOFI-6-10-7',10,0,0.00,1),(123,6,10,8,'YOFI-6-10-8',10,0,0.00,1),(124,6,10,9,'YOFI-6-10-9',10,0,0.00,1),(125,6,10,10,'YOFI-6-10-10',10,0,0.00,1),(126,6,10,11,'YOFI-6-10-11',10,0,0.00,1),(197,19,11,12,'YF-MINI-INF-BOR',10,0,0.00,1),(198,19,12,12,'YF-MINI-INF-GRI',10,0,0.00,1),(199,19,13,12,'YF-MINI-INF-AZU',10,0,0.00,1),(200,19,3,12,'YF-MINI-INF-ROS',10,0,0.00,1),(201,20,16,12,'YF-MINI-BAN-CHO',10,0,0.00,1),(202,20,17,12,'YF-MINI-BAN-GTO',10,0,0.00,1),(203,21,14,12,'YF-MINI-ASP-NAT',10,0,0.00,1),(204,21,15,12,'YF-MINI-ASP-VER',10,0,0.00,1),(205,21,3,12,'YF-MINI-ASP-ROS',10,0,0.00,1),(206,22,11,12,'YF-MINI-010-UNI',10,0,0.00,1),(207,23,7,12,'YF-REG-001-UNI',10,0,0.00,1),(208,24,7,12,'YF-REG-002-UNI',10,0,0.00,1);
/*!40000 ALTER TABLE `tbl_skus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_slider`
--

DROP TABLE IF EXISTS `tbl_slider`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_slider` (
  `id_slide` int NOT NULL AUTO_INCREMENT,
  `imagen` varchar(250) NOT NULL,
  `imagen_mobile` varchar(250) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `orden` int DEFAULT '0',
  `activo` tinyint DEFAULT '1',
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_slide`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_slider`
--

LOCK TABLES `tbl_slider` WRITE;
/*!40000 ALTER TABLE `tbl_slider` DISABLE KEYS */;
INSERT INTO `tbl_slider` VALUES (1,'slide-1782174277.png',NULL,NULL,1,1,'2026-06-22 19:40:10'),(3,'slide-1782174332.png',NULL,NULL,1,1,'2026-06-22 21:25:32');
/*!40000 ALTER TABLE `tbl_slider` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_stock_log`
--

DROP TABLE IF EXISTS `tbl_stock_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_stock_log`
--

LOCK TABLES `tbl_stock_log` WRITE;
/*!40000 ALTER TABLE `tbl_stock_log` DISABLE KEYS */;
INSERT INTO `tbl_stock_log` VALUES (5,11,0,1,1,'reserva',13,NULL,'Reserva checkout SKU 153','2026-06-23 17:39:27'),(6,11,0,1,1,'reserva',13,NULL,'Reserva checkout SKU 154','2026-06-23 17:39:27'),(7,13,0,1,1,'reserva',13,NULL,'Reserva checkout SKU 155','2026-06-23 17:39:27'),(8,17,0,1,1,'reserva',14,NULL,'Reserva checkout SKU 184','2026-06-23 18:01:27'),(9,11,1,2,1,'reserva',15,NULL,'Reserva checkout SKU 153','2026-06-23 18:03:52'),(10,11,10,9,-1,'venta',15,'MercadoPago','Venta confirmada SKU 153','2026-06-23 18:05:27');
/*!40000 ALTER TABLE `tbl_stock_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_talles`
--

DROP TABLE IF EXISTS `tbl_talles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_talles` (
  `id_talle` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(20) NOT NULL,
  `orden` int DEFAULT '0',
  `activo` tinyint DEFAULT '1',
  PRIMARY KEY (`id_talle`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_talles`
--

LOCK TABLES `tbl_talles` WRITE;
/*!40000 ALTER TABLE `tbl_talles` DISABLE KEYS */;
INSERT INTO `tbl_talles` VALUES (1,'0-3M',1,1),(2,'3-6M',2,1),(3,'6-12M',3,1),(4,'1A',4,1),(5,'2A',5,1),(6,'3A',6,1),(7,'4A',7,1),(8,'6A',8,1),(9,'8A',9,1),(10,'10A',10,1),(11,'12A',11,1),(12,'Único',99,1);
/*!40000 ALTER TABLE `tbl_talles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_usuarios`
--

DROP TABLE IF EXISTS `tbl_usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `is_guest` tinyint(1) NOT NULL DEFAULT '1',
  `email_verificado` tinyint(1) NOT NULL DEFAULT '0',
  `token_verificacion` varchar(100) DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_ultimo_acceso` datetime DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_activo` (`activo`),
  KEY `idx_fecha_registro` (`fecha_registro`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_usuarios`
--

LOCK TABLES `tbl_usuarios` WRITE;
/*!40000 ALTER TABLE `tbl_usuarios` DISABLE KEYS */;
INSERT INTO `tbl_usuarios` VALUES (2,'checkout-user-1782233599@yofi.local','$2y$10$D/9UcRxSDpFwZITxsQxMcOVi6tonXkIaQ7QHclH/UkRzH9NkXEGD2','Logueado','Test',NULL,NULL,NULL,NULL,NULL,NULL,1,0,1,NULL,'2026-06-23 13:53:19',NULL,NULL),(4,'gonzalo.mattia@hotmail.com','$2y$10$CtzGBUhU9NsC8jGqr39G.uDcro.mlupOniVrTR.16R7SSgyQDh3g2','gonzalo','mattia','01160436765',NULL,NULL,NULL,NULL,'31632308',1,0,1,NULL,'2026-06-23 14:04:29','2026-06-23 14:06:16','2026-06-23 14:06:16');
/*!40000 ALTER TABLE `tbl_usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_usuarios_direcciones`
--

DROP TABLE IF EXISTS `tbl_usuarios_direcciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_usuarios_direcciones` (
  `id_direccion` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `calle` varchar(150) NOT NULL,
  `numero` varchar(20) NOT NULL DEFAULT '',
  `depto` varchar(30) DEFAULT NULL,
  `ciudad` varchar(100) NOT NULL,
  `provincia` varchar(100) NOT NULL,
  `cp` varchar(20) NOT NULL,
  `predeterminada` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_direccion`),
  KEY `idx_usuario_direcciones` (`usuario_id`),
  CONSTRAINT `fk_usuario_direcciones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `tbl_usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_usuarios_direcciones`
--

LOCK TABLES `tbl_usuarios_direcciones` WRITE;
/*!40000 ALTER TABLE `tbl_usuarios_direcciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_usuarios_direcciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_wishlist`
--

DROP TABLE IF EXISTS `tbl_wishlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_wishlist` (
  `id_wishlist` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `producto_id` int NOT NULL,
  `fecha_agregado` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_wishlist`),
  UNIQUE KEY `uq_wishlist_usuario_producto` (`usuario_id`,`producto_id`),
  KEY `idx_wishlist_usuario` (`usuario_id`),
  KEY `idx_wishlist_producto` (`producto_id`),
  CONSTRAINT `fk_wishlist_producto` FOREIGN KEY (`producto_id`) REFERENCES `tbl_productos` (`id_prod`) ON DELETE CASCADE,
  CONSTRAINT `fk_wishlist_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `tbl_usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_wishlist`
--

LOCK TABLES `tbl_wishlist` WRITE;
/*!40000 ALTER TABLE `tbl_wishlist` DISABLE KEYS */;
INSERT INTO `tbl_wishlist` VALUES (17,4,7,'2026-06-23 14:21:44');
/*!40000 ALTER TABLE `tbl_wishlist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'yofi'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-30 19:35:07
