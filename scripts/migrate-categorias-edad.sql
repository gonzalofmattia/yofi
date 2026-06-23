-- Yofi — Reemplazar categorías de edad por categorías de producto + filtros de edad vía talles
-- Ejecutar sobre una base existente con el schema inicial (mini/ninas/ninos/ofertas como categorías)

SET NAMES utf8mb4;

DELETE FROM tbl_categorias WHERE slug = 'ofertas';

UPDATE tbl_categorias SET
  nombre = 'Abrigos',
  slug = 'abrigos',
  descripcion = 'Abrigos, camperas y abrigos',
  imagen = NULL,
  orden = 1
WHERE slug IN ('mini', 'abrigos');

UPDATE tbl_categorias SET
  nombre = 'Buzos y Cardigans',
  slug = 'buzos',
  descripcion = 'Buzos, sweaters y cardigans',
  imagen = NULL,
  orden = 2
WHERE slug IN ('ninas', 'buzos');

UPDATE tbl_categorias SET
  nombre = 'Pantalones',
  slug = 'pantalones',
  descripcion = 'Pantalones y joggers',
  imagen = NULL,
  orden = 3
WHERE slug IN ('ninos', 'pantalones');

INSERT INTO tbl_categorias (nombre, slug, descripcion, orden, publicado)
SELECT 'Remeras', 'remeras', 'Remeras y tops', 4, 1
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM tbl_categorias WHERE slug = 'remeras');

INSERT INTO tbl_categorias (nombre, slug, descripcion, orden, publicado)
SELECT 'Vestidos', 'vestidos', 'Vestidos y polleras', 5, 1
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM tbl_categorias WHERE slug = 'vestidos');

UPDATE tbl_categorias SET orden = 6 WHERE slug = 'accesorios';
UPDATE tbl_categorias SET orden = 7 WHERE slug = 'calzado';

UPDATE tbl_productos p
INNER JOIN tbl_categorias c ON c.slug = 'vestidos'
SET p.id_cate = c.id_cate
WHERE p.id_prod = 1;

UPDATE tbl_productos p
INNER JOIN tbl_categorias c ON c.slug = 'abrigos'
SET p.id_cate = c.id_cate
WHERE p.id_prod IN (2, 5);

UPDATE tbl_productos p
INNER JOIN tbl_categorias c ON c.slug = 'buzos'
SET p.id_cate = c.id_cate
WHERE p.id_prod = 3;

UPDATE tbl_productos p
INNER JOIN tbl_categorias c ON c.slug = 'pantalones'
SET p.id_cate = c.id_cate
WHERE p.id_prod = 4;

UPDATE tbl_categorias SET imagen = 'subcat-abrigos.jpg' WHERE slug = 'abrigos' AND (imagen IS NULL OR imagen = '' OR imagen LIKE 'cat-%');
UPDATE tbl_categorias SET imagen = 'subcat-buzos.jpg' WHERE slug = 'buzos' AND (imagen IS NULL OR imagen = '' OR imagen LIKE 'cat-%');
UPDATE tbl_categorias SET imagen = 'subcat-pantalones.jpg' WHERE slug = 'pantalones' AND (imagen IS NULL OR imagen = '' OR imagen LIKE 'cat-%');
UPDATE tbl_categorias SET imagen = 'subcat-remeras.jpg' WHERE slug = 'remeras' AND (imagen IS NULL OR imagen = '');
UPDATE tbl_categorias SET imagen = 'subcat-vestidos.jpg' WHERE slug = 'vestidos' AND (imagen IS NULL OR imagen = '');
