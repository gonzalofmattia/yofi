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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_admin`
--

LOCK TABLES `tbl_admin` WRITE;
/*!40000 ALTER TABLE `tbl_admin` DISABLE KEYS */;
INSERT INTO `tbl_admin` VALUES (1,'admin','$2y$10$vBAYTZx3aBOCkgLEfolXluBl.8AuDxjZuVJFHLoMlWgWqVvHDZaJm',1,'admin','$2y$10$vBAYTZx3aBOCkgLEfolXluBl.8AuDxjZuVJFHLoMlWgWqVvHDZaJm','admin@yofi.com.ar');
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
  `banner_img` varchar(250) DEFAULT NULL,
  `seo_title` varchar(250) DEFAULT NULL,
  `seo_description` text,
  `orden` int DEFAULT '0',
  `publicado` tinyint DEFAULT '1',
  `destacado_home` tinyint DEFAULT '0',
  PRIMARY KEY (`id_cate`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_categorias`
--

LOCK TABLES `tbl_categorias` WRITE;
/*!40000 ALTER TABLE `tbl_categorias` DISABLE KEYS */;
INSERT INTO `tbl_categorias` VALUES (1,NULL,'Abrigos','abrigos','Abrigos, camperas y abrigos','categoria-1782176049.png',NULL,NULL,NULL,1,1,1),(2,NULL,'Buzos y Cardigans','buzos-y-cardigans','Buzos, sweaters y cardigans','categoria-1782176920.png',NULL,NULL,NULL,2,1,1),(3,NULL,'Pantalones','pantalones','Pantalones y joggers','categoria-1782176603.png',NULL,NULL,NULL,3,1,1),(4,NULL,'Accesorios','accesorios','','categoria-1782176793.png',NULL,NULL,NULL,6,1,1),(7,NULL,'Remeras','remeras','Remeras y tops','categoria-1782176510.png',NULL,NULL,NULL,4,1,1),(9,NULL,'Mini Ánima Invierno','mini-nima-invierno','Accesorios tejidos de invierno Mini Ánima','categoria-1782175186.png','categoria-banner-1782863014.png',NULL,NULL,8,1,1),(10,NULL,'Regalos','regalos','Regalos y productos especiales','categoria-1782175349.png',NULL,NULL,NULL,9,1,1);
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
-- Table structure for table `tbl_login_otp`
--

DROP TABLE IF EXISTS `tbl_login_otp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_login_otp` (
  `id_otp` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `code_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `attempts` int NOT NULL DEFAULT '0',
  `consumed_at` datetime DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_otp`),
  KEY `idx_login_otp_usuario` (`usuario_id`),
  KEY `idx_login_otp_expires` (`expires_at`),
  CONSTRAINT `fk_login_otp_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `tbl_usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=170 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_login_otp`
--

LOCK TABLES `tbl_login_otp` WRITE;
/*!40000 ALTER TABLE `tbl_login_otp` DISABLE KEYS */;
INSERT INTO `tbl_login_otp` VALUES (35,4,'$2y$10$wso1mqM9.pgUKRmx3BcyLehgLSlGGB3dJB3/gEO0Sbs4rQCTFGwMi','2026-07-01 02:35:27',0,'2026-06-30 23:25:48','2026-07-01 02:25:27'),(131,4,'$2y$10$0/BS5XRb8ko80hkiHQB1n.iE7YjgFv5U94LFjTm3XlER8njViEh5.','2026-07-02 23:48:04',0,'2026-07-02 20:38:19','2026-07-02 23:38:04'),(137,4,'$2y$10$xgaU7YfBSyA62295lnsijOjzie3PLWOF2PA6.WhazAQeirzyzwSt2','2026-07-03 00:04:14',0,'2026-07-02 20:56:31','2026-07-02 23:54:14'),(138,4,'$2y$10$5wInACC4ZiUK5fmgA0hwEOJLsBl6AmNc0H3M7o68DTdsE7ES0vdxS','2026-07-03 00:20:18',0,'2026-07-02 21:10:29','2026-07-03 00:10:18'),(139,4,'$2y$10$f99GIKUc9MMA.npD1wxkPeE3/15AjOyPs7ZMiQq3MY6yFU/zA4v/m','2026-07-03 01:07:00',0,'2026-07-02 21:57:17','2026-07-03 00:57:00');
/*!40000 ALTER TABLE `tbl_login_otp` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_metodos_pago`
--

