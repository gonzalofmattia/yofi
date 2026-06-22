<?php
/** @var array<string,mixed>|null $producto */
/** @var string $error_message */
/** @var mysqli_result|false $categorias */
/** @var bool $isDrawer */
/** @var int|null $id_prod */
/** @var array $coloresProducto */
/** @var array $coloresDisponibles */
/** @var array $tallesTodos */

$isEdit = $producto !== null;
$isDrawer = $isDrawer ?? false;
$formAction = $isEdit
    ? 'e_producto.php?id=' . (int)$producto['id_prod'] . ($isDrawer ? '&partial=1' : '')
    : 'a_producto.php' . ($isDrawer ? '?partial=1' : '');
?>
<div class="product-editor-root" data-mode="<?= $isEdit ? 'edit' : 'create' ?>" data-id-prod="<?= $isEdit ? (int)$producto['id_prod'] : 0 ?>">
    <form method="post" id="product-main-form" action="<?= htmlspecialchars($formAction, ENT_QUOTES, 'UTF-8') ?>">
        <?= admin_csrf_field() ?>
        <input type="hidden" name="envio" value="1">
        <?php include __DIR__ . '/_form_producto.php'; ?>
    </form>

    <?php if ($isEdit && isset($id_prod) && $id_prod > 0): ?>
        <?php include __DIR__ . '/_producto_colores.php'; ?>
    <?php elseif (!$isEdit): ?>
        <div class="editor-card">
            <div class="editor-card-body">
                <p class="text-muted small mb-0">Guardá el producto para cargar colores, fotos y stock por talle.</p>
            </div>
        </div>
    <?php endif; ?>
</div>
