-- Yofi — Datos de demostración (productos + imágenes + SKUs)
-- Ejecutar sobre la base `yofi` después de crear-db.sql

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM tbl_skus WHERE id_prod BETWEEN 1 AND 5;
DELETE FROM tbl_prod_imagenes WHERE id_prod BETWEEN 1 AND 5;
DELETE FROM tbl_productos WHERE id_prod BETWEEN 1 AND 5;

UPDATE tbl_categorias SET imagen = 'cat-mini.jpg' WHERE slug = 'mini';
UPDATE tbl_categorias SET imagen = 'cat-ninas.jpg' WHERE slug = 'ninas';
UPDATE tbl_categorias SET imagen = 'cat-ninos.jpg' WHERE slug = 'ninos';
UPDATE tbl_categorias SET imagen = 'cat-accesorios.jpg' WHERE slug = 'accesorios';
UPDATE tbl_categorias SET imagen = 'cat-calzado.jpg' WHERE slug = 'calzado';

-- Producto 1
INSERT INTO tbl_productos (id_prod, id_cate, nombre, slug, codigo, precio_base,
  precio_oferta, descripcion, peso, alto, ancho, profundidad,
  publicado, destacado)
VALUES (1, 2, 'Vestido Floral Romántico', 'vestido-floral-romantico',
  'YF-001', 18500, 14900,
  'Vestido de algodón importado con estampado floral.',
  0.30, 40, 35, 2, 1, 1);

INSERT INTO tbl_prod_imagenes (id_prod, id_color, path, orden, es_principal)
VALUES
  (1, 3, 'prod-001-rosa-1.jpg', 1, 1),
  (1, 4, 'prod-001-celeste-1.jpg', 1, 0);

INSERT INTO tbl_skus (id_prod, id_color, id_talle, codigo_sku, stock)
VALUES
  (1, 3, 5, 'YF-001-ROSA-2A', 8),
  (1, 3, 7, 'YF-001-ROSA-4A', 5),
  (1, 3, 8, 'YF-001-ROSA-6A', 3),
  (1, 4, 5, 'YF-001-CEL-2A',  6),
  (1, 4, 7, 'YF-001-CEL-4A',  0);

-- Producto 2
INSERT INTO tbl_productos (id_prod, id_cate, nombre, slug, codigo, precio_base,
  descripcion, peso, alto, ancho, profundidad, publicado, destacado)
VALUES (2, 1, 'Mameluco Tejido Bebé', 'mameluco-tejido-bebe',
  'YF-002', 12900,
  'Mameluco tejido importado, suave al tacto.',
  0.20, 35, 30, 2, 1, 1);

INSERT INTO tbl_prod_imagenes (id_prod, id_color, path, orden, es_principal)
VALUES (2, 3, 'prod-002-rosa-1.jpg', 1, 1);

INSERT INTO tbl_skus (id_prod, id_color, id_talle, codigo_sku, stock)
VALUES
  (2, 3, 1, 'YF-002-ROSA-03M', 10),
  (2, 3, 2, 'YF-002-ROSA-36M', 7),
  (2, 3, 3, 'YF-002-ROSA-612M', 4);

-- Producto 3
INSERT INTO tbl_productos (id_prod, id_cate, nombre, slug, codigo, precio_base,
  descripcion, peso, alto, ancho, profundidad, publicado, destacado)
VALUES (3, 3, 'Buzo Canguro Niño', 'buzo-canguro-nino',
  'YF-003', 15900,
  'Buzo canguro de algodón importado.',
  0.35, 45, 40, 3, 1, 1);

INSERT INTO tbl_prod_imagenes (id_prod, id_color, path, orden, es_principal)
VALUES (3, 7, 'prod-003-beige-1.jpg', 1, 1);

INSERT INTO tbl_skus (id_prod, id_color, id_talle, codigo_sku, stock)
VALUES
  (3, 7, 7, 'YF-003-BEI-4A', 5),
  (3, 7, 8, 'YF-003-BEI-6A', 8),
  (3, 7, 9, 'YF-003-BEI-8A', 3);

-- Producto 4
INSERT INTO tbl_productos (id_prod, id_cate, nombre, slug, codigo, precio_base,
  precio_oferta, descripcion, peso, alto, ancho, profundidad,
  publicado, destacado, oferta, promo_badge)
VALUES (4, 2, 'Conjunto Primavera', 'conjunto-primavera',
  'YF-004', 22000, 17500,
  'Conjunto de dos piezas, top y pantalón.',
  0.40, 45, 38, 3, 1, 1, 1, '3x2');

INSERT INTO tbl_prod_imagenes (id_prod, id_color, path, orden, es_principal)
VALUES (4, 1, 'prod-004-blanco-1.jpg', 1, 1);

INSERT INTO tbl_skus (id_prod, id_color, id_talle, codigo_sku, stock)
VALUES
  (4, 1, 5, 'YF-004-BLA-2A', 6),
  (4, 1, 7, 'YF-004-BLA-4A', 4);

-- Producto 5
INSERT INTO tbl_productos (id_prod, id_cate, nombre, slug, codigo, precio_base,
  descripcion, peso, alto, ancho, profundidad, publicado, destacado)
VALUES (5, 2, 'Campera Acolchada Niña', 'campera-acolchada-nina',
  'YF-005', 28500,
  'Campera acolchada importada, abrigada y liviana.',
  0.50, 48, 42, 5, 1, 1);

INSERT INTO tbl_prod_imagenes (id_prod, id_color, path, orden, es_principal)
VALUES (5, 6, 'prod-005-verde-1.jpg', 1, 1);

INSERT INTO tbl_skus (id_prod, id_color, id_talle, codigo_sku, stock)
VALUES
  (5, 6, 7, 'YF-005-VER-4A', 3),
  (5, 6, 8, 'YF-005-VER-6A', 5),
  (5, 6, 9, 'YF-005-VER-8A', 2);

SET FOREIGN_KEY_CHECKS = 1;

-- 2026-06-16 — Seed demo Yofi v1.0
