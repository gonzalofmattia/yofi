-- Yofi — Catálogo inicial (WhatsApp Business)
-- Ejecutar sobre la base `yofi` después de crear-db.sql
-- Modelo Carter's: 1 producto padre, múltiples colores vía tbl_skus + tbl_prod_imagenes

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

UPDATE tbl_productos SET publicado = 0, destacado = 0
WHERE codigo IN ('YF-001', 'YF-002', 'YF-003', 'YF-004', 'YF-005');

INSERT INTO tbl_talles (nombre, orden, activo)
SELECT 'Único', 99, 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM tbl_talles WHERE nombre = 'Único' LIMIT 1);

INSERT INTO tbl_colores (nombre, hex_code, activo)
SELECT 'Bordó', '#722F37', 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM tbl_colores WHERE nombre = 'Bordó' LIMIT 1);
INSERT INTO tbl_colores (nombre, hex_code, activo)
SELECT 'Gris', '#9E9E9E', 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM tbl_colores WHERE nombre = 'Gris' LIMIT 1);
INSERT INTO tbl_colores (nombre, hex_code, activo)
SELECT 'Azul', '#4A6FA5', 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM tbl_colores WHERE nombre = 'Azul' LIMIT 1);
INSERT INTO tbl_colores (nombre, hex_code, activo)
SELECT 'Natural', '#E8DCC8', 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM tbl_colores WHERE nombre = 'Natural' LIMIT 1);
INSERT INTO tbl_colores (nombre, hex_code, activo)
SELECT 'Verde antiguo', '#6B7F5E', 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM tbl_colores WHERE nombre = 'Verde antiguo' LIMIT 1);
INSERT INTO tbl_colores (nombre, hex_code, activo)
SELECT 'Chocolate', '#5C4033', 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM tbl_colores WHERE nombre = 'Chocolate' LIMIT 1);
INSERT INTO tbl_colores (nombre, hex_code, activo)
SELECT 'Gris topo', '#8B8680', 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM tbl_colores WHERE nombre = 'Gris topo' LIMIT 1);

INSERT INTO tbl_categorias (nombre, slug, descripcion, orden, publicado)
SELECT 'Mini Ánima Invierno', 'mini-anima-invierno', 'Accesorios tejidos de invierno Mini Ánima', 8, 1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM tbl_categorias WHERE slug = 'mini-anima-invierno' LIMIT 1);

INSERT INTO tbl_categorias (nombre, slug, descripcion, orden, publicado)
SELECT 'Regalos', 'regalos', 'Regalos y productos especiales', 9, 1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM tbl_categorias WHERE slug = 'regalos' LIMIT 1);

SET @cate_mini = (SELECT id_cate FROM tbl_categorias WHERE slug = 'mini-anima-invierno' LIMIT 1);
SET @cate_regalos = (SELECT id_cate FROM tbl_categorias WHERE slug = 'regalos' LIMIT 1);
SET @talle_unico = (SELECT id_talle FROM tbl_talles WHERE nombre = 'Único' LIMIT 1);
SET @color_bordo = (SELECT id_color FROM tbl_colores WHERE nombre = 'Bordó' LIMIT 1);
SET @color_gris = (SELECT id_color FROM tbl_colores WHERE nombre = 'Gris' LIMIT 1);
SET @color_azul = (SELECT id_color FROM tbl_colores WHERE nombre = 'Azul' LIMIT 1);
SET @color_rosa = (SELECT id_color FROM tbl_colores WHERE nombre = 'Rosa' LIMIT 1);
SET @color_natural = (SELECT id_color FROM tbl_colores WHERE nombre = 'Natural' LIMIT 1);
SET @color_verde_ant = (SELECT id_color FROM tbl_colores WHERE nombre = 'Verde antiguo' LIMIT 1);
SET @color_chocolate = (SELECT id_color FROM tbl_colores WHERE nombre = 'Chocolate' LIMIT 1);
SET @color_gris_topo = (SELECT id_color FROM tbl_colores WHERE nombre = 'Gris topo' LIMIT 1);
SET @color_beige = (SELECT id_color FROM tbl_colores WHERE nombre = 'Beige' LIMIT 1);
SET @placeholder = 'placeholder.jpg';

DELETE s FROM tbl_skus s
INNER JOIN tbl_productos p ON p.id_prod = s.id_prod
WHERE p.codigo LIKE 'YF-MINI-%' OR p.codigo LIKE 'YF-REG-%';

DELETE i FROM tbl_prod_imagenes i
INNER JOIN tbl_productos p ON p.id_prod = i.id_prod
WHERE p.codigo LIKE 'YF-MINI-%' OR p.codigo LIKE 'YF-REG-%';

DELETE FROM tbl_productos
WHERE codigo LIKE 'YF-MINI-%' OR codigo LIKE 'YF-REG-%';

-- Infinito tejido — 4 colores
INSERT INTO tbl_productos (id_cate, nombre, slug, codigo, precio_base, descripcion, publicado)
VALUES (@cate_mini, 'Infinito tejido', 'infinito-tejido', 'YF-MINI-INF', 15000.00,
  'Fabricados en Acrílico Premium: Elegimos los mejores i...', 1);
