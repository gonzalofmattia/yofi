<?php
/** @var int $id_prod */
/** @var array<int,array<string,mixed>> $coloresProducto */
/** @var array<int,array<string,mixed>> $coloresDisponibles */
/** @var array<int,array<string,mixed>> $tallesTodos */
?>
<div class="editor-card" id="product-colors-section" data-id-prod="<?= (int)$id_prod ?>">
    <div class="editor-card-title d-flex justify-content-between align-items-center">
        <span>Colores y variantes</span>
        <?php if (!empty($coloresDisponibles)): ?>
        <button type="button" class="color-add-btn" id="btnToggleNuevoColor"><i class="bi bi-plus"></i> Agregar color</button>
        <?php endif; ?>
    </div>
    <div class="editor-card-body">
        <p class="text-muted small mb-3">Cada color es una entrada del catálogo con fotos y stock por talle.</p>

        <?php if (!empty($coloresDisponibles)): ?>
        <div class="color-new-panel d-none" id="panelNuevoColor">
            <h6 class="fw-bold mb-3">Nuevo color</h6>
                        <form method="post" enctype="multipart/form-data" action="<?= app_path('admin/api/upload-imagen-producto.php') ?>" class="color-variant-form">
                            <?= admin_csrf_field() ?>
                            <input type="hidden" name="id_prod" value="<?= (int)$id_prod ?>">
                            <input type="hidden" name="redirect" value="listado.php?editar=<?= (int)$id_prod ?>">
                <div class="row g-3 mb-3">
                    <div class="col-md-5">
                        <label class="form-label">Color</label>
                        <select name="id_color" class="form-select color-select" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach ($coloresDisponibles as $c): ?>
                            <option value="<?= (int)$c['id_color'] ?>"><?= htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label">Fotos</label>
                        <input type="file" name="imagenes[]" class="form-control" accept="image/*" multiple>
                    </div>
                </div>
                <label class="form-label fw-semibold">Stock inicial por talle</label>
                <div class="row g-2 stock-grid mb-3">
                    <?php foreach ($tallesTodos as $t): ?>
                    <div class="col-4 col-md-3 col-lg-2">
                        <label class="form-label small mb-1"><?= htmlspecialchars($t['nombre'], ENT_QUOTES, 'UTF-8') ?></label>
                        <input type="number" class="form-control form-control-sm stock-input" data-id-talle="<?= (int)$t['id_talle'] ?>" value="0" min="0">
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn btn-ink btn-sm btn-guardar-color">Confirmar color</button>
            </form>
        </div>
        <?php endif; ?>

        <?php if (empty($coloresProducto)): ?>
        <div class="alert alert-light border mb-0">Todavía no hay colores. Usá «Agregar color» para crear la primera variante.</div>
        <?php else: ?>
        <div class="color-swatches" role="tablist">
            <?php $first = true; foreach ($coloresProducto as $colorData): $idColor = (int)$colorData['id_color']; ?>
            <button type="button" class="color-swatch-btn<?= $first ? ' active' : '' ?>" data-color-panel="<?= $idColor ?>" role="tab">
                <span class="color-swatch-dot" style="background:<?= htmlspecialchars($colorData['hex_code'], ENT_QUOTES, 'UTF-8') ?>"></span>
                <?= htmlspecialchars($colorData['nombre'], ENT_QUOTES, 'UTF-8') ?>
            </button>
            <?php $first = false; endforeach; ?>
        </div>

        <?php $first = true; foreach ($coloresProducto as $colorData): $idColor = (int)$colorData['id_color']; ?>
        <div class="color-panel<?= $first ? ' active' : '' ?>" id="colorPanel<?= $idColor ?>" data-id-color="<?= $idColor ?>">
            <form method="post" enctype="multipart/form-data" action="<?= app_path('admin/api/upload-imagen-producto.php') ?>" class="mb-3 color-upload-form">
                <?= admin_csrf_field() ?>
                <input type="hidden" name="id_prod" value="<?= (int)$id_prod ?>">
                <input type="hidden" name="id_color" value="<?= $idColor ?>">
                <input type="hidden" name="redirect" value="listado.php?editar=<?= (int)$id_prod ?>">
                <div class="row g-2 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label small">Agregar fotos</label>
                        <input type="file" name="imagenes[]" class="form-control form-control-sm" accept="image/*" multiple>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-outline-ink btn-sm w-100">Subir</button>
                    </div>
                </div>
            </form>

            <?php if (!empty($colorData['imagenes'])): ?>
            <div class="img-grid-editor mb-3">
                <?php foreach ($colorData['imagenes'] as $img): ?>
                <div class="img-item" data-imagen-id="<?= (int)$img['id_imagen'] ?>">
                    <img src="<?= imgprod_path($img['path']) ?>" alt="">
                    <?php if ((int)$img['es_principal'] === 1): ?>
                    <span class="badge bg-success position-absolute top-0 start-0 m-1" style="font-size:0.65rem">Principal</span>
                    <?php else: ?>
                    <button type="button" class="btn btn-sm btn-light position-absolute top-0 start-0 m-1 btn-principal py-0 px-1" data-id-imagen="<?= (int)$img['id_imagen'] ?>" title="Marcar principal">★</button>
                    <?php endif; ?>
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 btn-eliminar-imagen py-0 px-1" data-id-imagen="<?= (int)$img['id_imagen'] ?>" title="Eliminar">×</button>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-muted small">Sin fotos para este color.</p>
            <?php endif; ?>

            <label class="form-label fw-semibold small">Stock por talle</label>
            <div class="row g-2 mb-2 stock-grid">
                <?php foreach ($tallesTodos as $t):
                    $idTalle = (int)$t['id_talle'];
                    $stockVal = $colorData['skus'][$idTalle]['stock'] ?? 0;
                ?>
                <div class="col-4 col-md-3 col-lg-2">
                    <label class="form-label small mb-1"><?= htmlspecialchars($t['nombre'], ENT_QUOTES, 'UTF-8') ?></label>
                    <input type="number" class="form-control form-control-sm stock-input" data-id-talle="<?= $idTalle ?>" value="<?= (int)$stockVal ?>" min="0">
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn btn-ink btn-sm btn-guardar-stock" data-id-color="<?= $idColor ?>">Guardar stock</button>
        </div>
        <?php $first = false; endforeach; ?>
        <?php endif; ?>
    </div>
</div>
