<?php
/** @var mysqli $con */
/** @var string $error_message */
/** @var mysqli_result|false $categorias */
/** @var array<string,mixed>|null $producto */
/** @var bool $isDrawer */
$producto = $producto ?? null;
$isEdit = $producto !== null;
$isDrawer = $isDrawer ?? false;
$formId = $isDrawer ? 'product-main-form' : 'product-main-form';
$precioBase = (float)($producto['precio_base'] ?? 0);
$precioOferta = isset($producto['precio_oferta']) && $producto['precio_oferta'] !== '' && $producto['precio_oferta'] !== null
    ? (float)$producto['precio_oferta']
    : 0;
$offPct = ($precioBase > 0 && $precioOferta > 0 && $precioOferta < $precioBase)
    ? (int)round((1 - $precioOferta / $precioBase) * 100)
    : 0;
?>
<?php if (!empty($error_message)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<div class="editor-card">
    <div class="editor-card-title">General</div>
    <div class="editor-card-body row g-3">
        <div class="col-12">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($producto['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Código</label>
            <input type="text" name="codigo" class="form-control" required value="<?= htmlspecialchars($producto['codigo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
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
    </div>
</div>

<div class="editor-card">
    <div class="editor-card-title">Precios</div>
    <div class="editor-card-body row g-3">
        <div class="col-md-6">
            <label class="form-label">Precio base</label>
            <input type="number" step="0.01" name="precio_base" id="precio_base" class="form-control" required value="<?= htmlspecialchars((string)($producto['precio_base'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Precio oferta</label>
            <div class="d-flex align-items-center gap-2">
                <input type="number" step="0.01" name="precio_oferta" id="precio_oferta" class="form-control" value="<?= htmlspecialchars((string)($producto['precio_oferta'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <span class="precio-off-badge<?= $offPct > 0 ? '' : ' d-none' ?>" id="precio-off-badge">-<?= $offPct ?>%</span>
            </div>
        </div>
    </div>
</div>

<div class="editor-card">
    <div class="editor-card-title">Organización</div>
    <div class="editor-card-body row g-3">
        <div class="col-md-6">
            <label class="form-label">Categoría</label>
            <select name="id_cate" class="form-select" required>
                <option value="">Seleccionar...</option>
                <?php if ($categorias): mysqli_data_seek($categorias, 0); while ($cat = mysqli_fetch_assoc($categorias)): ?>
                    <option value="<?= (int)$cat['id_cate'] ?>" <?= ((int)($producto['id_cate'] ?? 0) === (int)$cat['id_cate']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nombre'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endwhile; endif; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Promo badge</label>
            <input type="text" name="promo_badge" class="form-control" value="<?= htmlspecialchars($producto['promo_badge'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Ej: 3x2">
        </div>
        <div class="col-md-4">
            <div class="form-check form-switch form-switch-yofi">
                <input class="form-check-input" type="checkbox" name="publicado" id="publicado" <?= ((int)($producto['publicado'] ?? 1) === 1) ? 'checked' : '' ?>>
                <label class="form-check-label fw-semibold" for="publicado">Publicado</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-check form-switch form-switch-yofi">
                <input class="form-check-input" type="checkbox" name="destacado" id="destacado" <?= ((int)($producto['destacado'] ?? 0) === 1) ? 'checked' : '' ?>>
                <label class="form-check-label fw-semibold" for="destacado">Destacado</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-check form-switch form-switch-yofi">
                <input class="form-check-input" type="checkbox" name="oferta" id="oferta" <?= ((int)($producto['oferta'] ?? 0) === 1) ? 'checked' : '' ?>>
                <label class="form-check-label fw-semibold" for="oferta">Oferta</label>
            </div>
        </div>
    </div>
</div>

<div class="editor-card">
    <div class="editor-card-title">Logística</div>
    <div class="editor-card-body row g-3">
        <div class="col-6 col-md-3"><label class="form-label">Peso (kg)</label><input type="number" step="0.01" name="peso" class="form-control" value="<?= htmlspecialchars((string)($producto['peso'] ?? '0.5'), ENT_QUOTES, 'UTF-8') ?>"></div>
        <div class="col-6 col-md-3"><label class="form-label">Alto (cm)</label><input type="number" step="0.01" name="alto" class="form-control" value="<?= htmlspecialchars((string)($producto['alto'] ?? '20'), ENT_QUOTES, 'UTF-8') ?>"></div>
        <div class="col-6 col-md-3"><label class="form-label">Ancho (cm)</label><input type="number" step="0.01" name="ancho" class="form-control" value="<?= htmlspecialchars((string)($producto['ancho'] ?? '20'), ENT_QUOTES, 'UTF-8') ?>"></div>
        <div class="col-6 col-md-3"><label class="form-label">Profundidad (cm)</label><input type="number" step="0.01" name="profundidad" class="form-control" value="<?= htmlspecialchars((string)($producto['profundidad'] ?? '5'), ENT_QUOTES, 'UTF-8') ?>"></div>
    </div>
</div>

<script>
(function () {
    var base = document.getElementById('precio_base');
    var oferta = document.getElementById('precio_oferta');
    var badge = document.getElementById('precio-off-badge');
    if (!base || !oferta || !badge) return;
    function updateOff() {
        var p = parseFloat(base.value) || 0;
        var o = parseFloat(oferta.value) || 0;
        if (p > 0 && o > 0 && o < p) {
            badge.textContent = '-' + Math.round((1 - o / p) * 100) + '%';
            badge.classList.remove('d-none');
        } else {
            badge.classList.add('d-none');
        }
    }
    base.addEventListener('input', updateOff);
    oferta.addEventListener('input', updateOff);
})();
</script>
