<?php
/** @var mysqli $con */
/** @var string $error_message */
/** @var mysqli_result|false $categorias */
/** @var array<string,mixed>|null $producto */
$producto = $producto ?? null;
$isEdit = $producto !== null;
$action = $isEdit ? 'e_producto.php?id=' . (int)$producto['id_prod'] : 'a_producto.php';
?>
<?php if ($error_message): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>">
    <?= admin_csrf_field() ?>
    <input type="hidden" name="envio" value="1">

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header card-header-yofi"><strong>Datos del producto</strong></div>
        <div class="card-body row g-3">
            <div class="col-md-8">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($producto['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Código</label>
                <input type="text" name="codigo" class="form-control" required value="<?= htmlspecialchars($producto['codigo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Categoría</label>
                <select name="id_cate" class="form-select" required>
                    <option value="">Seleccionar...</option>
                    <?php if ($categorias): while ($cat = mysqli_fetch_assoc($categorias)): ?>
                        <option value="<?= (int)$cat['id_cate'] ?>" <?= ((int)($producto['id_cate'] ?? 0) === (int)$cat['id_cate']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nombre'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endwhile; endif; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Precio base</label>
                <input type="number" step="0.01" name="precio_base" class="form-control" required value="<?= htmlspecialchars((string)($producto['precio_base'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Precio oferta</label>
                <input type="number" step="0.01" name="precio_oferta" class="form-control" value="<?= htmlspecialchars((string)($producto['precio_oferta'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="4"><?= htmlspecialchars($producto['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Composición</label>
                <textarea name="composicion" class="form-control" rows="3"><?= htmlspecialchars($producto['composicion'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Cuidados</label>
                <textarea name="cuidados" class="form-control" rows="3"><?= htmlspecialchars($producto['cuidados'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="col-md-3"><label class="form-label">Peso (kg)</label><input type="number" step="0.01" name="peso" class="form-control" value="<?= htmlspecialchars((string)($producto['peso'] ?? '0.5'), ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="col-md-3"><label class="form-label">Alto (cm)</label><input type="number" step="0.01" name="alto" class="form-control" value="<?= htmlspecialchars((string)($producto['alto'] ?? '20'), ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="col-md-3"><label class="form-label">Ancho (cm)</label><input type="number" step="0.01" name="ancho" class="form-control" value="<?= htmlspecialchars((string)($producto['ancho'] ?? '20'), ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="col-md-3"><label class="form-label">Profundidad (cm)</label><input type="number" step="0.01" name="profundidad" class="form-control" value="<?= htmlspecialchars((string)($producto['profundidad'] ?? '5'), ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="col-md-3 form-check"><input class="form-check-input" type="checkbox" name="publicado" id="publicado" <?= ((int)($producto['publicado'] ?? 1) === 1) ? 'checked' : '' ?>><label class="form-check-label" for="publicado">Publicado</label></div>
            <div class="col-md-3 form-check"><input class="form-check-input" type="checkbox" name="destacado" id="destacado" <?= ((int)($producto['destacado'] ?? 0) === 1) ? 'checked' : '' ?>><label class="form-check-label" for="destacado">Destacado</label></div>
            <div class="col-md-3 form-check"><input class="form-check-input" type="checkbox" name="oferta" id="oferta" <?= ((int)($producto['oferta'] ?? 0) === 1) ? 'checked' : '' ?>><label class="form-check-label" for="oferta">Oferta</label></div>
            <div class="col-md-3">
                <label class="form-label">Promo badge</label>
                <input type="text" name="promo_badge" class="form-control" value="<?= htmlspecialchars($producto['promo_badge'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <?php if (!$isEdit): ?>
            <div class="col-12">
                <p class="text-muted small mb-0">Después de crear el producto podés cargar colores, fotos y stock por talle.</p>
            </div>
            <?php endif; ?>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-yofi"><?= $isEdit ? 'Guardar cambios' : 'Crear producto' ?></button>
            <a href="listado.php" class="btn btn-outline-secondary">Volver</a>
        </div>
    </div>
</form>