SET @prod = LAST_INSERT_ID();
INSERT INTO tbl_prod_imagenes (id_prod, id_color, path, orden, es_principal) VALUES
  (@prod, @color_bordo, @placeholder, 1, 1),
  (@prod, @color_gris, @placeholder, 1, 1),
  (@prod, @color_azul, @placeholder, 1, 1),
  (@prod, @color_rosa, @placeholder, 1, 1);
INSERT INTO tbl_skus (id_prod, id_color, id_talle, codigo_sku, stock) VALUES
  (@prod, @color_bordo, @talle_unico, 'YF-MINI-INF-BOR', 0),
  (@prod, @color_gris, @talle_unico, 'YF-MINI-INF-GRI', 0),
  (@prod, @color_azul, @talle_unico, 'YF-MINI-INF-AZU', 0),
  (@prod, @color_rosa, @talle_unico, 'YF-MINI-INF-ROS', 0);

-- Bandana tejida — 2 colores
INSERT INTO tbl_productos (id_cate, nombre, slug, codigo, precio_base, descripcion, publicado)
VALUES (@cate_mini, 'Bandana tejida', 'bandana-tejida', 'YF-MINI-BAN', 5000.00,
  'Nuestros nuevos y exclusivos cuellitos tipo bandana teji...', 1);
SET @prod = LAST_INSERT_ID();
INSERT INTO tbl_prod_imagenes (id_prod, id_color, path, orden, es_principal) VALUES
  (@prod, @color_chocolate, @placeholder, 1, 1),
  (@prod, @color_gris_topo, @placeholder, 1, 1);
INSERT INTO tbl_skus (id_prod, id_color, id_talle, codigo_sku, stock) VALUES
  (@prod, @color_chocolate, @talle_unico, 'YF-MINI-BAN-CHO', 0),
  (@prod, @color_gris_topo, @talle_unico, 'YF-MINI-BAN-GTO', 0);

-- Gorro aspen — 3 colores
INSERT INTO tbl_productos (id_cate, nombre, slug, codigo, precio_base, descripcion, publicado)
VALUES (@cate_mini, 'Gorro aspen', 'gorro-aspen', 'YF-MINI-ASP', 15590.00,
  'Fabricados en Acrílico Premium: Elegimos los mejores i...', 1);
SET @prod = LAST_INSERT_ID();
INSERT INTO tbl_prod_imagenes (id_prod, id_color, path, orden, es_principal) VALUES
  (@prod, @color_natural, @placeholder, 1, 1),
  (@prod, @color_verde_ant, @placeholder, 1, 1),
  (@prod, @color_rosa, @placeholder, 1, 1);
INSERT INTO tbl_skus (id_prod, id_color, id_talle, codigo_sku, stock) VALUES
  (@prod, @color_natural, @talle_unico, 'YF-MINI-ASP-NAT', 0),
  (@prod, @color_verde_ant, @talle_unico, 'YF-MINI-ASP-VER', 0),
  (@prod, @color_rosa, @talle_unico, 'YF-MINI-ASP-ROS', 0);

-- Gorro pompón bordó — color único
INSERT INTO tbl_productos (id_cate, nombre, slug, codigo, precio_base, descripcion, publicado)
VALUES (@cate_mini, 'Gorro pompón bordó', 'gorro-pompon-bordo', 'YF-MINI-010', 15590.00,
  '- Fabricados en Acrílico Premium: Elegimos los mejores...', 1);
SET @prod = LAST_INSERT_ID();
INSERT INTO tbl_prod_imagenes (id_prod, id_color, path, orden, es_principal)
VALUES (@prod, @color_bordo, @placeholder, 1, 1);
INSERT INTO tbl_skus (id_prod, id_color, id_talle, codigo_sku, stock)
VALUES (@prod, @color_bordo, @talle_unico, 'YF-MINI-010-UNI', 0);

-- Delantal vintage sin bordar
INSERT INTO tbl_productos (id_cate, nombre, slug, codigo, precio_base, descripcion, publicado)
VALUES (@cate_regalos, 'Delantal vintage sin bordar', 'delantal-vintage-sin-bordar', 'YF-REG-001', 40000.00,
  'Delantal estilo romantico Fabricado artesanalmente, edic...', 1);
SET @prod = LAST_INSERT_ID();
INSERT INTO tbl_prod_imagenes (id_prod, id_color, path, orden, es_principal)
VALUES (@prod, @color_beige, @placeholder, 1, 1);
INSERT INTO tbl_skus (id_prod, id_color, id_talle, codigo_sku, stock)
VALUES (@prod, @color_beige, @talle_unico, 'YF-REG-001-UNI', 0);

-- Bordado a pedido
INSERT INTO tbl_productos (id_cate, nombre, slug, codigo, precio_base, descripcion, publicado)
VALUES (@cate_regalos, 'Bordado a pedido', 'bordado-a-pedido', 'YF-REG-002', 8000.00, NULL, 1);
SET @prod = LAST_INSERT_ID();
INSERT INTO tbl_prod_imagenes (id_prod, id_color, path, orden, es_principal)
VALUES (@prod, @color_beige, @placeholder, 1, 1);
INSERT INTO tbl_skus (id_prod, id_color, id_talle, codigo_sku, stock)
VALUES (@prod, @color_beige, @talle_unico, 'YF-REG-002-UNI', 0);

SET FOREIGN_KEY_CHECKS = 1;

-- 2026-06-22 — Seed catálogo inicial Yofi (modelo 1 producto + variantes de color)