LOCK TABLES `tbl_metodos_pago` WRITE;
/*!40000 ALTER TABLE `tbl_metodos_pago` DISABLE KEYS */;
INSERT INTO `tbl_metodos_pago` VALUES (1,'mercadopago','Mercado Pago','Tarjetas, transferencia y dinero en cuenta vía Mercado Pago',1,1),(2,'transferencia','Transferencia bancaria','',1,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_mp_preferences`
--

LOCK TABLES `tbl_mp_preferences` WRITE;
/*!40000 ALTER TABLE `tbl_mp_preferences` DISABLE KEYS */;
INSERT INTO `tbl_mp_preferences` VALUES (1,'3494486768-a5e27ae8-b050-496e-a6ae-0e8362b9a193','[{\"id\":\"184\",\"title\":\"Delantal vintage sin bordar — Verde antiguo Único\",\"quantity\":1,\"unit_price\":40000,\"currency_id\":\"ARS\"},{\"id\":\"shipping\",\"title\":\"Envío\",\"quantity\":1,\"unit_price\":491641,\"currency_id\":\"ARS\"}]','{\"order_id\":14,\"numero_orden\":\"ORD-20260623-77204D\",\"customer\":{\"email\":\"gonzalo.mattia@hotmail.com\",\"firstName\":\"gonzalo\",\"lastName\":\"mattia\"},\"shipping\":{\"address\":\"Fred Aden 406\",\"city\":\"Villa Sarmiento\",\"province\":\"Buenos Aires\",\"zip\":\"1706\",\"notes\":\"\"},\"mp_currency\":\"ARS\"}','pending','2026-06-23 15:01:28',NULL),(2,'3494486768-1a49478c-8727-4670-8cdb-a789363a12b3','[{\"id\":\"153\",\"title\":\"Bandana tejida — Chocolate Único\",\"quantity\":1,\"unit_price\":5000,\"currency_id\":\"ARS\"},{\"id\":\"shipping\",\"title\":\"Envío\",\"quantity\":1,\"unit_price\":491006,\"currency_id\":\"ARS\"}]','{\"order_id\":15,\"numero_orden\":\"ORD-20260623-82508A\",\"customer\":{\"email\":\"gonzalo.mattia@hotmail.com\",\"firstName\":\"gonzalo\",\"lastName\":\"mattia\"},\"shipping\":{\"address\":\"Fred Aden 406\",\"city\":\"Villa Sarmiento\",\"province\":\"Buenos Aires\",\"zip\":\"1706\",\"notes\":\"\"},\"mp_currency\":\"ARS\"}','confirmado','2026-06-23 15:03:53','2026-06-23 15:06:12'),(4,'3494486768-06268c8c-3e5c-45f6-98a1-9e691652e066','[{\"id\":\"184\",\"title\":\"Delantal vintage sin bordar — Verde antiguo Único\",\"quantity\":1,\"unit_price\":40000,\"currency_id\":\"ARS\"},{\"id\":\"151\",\"title\":\"Infinito tejido — Azul Único\",\"quantity\":1,\"unit_price\":15000,\"currency_id\":\"ARS\"},{\"id\":\"shipping\",\"title\":\"Envío\",\"quantity\":1,\"unit_price\":494490,\"currency_id\":\"ARS\"}]','{\"order_id\":21,\"numero_orden\":\"ORD-20260702-82D47F\",\"customer\":{\"email\":\"gonzalo.mattia@hotmail.com\",\"firstName\":\"gonzalo\",\"lastName\":\"mattia\"},\"shipping\":{\"address\":\"Fred Aden 406\",\"city\":\"Villa Sarmiento\",\"province\":\"Buenos Aires\",\"zip\":\"1706\",\"notes\":\"\"},\"mp_currency\":\"ARS\"}','pending','2026-07-02 20:38:47',NULL),(5,'3494486768-7c13d85e-0f46-4d78-b44a-bed03fe85e64','[{\"id\":\"184\",\"title\":\"Delantal vintage sin bordar — Verde antiguo Único\",\"quantity\":1,\"unit_price\":40000,\"currency_id\":\"ARS\"},{\"id\":\"shipping\",\"title\":\"Envío\",\"quantity\":1,\"unit_price\":494218,\"currency_id\":\"ARS\"}]','{\"order_id\":22,\"numero_orden\":\"ORD-20260702-9D9DF4\",\"customer\":{\"email\":\"gonzalo.mattia@hotmail.com\",\"firstName\":\"gonzalo\",\"lastName\":\"mattia\"},\"shipping\":{\"address\":\"Fred Aden 406\",\"city\":\"Villa Sarmiento\",\"province\":\"Buenos Aires\",\"zip\":\"1706\",\"notes\":\"\"},\"mp_currency\":\"ARS\"}','pending','2026-07-02 20:56:56',NULL),(6,'3494486768-05f88dd7-e203-4ea9-88fe-63753d832043','[{\"id\":\"158\",\"title\":\"Gorro pompón bordó — Bordó Único\",\"quantity\":1,\"unit_price\":15590,\"currency_id\":\"ARS\"},{\"id\":\"shipping\",\"title\":\"Envío\",\"quantity\":1,\"unit_price\":493775,\"currency_id\":\"ARS\"}]','{\"order_id\":23,\"numero_orden\":\"ORD-20260703-D7C1D8\",\"customer\":{\"email\":\"gonzalo.mattia@hotmail.com\",\"firstName\":\"gonzalo\",\"lastName\":\"mattia\"},\"shipping\":{\"address\":\"Fred Aden 406\",\"city\":\"Villa Sarmiento\",\"province\":\"Buenos Aires\",\"zip\":\"1706\",\"notes\":\"\"},\"mp_currency\":\"ARS\"}','pending','2026-07-02 21:10:43',NULL),(7,'3494486768-0afb023c-a212-47c8-8a27-3334f75a7d85','[{\"id\":\"184\",\"title\":\"Delantal vintage sin bordar — Verde antiguo Único\",\"quantity\":1,\"unit_price\":40000,\"currency_id\":\"ARS\"},{\"id\":\"shipping\",\"title\":\"Envío\",\"quantity\":1,\"unit_price\":494218,\"currency_id\":\"ARS\"}]','{\"order_id\":24,\"numero_orden\":\"ORD-20260703-45DA20\",\"customer\":{\"email\":\"gonzalo.mattia@hotmail.com\",\"firstName\":\"gonzalo\",\"lastName\":\"mattia\"},\"shipping\":{\"address\":\"Fred Aden 406\",\"city\":\"Villa Sarmiento\",\"province\":\"Buenos Aires\",\"zip\":\"1706\",\"notes\":\"\"},\"mp_currency\":\"ARS\"}','pending','2026-07-02 21:41:22',NULL),(8,'3494486768-abd31c47-f169-4782-b059-768344e34914','[{\"id\":\"184\",\"title\":\"Delantal vintage sin bordar — Verde antiguo Único\",\"quantity\":1,\"unit_price\":40000,\"currency_id\":\"ARS\"},{\"id\":\"shipping\",\"title\":\"Envío\",\"quantity\":1,\"unit_price\":494218,\"currency_id\":\"ARS\"}]','{\"order_id\":25,\"numero_orden\":\"ORD-20260703-5188C8\",\"customer\":{\"email\":\"test@yofi.com.ar\",\"firstName\":\"Test\",\"lastName\":\"Comprador\"},\"shipping\":{\"address\":\"Av Corrientes 1234\",\"city\":\"CABA\",\"province\":\"Ciudad Autónoma de Buenos Aires\",\"zip\":\"1414\",\"notes\":\"\"},\"mp_currency\":\"ARS\"}','pending','2026-07-02 21:48:58',NULL),(9,'3494486768-0e28667e-9365-41df-a415-1296c97d057a','[{\"id\":\"158\",\"title\":\"Gorro pompón bordó — Bordó Único\",\"quantity\":1,\"unit_price\":15590,\"currency_id\":\"ARS\"},{\"id\":\"shipping\",\"title\":\"Envío\",\"quantity\":1,\"unit_price\":493775,\"currency_id\":\"ARS\"}]','{\"order_id\":26,\"numero_orden\":\"ORD-20260703-929633\",\"customer\":{\"email\":\"gonzalo.mattia@hotmail.com\",\"firstName\":\"gonzalo\",\"lastName\":\"mattia\"},\"shipping\":{\"address\":\"Fred Aden 406\",\"city\":\"Villa Sarmiento\",\"province\":\"Buenos Aires\",\"zip\":\"1706\",\"notes\":\"\"},\"mp_currency\":\"ARS\"}','pending','2026-07-02 21:57:38',NULL),(10,'3516356138-27329163-365c-41e3-b99b-0517d8b9c748','[{\"id\":\"184\",\"title\":\"Delantal vintage sin bordar — Verde antiguo Único\",\"quantity\":1,\"unit_price\":40000,\"currency_id\":\"ARS\"},{\"id\":\"shipping\",\"title\":\"Envío\",\"quantity\":1,\"unit_price\":494218,\"currency_id\":\"ARS\"}]','{\"order_id\":27,\"numero_orden\":\"ORD-20260703-2DF146\",\"customer\":{\"email\":\"test.comprador@example.com\",\"firstName\":\"Test\",\"lastName\":\"Comprador\"},\"shipping\":{\"address\":\"Av. Siempre Viva 123\",\"city\":\"Ciudad Autónoma de Buenos Aires\",\"province\":\"Ciudad Autónoma de Buenos Aires\",\"zip\":\"1425\",\"notes\":\"\"},\"mp_currency\":\"ARS\"}','pending','2026-07-03 10:59:45',NULL),(11,'3516356138-144b80d2-45a8-4470-b823-a646a2b158d9','[{\"id\":\"158\",\"title\":\"Gorro pompón bordó — Bordó Único\",\"quantity\":1,\"unit_price\":15590,\"currency_id\":\"ARS\"},{\"id\":\"shipping\",\"title\":\"Envío\",\"quantity\":1,\"unit_price\":493775,\"currency_id\":\"ARS\"}]','{\"order_id\":28,\"numero_orden\":\"ORD-20260703-B6B9E3\",\"customer\":{\"email\":\"gonzalo@yagondesign.com.ar\",\"firstName\":\"gonzalo\",\"lastName\":\"mattia\"},\"shipping\":{\"address\":\"yapeyu 365\",\"city\":\"villa sarmiento\",\"province\":\"Buenos Aires\",\"zip\":\"1706\",\"notes\":\"\"},\"mp_currency\":\"ARS\"}','pending','2026-07-03 11:06:48',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_ordenes`
--

LOCK TABLES `tbl_ordenes` WRITE;
/*!40000 ALTER TABLE `tbl_ordenes` DISABLE KEYS */;
INSERT INTO `tbl_ordenes` VALUES (1,'TEST-0001','confirmado','Cliente','Prueba','prueba1@test.com',NULL,'1122334455','Calle Falsa 123','CABA','Buenos Aires','1000','Orden de prueba para dashboard','mercadopago',NULL,NULL,NULL,NULL,NULL,NULL,14900.00,0.00,14900.00,'[{\"id_sku\": 2, \"imagen\": \"\", \"nombre\": \"Vestido Floral Romántico\", \"id_prod\": 1, \"cantidad\": 1, \"color_nombre\": \"Rosa\", \"talle_nombre\": \"4A\", \"precio_unitario\": 13900.0}]','2026-06-22 18:30:33',NULL,NULL,0,NULL),(2,'TEST-0002','enviado','Cliente','Dos','prueba2@test.com',NULL,'1122334456','Av. Siempreviva 742','La Plata','Buenos Aires','1900','Orden de prueba para dashboard','mercadopago',NULL,NULL,NULL,NULL,NULL,NULL,25800.00,0.00,25800.00,'[{\"id_sku\": 6, \"imagen\": \"\", \"nombre\": \"Mameluco Tejido Bebé\", \"id_prod\": 2, \"cantidad\": 2, \"color_nombre\": \"Rosa\", \"talle_nombre\": \"0-3M\", \"precio_unitario\": 12900.0}]','2026-06-22 18:30:33',NULL,NULL,0,NULL),(3,'TEST-0003','entregado','Cliente','Tres','prueba3@test.com',NULL,'1122334457','Mitre 50','Mar del Plata','Buenos Aires','7600','Orden de prueba para dashboard','mercadopago',NULL,NULL,NULL,NULL,NULL,NULL,79500.00,0.00,79500.00,'[{\"id_sku\": 9, \"imagen\": \"\", \"nombre\": \"Buzo Canguro Niño\", \"id_prod\": 3, \"cantidad\": 5, \"color_nombre\": \"Beige\", \"talle_nombre\": \"4A\", \"precio_unitario\": 15900.0}]','2026-06-22 18:30:33',NULL,NULL,0,NULL),(4,'TEST-GUEST-1782233599','pendiente','Invitado','Test','checkout-guest-1782233599@yofi.local',NULL,'1100000000','Calle 1','CABA','CABA','1406',NULL,'transferencia',NULL,NULL,NULL,NULL,NULL,NULL,1000.00,500.00,1500.00,'[]','2026-06-23 13:53:19',NULL,NULL,0,NULL),(5,'TEST-USER-1782233599','pendiente','Logueado','Test','checkout-user-1782233599@yofi.local',NULL,'1100000000','Calle 2','CABA','CABA','1406',NULL,'transferencia',NULL,NULL,NULL,NULL,NULL,NULL,2000.00,0.00,2000.00,'[]','2026-06-23 13:53:19',NULL,NULL,0,NULL),(13,'ORD-20260623-F1B477','cancelado','gonzalo','mattia','gonzalo.mattia@hotmail.com',4,'01160436765','Ruta Nacional 6 km 149.5','General Rodriguez','Buenos Aires','1748','','mercadopago','standard_delivery','OCA','6 a 10 días hábiles','{\"service\": \"Entrega a domicilio\", \"carrier_id\": 208, \"logistic_type\": \"xd_dropoff\"}',NULL,NULL,25590.00,491379.00,516969.00,'[{\"id_sku\": 155, \"imagen\": \"http://localhost/yofi/imgprod/prod-13-14-1782231749-0.jpg\", \"nombre\": \"Gorro aspen\", \"id_prod\": 13, \"cantidad\": 1, \"color_nombre\": \"Natural\", \"talle_nombre\": \"Único\", \"precio_unitario\": 15590}, {\"id_sku\": 153, \"imagen\": \"http://localhost/yofi/imgprod/prod-11-16-1782231562-0.jpg\", \"nombre\": \"Bandana tejida\", \"id_prod\": 11, \"cantidad\": 1, \"color_nombre\": \"Chocolate\", \"talle_nombre\": \"Único\", \"precio_unitario\": 5000}, {\"id_sku\": 154, \"imagen\": \"http://localhost/yofi/imgprod/prod-11-17-1782231642-0.jpg\", \"nombre\": \"Bandana tejida\", \"id_prod\": 11, \"cantidad\": 1, \"color_nombre\": \"Gris topo\", \"talle_nombre\": \"Único\", \"precio_unitario\": 5000}]','2026-06-23 14:39:27','2026-06-29 19:29:21','2026-06-23 15:09:27',0,NULL),(14,'ORD-20260623-77204D','cancelado','gonzalo','mattia','gonzalo.mattia@hotmail.com',4,'+541160436765','Fred Aden 406','Villa Sarmiento','Buenos Aires','1706','','mercadopago','standard_delivery','OCA','7 a 12 días hábiles','{\"service\": \"Entrega a domicilio\", \"carrier_id\": 208, \"logistic_type\": \"xd_dropoff\"}',NULL,NULL,40000.00,491641.00,531641.00,'[{\"id_sku\": 184, \"imagen\": \"/yofi/imgprod/prod-17-15-1782231439-0.jpg\", \"nombre\": \"Delantal vintage sin bordar\", \"id_prod\": 17, \"cantidad\": 1, \"color_nombre\": \"Verde antiguo\", \"talle_nombre\": \"Único\", \"precio_unitario\": 40000}]','2026-06-23 15:01:27','2026-06-29 19:29:21','2026-06-23 15:31:27',0,NULL),(15,'ORD-20260623-82508A','confirmado','gonzalo','mattia','gonzalo.mattia@hotmail.com',4,'+541160436765','Fred Aden 406','Villa Sarmiento','Buenos Aires','1706','','mercadopago','standard_delivery','OCA','7 a 12 días hábiles','{\"service\": \"Entrega a domicilio\", \"carrier_id\": 208, \"logistic_type\": \"xd_dropoff\"}','27817827','https://app.zipnova.com.ar/track/1CcLeoqibws7Ep93fZfiWV',5000.00,491006.00,496006.00,'[{\"id_sku\": 153, \"imagen\": \"/yofi/imgprod/prod-11-16-1782231562-0.jpg\", \"nombre\": \"Bandana tejida\", \"id_prod\": 11, \"cantidad\": 1, \"color_nombre\": \"Chocolate\", \"talle_nombre\": \"Único\", \"precio_unitario\": 5000}]','2026-06-23 15:03:52','2026-07-02 14:35:01','2026-06-23 15:33:52',0,NULL),(16,'ORD-20260701-F62F5D','cancelado','gonzalo','mattia','gonzalo.mattia@hotmail.com',4,'+541160436765','Fred Aden 406','Villa Sarmiento','Buenos Aires','1706','','transferencia','standard_delivery','OCA','7 a 12 días hábiles','{\"service\": \"Entrega a domicilio\", \"carrier_id\": 208, \"logistic_type\": \"xd_dropoff\"}',NULL,NULL,35590.00,491561.00,527151.00,'[{\"id_sku\": 155, \"imagen\": \"http://localhost/yofi/imgprod/prod-13-14-1782231749-0.jpg\", \"nombre\": \"Gorro aspen\", \"id_prod\": 13, \"cantidad\": 1, \"color_nombre\": \"Natural\", \"talle_nombre\": \"Único\", \"precio_unitario\": 15590}, {\"id_sku\": 153, \"imagen\": \"http://localhost/yofi/imgprod/prod-11-16-1782231562-0.jpg\", \"nombre\": \"Bandana tejida\", \"id_prod\": 11, \"cantidad\": 3, \"color_nombre\": \"Chocolate\", \"talle_nombre\": \"Único\", \"precio_unitario\": 5000}, {\"id_sku\": 154, \"imagen\": \"http://localhost/yofi/imgprod/prod-11-17-1782231642-0.jpg\", \"nombre\": \"Bandana tejida\", \"id_prod\": 11, \"cantidad\": 1, \"color_nombre\": \"Gris topo\", \"talle_nombre\": \"Único\", \"precio_unitario\": 5000}]','2026-06-30 21:39:27','2026-06-30 23:25:48','2026-06-30 22:09:27',0,NULL),(17,'ORD-20260701-AC4019','cancelado','gonzalo','mattia','gonzalo.mattia@hotmail.com',4,'+541160436765','Fred Aden 406','Villa Sarmiento','Buenos Aires','1706','','transferencia','standard_delivery','OCA','7 a 12 días hábiles','{\"service\": \"Entrega a domicilio\", \"carrier_id\": 208, \"logistic_type\": \"xd_dropoff\"}',NULL,NULL,5000.00,491006.00,496006.00,'[{\"id_sku\": 153, \"imagen\": \"/yofi/imgprod/prod-11-16-1782231562-0.jpg\", \"nombre\": \"Bandana tejida\", \"id_prod\": 11, \"cantidad\": 1, \"color_nombre\": \"Chocolate\", \"talle_nombre\": \"Único\", \"precio_unitario\": 5000}]','2026-06-30 21:43:06','2026-06-30 23:25:48','2026-06-30 22:13:06',0,NULL),(18,'ORD-20260701-E10DFF','enviado','Juan','Pérez','gonzalo@yagondesign.com.ar',NULL,'1122334455','Av. Corrientes 1234','CABA','Ciudad Autónoma de Buenos Aires','1414','','transferencia','standard_delivery','OCA','7 a 8 días hábiles','{\"service\": \"Entrega a domicilio\", \"carrier_id\": 208, \"logistic_type\": \"xd_dropoff\"}',NULL,NULL,10000.00,491096.00,501096.00,'[{\"id_sku\": 153, \"imagen\": \"http://localhost/yofi/imgprod/prod-11-16-1782231562-0.jpg\", \"nombre\": \"Bandana tejida\", \"id_prod\": 11, \"cantidad\": 2, \"color_nombre\": \"Chocolate\", \"talle_nombre\": \"Único\", \"precio_unitario\": 5000}]','2026-06-30 22:27:58','2026-06-30 22:51:59','2026-06-30 22:57:58',1,NULL),(20,'ORD-20260701-7F0888','confirmado','gonzalo','mattia','gonzalo.mattia@hotmail.com',4,'01160436765','Av. Corrientes 1234','CABA','Ciudad Autónoma de Buenos Aires','1414','','transferencia','standard_delivery','OCA','6 a 7 días hábiles','{\"service\": \"Entrega a domicilio\", \"carrier_id\": 208, \"logistic_type\": \"xd_dropoff\"}',NULL,NULL,10000.00,491096.00,501096.00,'[{\"id_sku\": 153, \"imagen\": \"http://localhost/yofi/imgprod/prod-11-16-1782231562-0.jpg\", \"nombre\": \"Bandana tejida\", \"id_prod\": 11, \"cantidad\": 2, \"color_nombre\": \"Chocolate\", \"talle_nombre\": \"Único\", \"precio_unitario\": 5000}]','2026-07-01 00:30:15','2026-07-01 00:33:03','2026-07-01 01:00:15',1,NULL),(21,'ORD-20260702-82D47F','cancelado','gonzalo','mattia','gonzalo.mattia@hotmail.com',4,'01160436765','Fred Aden 406','Villa Sarmiento','Buenos Aires','1706','','mercadopago','standard_delivery','OCA','7 a 12 días hábiles','{\"service\": \"Entrega a domicilio\", \"carrier_id\": 208, \"logistic_type\": \"xd_dropoff\"}',NULL,NULL,55000.00,494490.00,549490.00,'[{\"id_sku\": 184, \"imagen\": \"/yofi/imgprod/prod-17-15-1782231439-0.jpg\", \"nombre\": \"Delantal vintage sin bordar\", \"id_prod\": 17, \"cantidad\": 1, \"color_nombre\": \"Verde antiguo\", \"talle_nombre\": \"Único\", \"precio_unitario\": 40000}, {\"id_sku\": 151, \"imagen\": \"/yofi/imgprod/prod-7-13-1782230965-0.jpg\", \"nombre\": \"Infinito tejido\", \"id_prod\": 7, \"cantidad\": 1, \"color_nombre\": \"Azul\", \"talle_nombre\": \"Único\", \"precio_unitario\": 15000}]','2026-07-02 20:38:32','2026-07-02 21:10:37','2026-07-02 21:08:32',0,NULL),(22,'ORD-20260702-9D9DF4','cancelado','gonzalo','mattia','gonzalo.mattia@hotmail.com',4,'01160436765','Fred Aden 406','Villa Sarmiento','Buenos Aires','1706','','mercadopago','standard_delivery','OCA','7 a 12 días hábiles','{\"service\": \"Entrega a domicilio\", \"carrier_id\": 208, \"logistic_type\": \"xd_dropoff\"}',NULL,NULL,40000.00,494218.00,534218.00,'[{\"id_sku\": 184, \"imagen\": \"/yofi/imgprod/prod-17-15-1782231439-0.jpg\", \"nombre\": \"Delantal vintage sin bordar\", \"id_prod\": 17, \"cantidad\": 1, \"color_nombre\": \"Verde antiguo\", \"talle_nombre\": \"Único\", \"precio_unitario\": 40000}]','2026-07-02 20:56:41','2026-07-02 21:41:08','2026-07-02 21:26:41',0,NULL),(23,'ORD-20260703-D7C1D8','cancelado','gonzalo','mattia','gonzalo.mattia@hotmail.com',4,'01160436765','Fred Aden 406','Villa Sarmiento','Buenos Aires','1706','','mercadopago','standard_delivery','OCA','7 a 12 días hábiles','{\"service\": \"Entrega a domicilio\", \"carrier_id\": 208, \"logistic_type\": \"xd_dropoff\"}',NULL,NULL,15590.00,493775.00,509365.00,'[{\"id_sku\": 158, \"imagen\": \"/yofi/imgprod/prod-16-11-1782232065-0.jpg\", \"nombre\": \"Gorro pompón bordó\", \"id_prod\": 16, \"cantidad\": 1, \"color_nombre\": \"Bordó\", \"talle_nombre\": \"Único\", \"precio_unitario\": 15590}]','2026-07-02 21:10:37','2026-07-02 21:41:08','2026-07-02 21:40:37',0,NULL),(24,'ORD-20260703-45DA20','cancelado','gonzalo','mattia','gonzalo.mattia@hotmail.com',4,'+541160436765','Fred Aden 406','Villa Sarmiento','Buenos Aires','1706','','mercadopago','standard_delivery','OCA','7 a 12 días hábiles','{\"service\": \"Entrega a domicilio\", \"carrier_id\": 208, \"logistic_type\": \"xd_dropoff\"}',NULL,NULL,40000.00,494218.00,534218.00,'[{\"id_sku\": 184, \"imagen\": \"/yofi/imgprod/prod-17-15-1782231439-0.jpg\", \"nombre\": \"Delantal vintage sin bordar\", \"id_prod\": 17, \"cantidad\": 1, \"color_nombre\": \"Verde antiguo\", \"talle_nombre\": \"Único\", \"precio_unitario\": 40000}]','2026-07-02 21:41:08','2026-07-03 10:59:30','2026-07-02 22:11:08',0,NULL),(25,'ORD-20260703-5188C8','cancelado','Test','Comprador','test@yofi.com.ar',NULL,'1122334455','Av Corrientes 1234','CABA','Ciudad Autónoma de Buenos Aires','1414','','mercadopago','standard_delivery','OCA','7 a 8 días hábiles','{\"service\": \"Entrega a domicilio\", \"carrier_id\": 208, \"logistic_type\": \"xd_dropoff\"}',NULL,NULL,40000.00,494218.00,534218.00,'[{\"id_sku\": 184, \"imagen\": \"/yofi/imgprod/prod-17-15-1782231439-0.jpg\", \"nombre\": \"Delantal vintage sin bordar\", \"id_prod\": 17, \"cantidad\": 1, \"color_nombre\": \"Verde antiguo\", \"talle_nombre\": \"Único\", \"precio_unitario\": 40000}]','2026-07-02 21:48:53','2026-07-03 10:59:30','2026-07-02 22:18:53',0,NULL),(26,'ORD-20260703-929633','cancelado','gonzalo','mattia','gonzalo.mattia@hotmail.com',4,'01160436765','Fred Aden 406','Villa Sarmiento','Buenos Aires','1706','','mercadopago','standard_delivery','OCA','7 a 12 días hábiles','{\"service\": \"Entrega a domicilio\", \"carrier_id\": 208, \"logistic_type\": \"xd_dropoff\"}',NULL,NULL,15590.00,493775.00,509365.00,'[{\"id_sku\": 158, \"imagen\": \"/yofi/imgprod/prod-16-11-1782232065-0.jpg\", \"nombre\": \"Gorro pompón bordó\", \"id_prod\": 16, \"cantidad\": 1, \"color_nombre\": \"Bordó\", \"talle_nombre\": \"Único\", \"precio_unitario\": 15590}]','2026-07-02 21:57:29','2026-07-03 10:59:30','2026-07-02 22:27:29',0,NULL),(27,'ORD-20260703-2DF146','pendiente','Test','Comprador','test.comprador@example.com',NULL,'1122334455','Av. Siempre Viva 123','Ciudad Autónoma de Buenos Aires','Ciudad Autónoma de Buenos Aires','1425','','mercadopago','standard_delivery','OCA','6 a 7 días hábiles','{\"service\": \"Entrega a domicilio\", \"carrier_id\": 208, \"logistic_type\": \"xd_dropoff\"}',NULL,NULL,40000.00,494218.00,534218.00,'[{\"id_sku\": 184, \"imagen\": \"/yofi/imgprod/prod-17-15-1782231439-0.jpg\", \"nombre\": \"Delantal vintage sin bordar\", \"id_prod\": 17, \"cantidad\": 1, \"color_nombre\": \"Verde antiguo\", \"talle_nombre\": \"Único\", \"precio_unitario\": 40000}]','2026-07-03 10:59:30','2026-07-03 10:59:30','2026-07-03 11:29:30',1,NULL),(28,'ORD-20260703-B6B9E3','pendiente','gonzalo','mattia','gonzalo@yagondesign.com.ar',NULL,'1160436765','yapeyu 365','villa sarmiento','Buenos Aires','1706','','mercadopago','standard_delivery','OCA','6 a 11 días hábiles','{\"service\": \"Entrega a domicilio\", \"carrier_id\": 208, \"logistic_type\": \"xd_dropoff\"}',NULL,NULL,15590.00,493775.00,509365.00,'[{\"id_sku\": 158, \"imagen\": \"/yofi/imgprod/prod-16-11-1782232065-0.jpg\", \"nombre\": \"Gorro pompón bordó\", \"id_prod\": 16, \"cantidad\": 1, \"color_nombre\": \"Bordó\", \"talle_nombre\": \"Único\", \"precio_unitario\": 15590}]','2026-07-03 11:06:35','2026-07-03 11:06:35','2026-07-03 11:36:35',1,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_ordenes_historial`
--

LOCK TABLES `tbl_ordenes_historial` WRITE;
/*!40000 ALTER TABLE `tbl_ordenes_historial` DISABLE KEYS */;
INSERT INTO `tbl_ordenes_historial` VALUES (1,15,'pendiente','confirmado','2026-06-23 15:05:27','MercadoPago','accredited',NULL,NULL),(2,15,'confirmado','preparando_envio','2026-06-23 15:19:10','Zipnova API','Envío creado en Zipnova','https://app.zipnova.com.ar/track/1CcLeoqibws7Ep93fZfiWV',NULL),(3,18,'pendiente','confirmado','2026-06-30 22:31:09','admin','Cambio manual desde admin',NULL,NULL),(4,18,'confirmado','enviado','2026-06-30 22:51:59','admin','Cambio manual desde admin',NULL,NULL),(5,20,'pendiente','confirmado','2026-07-01 00:33:03','admin','Cambio manual desde admin',NULL,NULL),(6,15,'confirmado','enviado','2026-07-02 14:34:17','admin','Cambio manual desde admin',NULL,NULL),(7,15,'enviado','confirmado','2026-07-02 14:35:01','admin','Cambio manual desde admin',NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_prod_imagenes`
--

LOCK TABLES `tbl_prod_imagenes` WRITE;
/*!40000 ALTER TABLE `tbl_prod_imagenes` DISABLE KEYS */;
INSERT INTO `tbl_prod_imagenes` VALUES (1,1,3,'prod-001-rosa-1.jpg',1,1),(2,1,4,'prod-001-celeste-1.jpg',1,1),(3,2,3,'prod-002-rosa-1.jpg',1,1),(4,3,7,'prod-003-beige-1.jpg',1,1),(5,4,1,'prod-004-blanco-1.jpg',1,1),(6,5,6,'prod-005-verde-1.jpg',1,1),(7,6,NULL,'prod-6-main.webp',0,1),(8,6,NULL,'prod-6-gen-1781642237.jpg',0,1),(9,6,4,'prod-6-4-1781642901-0.jpg',0,1),(22,7,11,'prod-7-11-1782230798-0.jpg',0,1),(23,7,11,'prod-7-11-1782230798-1.jpg',1,0),(24,7,11,'prod-7-11-1782230798-2.jpg',2,0),(25,7,12,'prod-7-12-1782230883-0.jpg',0,1),(26,7,12,'prod-7-12-1782230883-1.jpg',1,0),(27,7,12,'prod-7-12-1782230883-2.jpg',2,0),(28,7,13,'prod-7-13-1782230965-0.jpg',0,1),(29,7,13,'prod-7-13-1782230965-1.jpg',1,0),(30,7,13,'prod-7-13-1782230965-2.jpg',2,0),(31,17,15,'prod-17-15-1782231439-0.jpg',0,1),(32,17,15,'prod-17-15-1782231439-1.jpg',1,0),(33,17,15,'prod-17-15-1782231439-2.jpg',2,0),(34,17,15,'prod-17-15-1782231439-3.jpg',3,0),(35,17,15,'prod-17-15-1782231439-4.jpg',4,0),(36,17,15,'prod-17-15-1782231439-5.jpg',5,0),(37,11,16,'prod-11-16-1782231562-0.jpg',0,1),(38,11,16,'prod-11-16-1782231562-1.jpg',1,0),(39,11,16,'prod-11-16-1782231562-2.jpg',2,0),(40,11,17,'prod-11-17-1782231642-0.jpg',0,1),(41,11,17,'prod-11-17-1782231642-1.jpg',1,0),(42,13,14,'prod-13-14-1782231749-0.jpg',0,1),(43,13,14,'prod-13-14-1782231749-1.jpg',1,0),(44,13,14,'prod-13-14-1782231749-2.jpg',2,0),(45,13,14,'prod-13-14-1782231749-3.jpg',3,0),(46,13,15,'prod-13-15-1782231828-0.jpg',1,1),(47,13,15,'prod-13-15-1782231828-1.jpg',2,0),(48,13,15,'prod-13-15-1782231828-2.jpg',3,0),(49,13,15,'prod-13-15-1782231828-3.jpg',4,0),(50,13,3,'prod-13-3-1782231900-0.jpg',1,1),(51,13,3,'prod-13-3-1782231900-1.jpg',2,0),(52,13,3,'prod-13-3-1782231900-2.jpg',3,0),(53,13,3,'prod-13-3-1782231900-3.jpg',4,0),(54,16,11,'prod-16-11-1782232065-0.jpg',0,1),(55,16,11,'prod-16-11-1782232065-1.jpg',1,0),(56,16,11,'prod-16-11-1782232065-2.jpg',2,0),(57,16,11,'prod-16-11-1782232065-3.jpg',3,0),(58,18,7,'prod-18-7-1782232111-0.jpg',0,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_productos`
--

LOCK TABLES `tbl_productos` WRITE;
/*!40000 ALTER TABLE `tbl_productos` DISABLE KEYS */;
INSERT INTO `tbl_productos` VALUES (1,1,'Vestido Floral Romántico','vestido-floral-rom-antico','YF-001',18500.00,13900.00,'Vestido de algodón importado con estampado floral.','','',0.30,40.00,35.00,2.00,0,0,0,'',1,'2026-06-16 13:46:57','2026-06-22 22:07:07'),(2,1,'Mameluco Tejido Bebé','mameluco-tejido-bebe','YF-002',12900.00,NULL,'Mameluco tejido importado, suave al tacto.',NULL,NULL,0.20,35.00,30.00,2.00,0,0,0,NULL,0,'2026-06-16 13:46:57','2026-06-22 21:27:10'),(3,2,'Buzo Canguro Niño','buzo-canguro-nino','YF-003',15900.00,NULL,'Buzo canguro de algodón importado.',NULL,NULL,0.35,45.00,40.00,3.00,0,0,0,NULL,0,'2026-06-16 13:46:57','2026-06-22 21:27:10'),(4,3,'Conjunto Primavera','conjunto-primavera','YF-004',22000.00,17500.00,'Conjunto de dos piezas, top y pantalón.',NULL,NULL,0.40,45.00,38.00,3.00,0,0,1,'3x2',0,'2026-06-16 13:46:57','2026-06-22 21:27:10'),(5,1,'Campera Acolchada Niña','campera-acolchada-nina','YF-005',28500.00,NULL,'Campera acolchada importada, abrigada y liviana.',NULL,NULL,0.50,48.00,42.00,5.00,0,0,0,NULL,1,'2026-06-16 13:46:57','2026-06-22 22:09:03'),(6,1,'Body Mini Argentina campeon','body-mini-argentina-campeon','I26M8806',26900.00,NULL,'El body mini Argentina, hecho en jersey con estampa.Tiene broche en escote y entre piernas. Para armar un look futbolero.\r\nComposición: 100% algodón\r\nTalles: del S al XXXL','','',0.50,20.00,20.00,5.00,0,1,1,'2x1',1,'2026-06-16 17:36:03','2026-06-22 22:17:17'),(7,9,'Infinito tejido','infinito-tejido','YF-MINI-INF',15000.00,NULL,'Fabricados en Acrílico Premium: Elegimos los mejores insumos para cuidar la piel de tu Mini. El acrílico ofrece suavidad al tacto y excelente durabilidad.\r\n\r\n- Hipoalergénicos y Sin Picazón: Nuestros Infinitos Tejidos son hipoalergénicos y no causan ninguna picazón. ¡Tu Mini cómodo y canchero todo el tiempo!.\r\n\r\n- Talles: Talle único que abarca desde recién nacidos hasta los 6 años. El diámetro del cuello es de aprox. 90cm','','',0.00,0.00,0.00,0.00,1,1,0,'',0,'2026-06-22 21:22:49','2026-06-23 13:10:24'),(11,9,'Bandana tejida','bandana-tejida','YF-MINI-BAN',15000.00,5000.00,'Nuestros nuevos y exclusivos cuellitos tipo bandana tejidos están fabricados con Acrilico calidad premium. Son hipoalergénicos, super suaves y no pican.\r\n\r\nPoseen un practico botón de coco para facilitar su colocación y es el complemento ideal para abrigar a tu bebé este INVIERNO.\r\n\r\nÉste modelito abarca desde los 6 meses hasta los 3 años aproximadamente pero a los más grandes también les va!','','',0.00,0.00,0.00,0.00,1,1,0,'',0,'2026-06-22 21:22:49','2026-06-23 13:20:44'),(13,9,'Gorro aspen','gorro-aspen','YF-MINI-ASP',15590.00,NULL,'Fabricados en Acrílico Premium: Elegimos los mejores insumos para cuidar la piel de tu Mini. El acrílico ofrece suavidad al tacto y excelente durabilidad.\r\n\r\n- Hipoalergénicos y Sin Picazón: Nuestros Gorros Pompón son hipoalergénicos y no causan ninguna picazón. ¡Tu Mini cómodo y canchero todo el tiempo!.\r\n\r\n- Ajuste Elástico: El diámetro del gorro en la frente es de 32 cm, pero la elasticidad de la lana permite que se ajuste con comodidad hasta 55 cm. Esto significa un ajuste seguro y adaptable\r\n\r\n- Talles: Talle único para todas las edades. Van desde los 6 meses hasta los 6 años, adaptándose al crecimiento de tu bebé y brindando uso prolongado.\r\n\r\n- Hecho con Amor: Diseñado y Fabricado en Mar del Plata, Argentina','','',0.00,0.00,0.00,0.00,1,0,0,'',0,'2026-06-22 21:22:49','2026-06-23 13:25:19'),(16,9,'Gorro pompón bordó','gorro-pomp-on-bord-o','YF-MINI-010',22390.00,15590.00,'- Fabricados en Acrílico Premium: Elegimos los mejores insumos para cuidar la piel de tu Mini. El acrílico ofrece suavidad al tacto y excelente durabilidad.\r\n\r\n- Hipoalergénicos y Sin Picazón: Nuestros Gorros Pompón son hipoalergénicos y no causan ninguna picazón. ¡Tu Mini cómodo y canchero todo el tiempo!.\r\n\r\n- Ajuste Elástico: El diámetro del gorro en la frente es de 32 cm, pero la elasticidad de la lana permite que se ajuste con comodidad hasta 55 cm. Esto significa un ajuste seguro y adaptable\r\n\r\n- Talles: Talle único para todas las edades. Van desde los 6 meses hasta los 6 años, adaptándose al crecimiento de tu bebé y brindando uso prolongado.\r\n\r\n- Hecho con Amor: Diseñado y Fabricado en Mar del Plata, Argentina.','','',0.00,0.00,0.00,0.00,1,1,0,'',0,'2026-06-22 21:22:49','2026-06-23 13:27:46'),(17,10,'Delantal vintage sin bordar','delantal-vintage-sin-bordar','YF-REG-001',40000.00,NULL,'Delantal estilo romantico\r\nFabricado artesanalmente, edición limitada.\r\núnico color y talle.\r\nTela tusor 100% algodón','','',0.00,0.00,0.00,0.00,1,1,0,'',0,'2026-06-22 21:22:49','2026-06-23 13:17:44'),(18,10,'Bordado a pedido','bordado-a-pedido','YF-REG-002',8000.00,NULL,'','','',0.00,0.00,0.00,0.00,1,0,0,'',0,'2026-06-22 21:22:49','2026-06-23 13:28:31'),(19,4,'01gfm','01gfm','01gfm',2.00,1.00,'test','','',0.50,20.00,20.00,5.00,1,0,0,'',0,'2026-07-03 13:30:40',NULL);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_sessions`
--

LOCK TABLES `tbl_sessions` WRITE;
/*!40000 ALTER TABLE `tbl_sessions` DISABLE KEYS */;
INSERT INTO `tbl_sessions` VALUES ('0hutltcr8r7emefac3llm7hk3i',_binary 'user_id|i:82;user_email|s:29:\"test-otp-a0846f08@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1783040803),('13n46bu1hsef3lmtfep2689kb3',_binary 'user_id|i:67;user_email|s:29:\"test-otp-1159e7cb@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1783008125),('1cl1jimrl2la284gnb51lteelg',_binary 'user_id|i:49;user_email|s:29:\"test-otp-cf11fe36@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1783002995),('1g9qhq6sdjvtkgtmrgn08ps800',_binary 'user_id|i:70;user_email|s:29:\"test-otp-0c3a4103@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1783008514),('2beot7neguqqa4nggbh1altiqr',_binary 'user_id|i:55;user_email|s:29:\"test-otp-c5484645@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1783003279),('2c9436e94b17d590e1efcfcaff0005d4',_binary 'public_csrf_token|s:64:\"725f07e2cb4fe5907607921686de2644aeb7c00a72875f2fd9f2624aca99bc3a\";',1782849122),('2inpdjgq5kbqbmliv2ebt9odmd',_binary 'user_id|i:76;user_email|s:29:\"test-otp-824a8c95@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1783013736),('37jnuk6sis567b7a83ihqs3p2a',_binary 'user_id|i:22;user_email|s:29:\"test-otp-8879e5e3@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1782872765),('400b6aa7b4616aa517e8c90ff46c7d82',_binary 'public_csrf_token|s:64:\"a72f68a6a3ba4f068d4c9bb77f2f01e43db5dde9cc98548d7e0b8caefde8a132\";',1782849486),('44prhqt18uf1m9m81484d3hu2p',_binary 'user_id|i:91;user_email|s:29:\"test-otp-e34a3446@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1783041690),('4i5iu6h23sopj7dm66rbdap1ab',_binary 'user_id|i:17;user_email|s:29:\"test-otp-a9f93fff@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1782872248),('51pfu4837v7qsu59rt162tjkp1',_binary 'user_id|i:58;user_email|s:29:\"test-otp-fa6896d3@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1783007516),('5d3e446d9c1014a03c16e0479952b2f6',_binary 'public_csrf_token|s:64:\"9c9e58f143981f0c6616567ef547748639e823aa40e75a4d357e38cdb00482db\";',1782850052),('5e93d2133d57b01b8cbd1762f45bac34',_binary 'public_csrf_token|s:64:\"d93ea90308c241e851e1829f3058797c1ef7e10c9bf1fc30b514e803fd38ae59\";',1782849386),('679ac4b67e68518e8fb5c435298757e8',_binary 'public_csrf_token|s:64:\"b94ab0ec1d65ed6366987d6b3535184f64339f67cbae6c5ba5ae59954ae0b7b2\";',1782850116),('6g4r0c7ch55viu4uqknf8u4vfh',_binary 'user_id|i:34;user_email|s:29:\"test-otp-315a1d27@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1782876196),('726a6fdcc3023826c17f1510088882dc',_binary 'public_csrf_token|s:64:\"1d0b9669939fe5b1252a0162043742683e39e7c3dd00f4a684e1d998fde7f84b\";',1782850114),('73bdc2284d6005ee21a04d9899babc72',_binary 'public_csrf_token|s:64:\"c2cbfdff2894f78477098c70de6f8f03e1b71ef2233f6ebab7c14a6f3519a3b5\";',1782850049),('75u93p5uik4rf96csdl5jotfo3',_binary 'public_csrf_token|s:64:\"0a042269897d009026a7bbf11a8f82843022da05c9f14d904058c9001c5179b8\";',1783087185),('79gtteo67085e7c68g1s6p897h',_binary 'user_id|i:61;user_email|s:29:\"test-otp-2cd1f43c@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1783007589),('7ldojncjdtfbhn8lrhnkquh30n',_binary 'public_csrf_token|s:64:\"6a9bc41010deee3621192b90b6eacf9d895092ae12bee0a0074c6e2b0d44e0cd\";',1783039282),('7u0tt24mt1tr6ushbkl6hvgv43',_binary 'user_id|i:97;user_email|s:29:\"test-otp-74256941@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1783094728),('8265n6gll80q0vu6v9s8lnoas8',_binary 'public_csrf_token|s:64:\"87bde70441d091d4d85a1cbfe02c0e1bdfd01b59b3869e1f0604bf49f524bc12\";',1783087608),('845d5540ecbbac5639359e51e66fdaf8',_binary 'public_csrf_token|s:64:\"4a9c4b7244d5fdaa1371f84906ce9b8398855dd3994559ad03e20ee90436cfec\";',1782849369),('894cde19b42b6a801b5f727e50e762a4',_binary 'public_csrf_token|s:64:\"650a70d92775d9f0068234ed553a2b39dd970b82019850dded1dcdf3ef3873d3\";',1782855592),('8c3576cfe5229c79142ab777ab6a8fe5',_binary 'public_csrf_token|s:64:\"f6756aee03dd2dd6d63bff002f1b2fc92a1d6aa6f6fefc81f334bdd687c3fa05\";',1782850115),('94c69c386ceb84fc938466b3cf313e76',_binary 'public_csrf_token|s:64:\"c42fe142d76bf33210d1c5e66cc949dc2f85941fd559754b4fcea3a2e54f2a3f\";',1782850047),('9997b67e125bd1ae232ad2e7c568c359',_binary 'public_csrf_token|s:64:\"b612039ae453faa0eb5dafedc88d57553c521ef32a74930a3a9a1ec66fc49ce8\";',1782855582),('9ddcb06b6a038d0ce62edc4dcd5e01f8',_binary 'public_csrf_token|s:64:\"90a295ea35d24439c02fa5e5a0b43379efcad6e09ac6a455aab63f970efbf38e\";',1782849691),('a06ac06192cd3c73a6c469f59616977a',_binary 'public_csrf_token|s:64:\"5961f3c9e9553dc1f5613901fbc1fca488052aa0aacad1eb58f0a4c8d77abee8\";',1782849968),('a45e77da29122ce82274d6dca0b891ab',_binary 'public_csrf_token|s:64:\"3f870d7d8d187d8b779953663295abfbb17e2f57ae1cb1e4fd4760d4bc515524\";',1782849634),('ade6a3220c33de0536bd2f20c3a3e195',_binary 'public_csrf_token|s:64:\"b95dc61aae02e9061f3d0966188a817e36c22bbf8d9500aa13a6fac677bc4d9c\";',1782849611),('ae3ca509b7dc47f1bb7d54005df3169a',_binary 'public_csrf_token|s:64:\"97295d53ecef54fdcfb92a043c062cf9053bcb59861a631ddf4c6b43948b752f\";',1782850051),('anokui78vpp3cvaqpke104du9b',_binary 'user_id|i:37;user_email|s:29:\"test-otp-73c31c7a@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1782876855),('ao119jkb9jft43emej0dgmv7fu',_binary 'public_csrf_token|s:64:\"89267949786c0c0beefae0a07cdf6c994e2be56dfde47b6fc4e2bde5ef4d27c6\";',1783092361),('bm7ube0qi5amtt18r0lq20deku',_binary 'user_id|i:64;user_email|s:29:\"test-otp-0d1a26d2@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1783007962),('bqjq2g1fjj5nhg6dbv6dg2ic0e',_binary 'user_id|i:28;user_email|s:29:\"test-otp-6bf7fdd4@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1782874607),('c16c2a577a286bccdfddc91c6f208f1b',_binary 'public_csrf_token|s:64:\"0eeb55fa405c1cb5d49b51c6a16c5e8ac9312ca09c4068d51a1c393ce4760f0d\";',1782855277),('c6044319bfbe149b526f51c5fe89f288',_binary 'public_csrf_token|s:64:\"6b01d961d9160af54a98a1767f9e2e0a5fc342b90b0432d47970d7c73db12787\";',1782849045),('dc4b89058c81969ba4d388ee175b18be',_binary 'public_csrf_token|s:64:\"d32c4884ff2b696228e9a520f32df853aa11cec8d15f81fce27ef2164ea4a29c\";',1782849937),('e6j2pik739u2284d4ajgk2g2c1',_binary 'public_csrf_token|s:64:\"ae31a09075fe55ddde6df650283194e98b2dfad9154e3830d166ea27fcd9235b\";user_id|i:4;user_email|s:26:\"gonzalo.mattia@hotmail.com\";user_name|s:14:\"gonzalo mattia\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:1;',1783037443),('eb3njev1of7r63mcf57ohrlrne',_binary 'user_id|i:88;user_email|s:29:\"test-otp-7c895fa2@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1783041461),('ejdqh4h62bsm1b58ps6udco4t3',_binary 'user_id|i:73;user_email|s:29:\"test-otp-37f25b4c@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1783009651),('f0dd30cb6a0fd57dda22c9fba8abbe79',_binary 'public_csrf_token|s:64:\"5b3a58c7ee3c0340bbd9885eb8e1463eee331e7a412447d499d5d1392d96879d\";',1782849646),('f108760002f2dcf439734c9c70bf2638',_binary 'public_csrf_token|s:64:\"c3019b9a9e980a8981826e7d7bcbce1ed463427ce48a8f1c89a5c1c580f8febe\";',1782849935),('f1d886f9fb09aa74fd38b24f7dbd4e25',_binary 'public_csrf_token|s:64:\"11a814801d761648033af59b3c5b98f549a8bca745ea8bd90e173b8cdccb4bbb\";',1782849377),('fnhvns66es61geflctlm3kkege',_binary 'user_id|i:9;user_email|s:29:\"test-otp-86fe95a8@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1782871663),('fvavrl04dn8ricuqvjh0tulc5h',_binary 'user_id|i:52;user_email|s:29:\"test-otp-ad13005e@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1783003135),('ga9nve6vg34bhqooecv04pspks',_binary 'user_id|i:79;user_email|s:29:\"test-otp-484ea618@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1783035943),('hqi698008bfk1h3a9m5bbjfggu',_binary 'user_id|i:43;user_email|s:29:\"test-otp-6532de15@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1782943953),('ib41psenltm0eqn0h5besf2th9',_binary 'public_csrf_token|s:64:\"7617575a59acf98b56fd1115f412d7ff25a7c500a4d24772cafce6e3b4043ab0\";',1782862729),('j8r64re40l507lib0b51ung2cq',_binary 'public_csrf_token|s:64:\"7cb1c1733f8f30b20544e7c1d7c0dad2f913a79a77f598f804e2b5ed207e4401\";',1783096625),('jr01j1g79hl52aq7b1bm59efq3',_binary 'user_id|i:94;user_email|s:29:\"test-otp-4fe7b2bf@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1783091850),('l9hgbtimjr6t27cpqi5ot23uql',_binary 'public_csrf_token|s:64:\"343ad35b37eb8867fba5752f59f434ce30b47e471704d8615658356eb7c9b8ef\";',1783086476),('lfbfbfs30u9k2vlu8vvi4bqdnc',_binary 'user_id|i:85;user_email|s:29:\"test-otp-71b1cefb@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1783041054),('m26spfb7r5mo0v9ht3oegnk2lu',_binary 'user_id|i:7;user_email|s:29:\"test-otp-b79c3f53@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1782871427),('mk7t8b68r1bb9tcddpd4m1jojj',_binary 'user_id|i:46;user_email|s:29:\"test-otp-23622b0c@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1782944122),('mq5dtfgecitbkcvbht435q8reh',_binary 'user_id|i:8;user_email|s:29:\"test-otp-ee4ec5af@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1782871470),('nkl1v4uejm1pnnp6eggcjb97ab',_binary 'user_id|i:6;user_email|s:29:\"test-otp-4f6beb60@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1782871341),('nqrskt7b3psgr79ff35g85e53f',_binary 'user_id|i:25;user_email|s:29:\"test-otp-618eb7e2@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1782873713),('nsafg2akk864h9nnnteet8rki7',_binary 'public_csrf_token|s:64:\"c61fbdee7f6b5120566e410567c63fb2e33eddffd02fc1c7bb4c6c613e133b57\";user_id|i:4;user_email|s:26:\"gonzalo.mattia@hotmail.com\";user_name|s:14:\"gonzalo mattia\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:1;',1783036616),('o472ej5rkjfo1bn3r137q8r62p',_binary 'public_csrf_token|s:64:\"14a83e3b29c6c0a4e92974771adc1542d1b3d31543396a3da214abeefd71cba8\";user_id|i:4;user_email|s:26:\"gonzalo.mattia@hotmail.com\";user_name|s:14:\"gonzalo mattia\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:1;',1783035527),('p2n35udusqal26kdbj7s2fqgpv',_binary 'user_id|i:12;user_email|s:29:\"test-otp-0a56d6c6@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1782871978),('pa4k7mhaimljdgttqdhd9smnr0',_binary 'public_csrf_token|s:64:\"6f0c55063f692d8ad1347072b05192267757519f293fddcea55316e573de84e3\";',1783086748),('qo9j0qdtmdto5s9ej3kju4185n',_binary 'user_id|i:31;user_email|s:29:\"test-otp-3b853bfe@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1782875082),('r0h02cdpbnleria95rjr0l8k78',_binary 'public_csrf_token|s:64:\"c7b20fd5f64ea70b01ba830a9c932c1b665d09b28f9d1ffd23fda57b01297473\";',1782868625),('s3hiensdo8vnmu6d0hi04uaur3',_binary 'public_csrf_token|s:64:\"79f1a03047645488a49ce465dd097a3e0c0e504012a9d9bbbfe35daf08b2c5a6\";',1783008222),('s76dlpaonee9kstj6fm622fhus',_binary 'user_id|i:40;user_email|s:29:\"test-otp-994d5e2a@example.com\";user_name|s:8:\"Test Otp\";user_logged_in|b:1;needs_password_setup|b:0;email_verificado|b:0;',1782877337),('spvp6ovs5idkkclt3admtftlq3',_binary 'public_csrf_token|s:64:\"5699abc80c707dc8c912846d66f2465d8b2603c308513614e45f451bab2f59d1\";',1782862756),('tbnucde6co2rdn698m51o3rcho',_binary 'public_csrf_token|s:64:\"b7acd892ed0f4ccb61b1f71734f0a6597dddd661ba6aec7f0bdd2e1b6fb54645\";',1782868640),('u6ei4muo5q91m2lrrtipn8dngq',_binary 'public_csrf_token|s:64:\"4b5cb19ec47c4d6d8bbf272565a7bddf2c52e95a1485a547946d9dc347726b96\";',1783086477),('vqqqq663fmq8emf6oas70nhop8','',1783040850);
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
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_shipping_config`
--

LOCK TABLES `tbl_shipping_config` WRITE;
/*!40000 ALTER TABLE `tbl_shipping_config` DISABLE KEYS */;
INSERT INTO `tbl_shipping_config` VALUES (1,'zipnova_enabled','1','Si el envío vía Zipnova está activo en el checkout',NULL,'2026-06-22 19:06:27'),(2,'zipnova_label','Envío a domicilio','Texto visible para el cliente','2026-06-22 19:18:08','2026-06-22 19:06:27'),(3,'zipnova_eta_default','3 a 6 días hábiles','ETA mostrado cuando la API no devuelve uno específico','2026-06-22 19:17:48','2026-06-22 19:06:27'),(4,'free_shipping_threshold','150000','Monto mínimo de compra para envío gratis (0 = desactivado)','2026-06-22 20:41:20','2026-06-22 19:06:27'),(5,'pickup_enabled','1','Si retiro en local está habilitado','2026-07-03 12:19:06','2026-06-22 19:06:27'),(6,'pickup_label','Retiro en local','Texto visible para el cliente',NULL,'2026-06-22 19:06:27'),(7,'pickup_address','Once, Ciudad Autónoma de Buenos Aires (lun a vie 10 a 18hs)','Dirección de retiro mostrada al cliente','2026-07-03 12:19:06','2026-06-22 19:06:27');
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
INSERT INTO `tbl_skus` VALUES (1,1,3,5,'YF-001-ROSA-2A',9,0,0.00,1),(2,1,3,7,'YF-001-ROSA-4A',10,0,0.00,1),(3,1,3,8,'YF-001-ROSA-6A',10,0,0.00,1),(4,1,4,5,'YF-001-CEL-2A',10,0,0.00,1),(5,1,4,7,'YF-001-CEL-4A',10,0,0.00,1),(6,2,3,1,'YF-002-ROSA-03M',10,0,0.00,1),(7,2,3,2,'YF-002-ROSA-36M',10,0,0.00,1),(8,2,3,3,'YF-002-ROSA-612M',10,0,0.00,1),(9,3,7,7,'YF-003-BEI-4A',10,0,0.00,1),(10,3,7,8,'YF-003-BEI-6A',10,0,0.00,1),(11,3,7,9,'YF-003-BEI-8A',10,0,0.00,1),(12,4,1,5,'YF-004-BLA-2A',10,0,0.00,1),(13,4,1,7,'YF-004-BLA-4A',10,0,0.00,1),(14,5,6,7,'YF-005-VER-4A',10,0,0.00,1),(15,5,6,8,'YF-005-VER-6A',10,0,0.00,1),(16,5,6,9,'YF-005-VER-8A',10,0,0.00,1),(17,6,1,1,'YOFI-6-1-1',10,0,0.00,1),(18,6,1,2,'YOFI-6-1-2',10,0,0.00,1),(19,6,1,3,'YOFI-6-1-3',10,0,0.00,1),(20,6,1,4,'YOFI-6-1-4',10,0,0.00,1),(21,6,1,5,'YOFI-6-1-5',10,0,0.00,1),(22,6,1,6,'YOFI-6-1-6',10,0,0.00,1),(23,6,1,7,'YOFI-6-1-7',10,0,0.00,1),(24,6,1,8,'YOFI-6-1-8',10,0,0.00,1),(25,6,1,9,'YOFI-6-1-9',10,0,0.00,1),(26,6,1,10,'YOFI-6-1-10',10,0,0.00,1),(27,6,1,11,'YOFI-6-1-11',10,0,0.00,1),(28,6,2,1,'YOFI-6-2-1',10,0,0.00,1),(29,6,2,2,'YOFI-6-2-2',10,0,0.00,1),(30,6,2,3,'YOFI-6-2-3',10,0,0.00,1),(31,6,2,4,'YOFI-6-2-4',10,0,0.00,1),(32,6,2,5,'YOFI-6-2-5',10,0,0.00,1),(33,6,2,6,'YOFI-6-2-6',10,0,0.00,1),(34,6,2,7,'YOFI-6-2-7',10,0,0.00,1),(35,6,2,8,'YOFI-6-2-8',10,0,0.00,1),(36,6,2,9,'YOFI-6-2-9',10,0,0.00,1),(37,6,2,10,'YOFI-6-2-10',10,0,0.00,1),(38,6,2,11,'YOFI-6-2-11',10,0,0.00,1),(39,6,3,1,'YOFI-6-3-1',10,0,0.00,1),(40,6,3,2,'YOFI-6-3-2',10,0,0.00,1),(41,6,3,3,'YOFI-6-3-3',10,0,0.00,1),(42,6,3,4,'YOFI-6-3-4',10,0,0.00,1),(43,6,3,5,'YOFI-6-3-5',10,0,0.00,1),(44,6,3,6,'YOFI-6-3-6',10,0,0.00,1),(45,6,3,7,'YOFI-6-3-7',10,0,0.00,1),(46,6,3,8,'YOFI-6-3-8',10,0,0.00,1),(47,6,3,9,'YOFI-6-3-9',10,0,0.00,1),(48,6,3,10,'YOFI-6-3-10',10,0,0.00,1),(49,6,3,11,'YOFI-6-3-11',10,0,0.00,1),(50,6,4,1,'YOFI-6-4-1',10,0,0.00,1),(51,6,4,2,'YOFI-6-4-2',10,0,0.00,1),(52,6,4,3,'YOFI-6-4-3',10,0,0.00,1),(53,6,4,4,'YOFI-6-4-4',10,0,0.00,1),(54,6,4,5,'YOFI-6-4-5',10,0,0.00,1),(55,6,4,6,'YOFI-6-4-6',10,0,0.00,1),(56,6,4,7,'YOFI-6-4-7',10,0,0.00,1),(57,6,4,8,'YOFI-6-4-8',10,0,0.00,1),(58,6,4,9,'YOFI-6-4-9',10,0,0.00,1),(59,6,4,10,'YOFI-6-4-10',10,0,0.00,1),(60,6,4,11,'YOFI-6-4-11',10,0,0.00,1),(61,6,5,1,'YOFI-6-5-1',10,0,0.00,1),(62,6,5,2,'YOFI-6-5-2',10,0,0.00,1),(63,6,5,3,'YOFI-6-5-3',10,0,0.00,1),(64,6,5,4,'YOFI-6-5-4',10,0,0.00,1),(65,6,5,5,'YOFI-6-5-5',10,0,0.00,1),(66,6,5,6,'YOFI-6-5-6',10,0,0.00,1),(67,6,5,7,'YOFI-6-5-7',10,0,0.00,1),(68,6,5,8,'YOFI-6-5-8',10,0,0.00,1),(69,6,5,9,'YOFI-6-5-9',10,0,0.00,1),(70,6,5,10,'YOFI-6-5-10',10,0,0.00,1),(71,6,5,11,'YOFI-6-5-11',10,0,0.00,1),(72,6,6,1,'YOFI-6-6-1',10,0,0.00,1),(73,6,6,2,'YOFI-6-6-2',10,0,0.00,1),(74,6,6,3,'YOFI-6-6-3',10,0,0.00,1),(75,6,6,4,'YOFI-6-6-4',10,0,0.00,1),(76,6,6,5,'YOFI-6-6-5',10,0,0.00,1),(77,6,6,6,'YOFI-6-6-6',10,0,0.00,1),(78,6,6,7,'YOFI-6-6-7',10,0,0.00,1),(79,6,6,8,'YOFI-6-6-8',10,0,0.00,1),(80,6,6,9,'YOFI-6-6-9',10,0,0.00,1),(81,6,6,10,'YOFI-6-6-10',10,0,0.00,1),(82,6,6,11,'YOFI-6-6-11',10,0,0.00,1),(83,6,7,1,'YOFI-6-7-1',10,0,0.00,1),(84,6,7,2,'YOFI-6-7-2',10,0,0.00,1),(85,6,7,3,'YOFI-6-7-3',10,0,0.00,1),(86,6,7,4,'YOFI-6-7-4',10,0,0.00,1),(87,6,7,5,'YOFI-6-7-5',10,0,0.00,1),(88,6,7,6,'YOFI-6-7-6',10,0,0.00,1),(89,6,7,7,'YOFI-6-7-7',10,0,0.00,1),(90,6,7,8,'YOFI-6-7-8',10,0,0.00,1),(91,6,7,9,'YOFI-6-7-9',10,0,0.00,1),(92,6,7,10,'YOFI-6-7-10',10,0,0.00,1),(93,6,7,11,'YOFI-6-7-11',10,0,0.00,1),(94,6,8,1,'YOFI-6-8-1',10,0,0.00,1),(95,6,8,2,'YOFI-6-8-2',10,0,0.00,1),(96,6,8,3,'YOFI-6-8-3',10,0,0.00,1),(97,6,8,4,'YOFI-6-8-4',10,0,0.00,1),(98,6,8,5,'YOFI-6-8-5',10,0,0.00,1),(99,6,8,6,'YOFI-6-8-6',10,0,0.00,1),(100,6,8,7,'YOFI-6-8-7',10,0,0.00,1),(101,6,8,8,'YOFI-6-8-8',10,0,0.00,1),(102,6,8,9,'YOFI-6-8-9',10,0,0.00,1),(103,6,8,10,'YOFI-6-8-10',10,0,0.00,1),(104,6,8,11,'YOFI-6-8-11',10,0,0.00,1),(105,6,9,1,'YOFI-6-9-1',10,0,0.00,1),(106,6,9,2,'YOFI-6-9-2',10,0,0.00,1),(107,6,9,3,'YOFI-6-9-3',10,0,0.00,1),(108,6,9,4,'YOFI-6-9-4',10,0,0.00,1),(109,6,9,5,'YOFI-6-9-5',10,0,0.00,1),(110,6,9,6,'YOFI-6-9-6',10,0,0.00,1),(111,6,9,7,'YOFI-6-9-7',10,0,0.00,1),(112,6,9,8,'YOFI-6-9-8',10,0,0.00,1),(113,6,9,9,'YOFI-6-9-9',10,0,0.00,1),(114,6,9,10,'YOFI-6-9-10',10,0,0.00,1),(115,6,9,11,'YOFI-6-9-11',10,0,0.00,1),(116,6,10,1,'YOFI-6-10-1',10,0,0.00,1),(117,6,10,2,'YOFI-6-10-2',10,0,0.00,1),(118,6,10,3,'YOFI-6-10-3',10,0,0.00,1),(119,6,10,4,'YOFI-6-10-4',10,0,0.00,1),(120,6,10,5,'YOFI-6-10-5',10,0,0.00,1),(121,6,10,6,'YOFI-6-10-6',10,0,0.00,1),(122,6,10,7,'YOFI-6-10-7',10,0,0.00,1),(123,6,10,8,'YOFI-6-10-8',10,0,0.00,1),(124,6,10,9,'YOFI-6-10-9',10,0,0.00,1),(125,6,10,10,'YOFI-6-10-10',10,0,0.00,1),(126,6,10,11,'YOFI-6-10-11',10,0,0.00,1),(149,7,11,12,'YF-MINI-001-UNI',10,0,0.00,1),(150,7,12,12,'YF-MINI-002-UNI',10,0,0.00,1),(151,7,13,12,'YF-MINI-003-UNI',10,0,0.00,1),(152,7,3,12,'YF-MINI-004-UNI',0,0,0.00,1),(153,11,16,12,'YF-MINI-005-UNI',9,4,0.00,1),(154,11,17,12,'YF-MINI-006-UNI',10,0,0.00,1),(155,13,14,12,'YF-MINI-007-UNI',10,0,0.00,1),(156,13,15,12,'YF-MINI-008-UNI',10,0,0.00,1),(157,13,3,12,'YF-MINI-009-UNI',10,0,0.00,1),(158,16,11,12,'YF-MINI-010-UNI',10,1,0.00,1),(159,17,7,12,'YF-REG-001-UNI',0,0,0.00,1),(160,18,7,12,'YF-REG-002-UNI',10,0,0.00,1),(161,7,3,1,'YOFI-7-3-1',0,0,0.00,1),(162,7,3,2,'YOFI-7-3-2',0,0,0.00,1),(163,7,3,3,'YOFI-7-3-3',0,0,0.00,1),(164,7,3,4,'YOFI-7-3-4',0,0,0.00,1),(165,7,3,5,'YOFI-7-3-5',0,0,0.00,1),(166,7,3,6,'YOFI-7-3-6',0,0,0.00,1),(167,7,3,7,'YOFI-7-3-7',0,0,0.00,1),(168,7,3,8,'YOFI-7-3-8',0,0,0.00,1),(169,7,3,9,'YOFI-7-3-9',0,0,0.00,1),(170,7,3,10,'YOFI-7-3-10',0,0,0.00,1),(171,7,3,11,'YOFI-7-3-11',0,0,0.00,1),(173,17,15,1,'YOFI-17-15-1',0,0,0.00,1),(174,17,15,2,'YOFI-17-15-2',0,0,0.00,1),(175,17,15,3,'YOFI-17-15-3',0,0,0.00,1),(176,17,15,4,'YOFI-17-15-4',0,0,0.00,1),(177,17,15,5,'YOFI-17-15-5',0,0,0.00,1),(178,17,15,6,'YOFI-17-15-6',0,0,0.00,1),(179,17,15,7,'YOFI-17-15-7',0,0,0.00,1),(180,17,15,8,'YOFI-17-15-8',0,0,0.00,1),(181,17,15,9,'YOFI-17-15-9',0,0,0.00,1),(182,17,15,10,'YOFI-17-15-10',0,0,0.00,1),(183,17,15,11,'YOFI-17-15-11',0,0,0.00,1),(184,17,15,12,'YOFI-17-15-12',10,1,0.00,1),(185,17,7,1,'YOFI-17-7-1',0,0,0.00,1),(186,17,7,2,'YOFI-17-7-2',0,0,0.00,1),(187,17,7,3,'YOFI-17-7-3',0,0,0.00,1),(188,17,7,4,'YOFI-17-7-4',0,0,0.00,1),(189,17,7,5,'YOFI-17-7-5',0,0,0.00,1),(190,17,7,6,'YOFI-17-7-6',0,0,0.00,1),(191,17,7,7,'YOFI-17-7-7',0,0,0.00,1),(192,17,7,8,'YOFI-17-7-8',0,0,0.00,1),(193,17,7,9,'YOFI-17-7-9',0,0,0.00,1),(194,17,7,10,'YOFI-17-7-10',0,0,0.00,1),(195,17,7,11,'YOFI-17-7-11',0,0,0.00,1),(197,19,10,1,'YOFI-19-10-1',5,0,0.00,1),(198,19,10,2,'YOFI-19-10-2',5,0,0.00,1),(199,19,10,3,'YOFI-19-10-3',0,0,0.00,1),(200,19,10,4,'YOFI-19-10-4',0,0,0.00,1),(201,19,10,5,'YOFI-19-10-5',0,0,0.00,1),(202,19,10,6,'YOFI-19-10-6',0,0,0.00,1),(203,19,10,7,'YOFI-19-10-7',0,0,0.00,1),(204,19,10,8,'YOFI-19-10-8',0,0,0.00,1),(205,19,10,9,'YOFI-19-10-9',0,0,0.00,1),(206,19,10,10,'YOFI-19-10-10',0,0,0.00,1),(207,19,10,11,'YOFI-19-10-11',0,0,0.00,1),(208,19,10,12,'YOFI-19-10-12',0,0,0.00,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_stock_log`
--

LOCK TABLES `tbl_stock_log` WRITE;
/*!40000 ALTER TABLE `tbl_stock_log` DISABLE KEYS */;
INSERT INTO `tbl_stock_log` VALUES (5,11,0,1,1,'reserva',13,NULL,'Reserva checkout SKU 153','2026-06-23 17:39:27'),(6,11,0,1,1,'reserva',13,NULL,'Reserva checkout SKU 154','2026-06-23 17:39:27'),(7,13,0,1,1,'reserva',13,NULL,'Reserva checkout SKU 155','2026-06-23 17:39:27'),(8,17,0,1,1,'reserva',14,NULL,'Reserva checkout SKU 184','2026-06-23 18:01:27'),(9,11,1,2,1,'reserva',15,NULL,'Reserva checkout SKU 153','2026-06-23 18:03:52'),(10,11,10,9,-1,'venta',15,'MercadoPago','Venta confirmada SKU 153','2026-06-23 18:05:27'),(13,11,1,0,-1,'liberacion_reserva',13,NULL,'Reserva expirada (30 min) SKU 153','2026-06-29 22:29:21'),(16,11,1,0,-1,'liberacion_reserva',13,NULL,'Reserva expirada (30 min) SKU 154','2026-06-29 22:29:21'),(19,13,1,0,-1,'liberacion_reserva',13,NULL,'Reserva expirada (30 min) SKU 155','2026-06-29 22:29:21'),(22,17,1,0,-1,'liberacion_reserva',14,NULL,'Reserva expirada (30 min) SKU 184','2026-06-29 22:29:21'),(25,11,0,3,3,'reserva',16,NULL,'Reserva checkout SKU 153','2026-07-01 00:39:27'),(26,11,0,1,1,'reserva',16,NULL,'Reserva checkout SKU 154','2026-07-01 00:39:27'),(27,13,0,1,1,'reserva',16,NULL,'Reserva checkout SKU 155','2026-07-01 00:39:27'),(28,11,3,4,1,'reserva',17,NULL,'Reserva checkout SKU 153','2026-07-01 00:43:06'),(29,11,4,1,-3,'liberacion_reserva',16,NULL,'Reserva expirada (30 min) SKU 153','2026-07-01 01:20:09'),(30,11,1,0,-1,'liberacion_reserva',16,NULL,'Reserva expirada (30 min) SKU 154','2026-07-01 01:20:09'),(31,13,1,0,-1,'liberacion_reserva',16,NULL,'Reserva expirada (30 min) SKU 155','2026-07-01 01:20:09'),(32,11,1,0,-1,'liberacion_reserva',17,NULL,'Reserva expirada (30 min) SKU 153','2026-07-01 01:20:09'),(33,11,0,2,2,'reserva',18,NULL,'Reserva checkout SKU 153','2026-07-01 01:27:58'),(34,11,2,4,2,'reserva',20,NULL,'Reserva checkout SKU 153','2026-07-01 03:30:15'),(35,7,0,1,1,'reserva',21,NULL,'Reserva checkout SKU 151','2026-07-02 23:38:32'),(36,17,0,1,1,'reserva',21,NULL,'Reserva checkout SKU 184','2026-07-02 23:38:32'),(37,17,1,2,1,'reserva',22,NULL,'Reserva checkout SKU 184','2026-07-02 23:56:41'),(38,7,1,0,-1,'liberacion_reserva',21,NULL,'Reserva expirada (30 min) SKU 151','2026-07-03 00:10:37'),(39,17,2,1,-1,'liberacion_reserva',21,NULL,'Reserva expirada (30 min) SKU 184','2026-07-03 00:10:37'),(40,16,0,1,1,'reserva',23,NULL,'Reserva checkout SKU 158','2026-07-03 00:10:37'),(41,17,1,0,-1,'liberacion_reserva',22,NULL,'Reserva expirada (30 min) SKU 184','2026-07-03 00:41:08'),(42,16,1,0,-1,'liberacion_reserva',23,NULL,'Reserva expirada (30 min) SKU 158','2026-07-03 00:41:08'),(43,17,0,1,1,'reserva',24,NULL,'Reserva checkout SKU 184','2026-07-03 00:41:08'),(44,17,1,2,1,'reserva',25,NULL,'Reserva checkout SKU 184','2026-07-03 00:48:53'),(45,16,0,1,1,'reserva',26,NULL,'Reserva checkout SKU 158','2026-07-03 00:57:29'),(46,17,2,1,-1,'liberacion_reserva',24,NULL,'Reserva expirada (30 min) SKU 184','2026-07-03 13:59:30'),(47,17,1,0,-1,'liberacion_reserva',25,NULL,'Reserva expirada (30 min) SKU 184','2026-07-03 13:59:30'),(48,16,1,0,-1,'liberacion_reserva',26,NULL,'Reserva expirada (30 min) SKU 158','2026-07-03 13:59:30'),(49,17,0,1,1,'reserva',27,NULL,'Reserva checkout SKU 184','2026-07-03 13:59:30'),(50,16,0,1,1,'reserva',28,NULL,'Reserva checkout SKU 158','2026-07-03 14:06:35');
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
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_usuarios`
--

LOCK TABLES `tbl_usuarios` WRITE;
/*!40000 ALTER TABLE `tbl_usuarios` DISABLE KEYS */;
INSERT INTO `tbl_usuarios` VALUES (2,'checkout-user-1782233599@yofi.local','$2y$10$D/9UcRxSDpFwZITxsQxMcOVi6tonXkIaQ7QHclH/UkRzH9NkXEGD2','Logueado','Test',NULL,NULL,NULL,NULL,NULL,NULL,1,0,1,NULL,'2026-06-23 13:53:19',NULL,NULL),(4,'gonzalo.mattia@hotmail.com','$2y$10$CtzGBUhU9NsC8jGqr39G.uDcro.mlupOniVrTR.16R7SSgyQDh3g2','gonzalo','mattia','01160436765',NULL,NULL,NULL,NULL,'31632308',1,0,1,NULL,'2026-06-23 14:04:29','2026-07-02 21:57:17','2026-07-02 21:57:17');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
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

-- Dump completed on 2026-07-03 13:40:38
