<?php
/**
 * Tarjeta de producto — recibe $producto con:
 * id_prod, id_color, nombre, slug, precio_base, precio_oferta, imagen_principal,
 * color_nombre, hex_code, talles[], promo_badge
 * Opcional: id_sku_default, sku_talle_nombre, sku_precio
 */

if (!isset($producto) || !is_array($producto)) {
    return;
}

require_once __DIR__ . '/../src/php/products.php';

$idProd = (int)($producto['id_prod'] ?? 0);
$idColor = (int)($producto['id_color'] ?? 0);
$nombre = htmlspecialchars((string)($producto['nombre'] ?? ''), ENT_QUOTES, 'UTF-8');
$slug = (string)($producto['slug'] ?? '');
$precioBase = (float)($producto['precio_base'] ?? 0);
$precioOferta = isset($producto['precio_oferta']) && $producto['precio_oferta'] !== null && $producto['precio_oferta'] !== ''
    ? (float)$producto['precio_oferta']
    : null;
$tieneOferta = $precioOferta !== null && $precioOferta > 0 && $precioOferta < $precioBase;
$precioMostrar = $tieneOferta ? $precioOferta : $precioBase;
$imagen = htmlspecialchars((string)($producto['imagen_principal'] ?? imgprod_path('placeholder.jpg')), ENT_QUOTES, 'UTF-8');
$badge = !empty($producto['promo_badge']) ? htmlspecialchars((string)$producto['promo_badge'], ENT_QUOTES, 'UTF-8') : '';
$detailUrl = product_url($slug, $idColor > 0 ? $idColor : null);

$talles = [];
if (!empty($producto['talles']) && is_array($producto['talles'])) {
    $talles = $producto['talles'];
}

$idSku = !empty($producto['id_sku_default']) ? (int)$producto['id_sku_default'] : 0;
$canQuickAdd = $idSku > 0 && $precioMostrar > 0;
$colorNombre = htmlspecialchars((string)($producto['color_nombre'] ?? ''), ENT_QUOTES, 'UTF-8');
$colorHex = htmlspecialchars((string)($producto['hex_code'] ?? ''), ENT_QUOTES, 'UTF-8');
?>
<article class="group relative bg-white" data-product-id="<?php echo $idProd; ?>" data-color-id="<?php echo $idColor; ?>">
    <div class="relative overflow-hidden aspect-[3/4] bg-[#f6f3ef]">
        <a href="<?php echo htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8'); ?>" class="block absolute inset-0" aria-label="Ver <?php echo $nombre; ?><?php echo $colorNombre !== '' ? ' en ' . $colorNombre : ''; ?>">
            <img
                src="<?php echo $imagen; ?>"
                alt="<?php echo $nombre; ?><?php echo $colorNombre !== '' ? ' — ' . $colorNombre : ''; ?>"
                loading="lazy"
                class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-[1.04]"
            >
        </a>

        <?php if ($badge !== ''): ?>
        <span class="absolute top-3 left-3 bg-accent text-white text-[10px] font-bold tracking-wider px-2.5 py-1 rounded-full z-10">
            <?php echo $badge; ?>
        </span>
        <?php endif; ?>

        <button
            type="button"
            class="absolute top-3 right-3 h-8 w-8 grid place-items-center rounded-full bg-white/90 opacity-0 group-hover:opacity-100 transition-opacity z-10"
            aria-label="Agregar a favoritos"
            data-action="wishlist-toggle"
        >
            <svg class="w-4 h-4 text-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </button>

        <?php if ($canQuickAdd): ?>
        <button
            type="button"
            class="absolute inset-x-0 bottom-0 translate-y-full group-hover:translate-y-0 transition-transform duration-300 bg-primary text-dark text-sm font-bold h-10 z-10"
            data-action="quick-add"
            data-id-sku="<?php echo $idSku; ?>"
            data-id-prod="<?php echo $idProd; ?>"
            data-nombre="<?php echo $nombre; ?>"
            data-precio="<?php echo $precioMostrar; ?>"
            data-imagen="<?php echo $imagen; ?>"
            data-color-nombre="<?php echo $colorNombre; ?>"
            data-color-hex="<?php echo $colorHex; ?>"
            data-talle-nombre="<?php echo htmlspecialchars((string)($producto['sku_talle_nombre'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
        >
            Agregar al carrito
        </button>
        <?php else: ?>
        <a
            href="<?php echo htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8'); ?>"
            class="absolute inset-x-0 bottom-0 translate-y-full group-hover:translate-y-0 transition-transform duration-300 bg-primary text-dark text-sm font-bold h-10 flex items-center justify-center z-10"
        >
            Ver producto
        </a>
        <?php endif; ?>
    </div>

    <div class="pt-3 pb-6 space-y-1.5">
        <h3 class="text-sm font-semibold truncate">
            <a href="<?php echo htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8'); ?>" class="hover:text-accent transition-colors">
                <?php echo $nombre; ?>
            </a>
        </h3>

        <?php if ($colorNombre !== ''): ?>
        <p class="text-xs text-earth"><?php echo $colorNombre; ?></p>
        <?php endif; ?>

        <?php if (!empty($talles)): ?>
        <div class="flex flex-wrap gap-1">
            <?php foreach ($talles as $talleNombre): ?>
            <span class="inline-flex items-center justify-center min-w-[1.75rem] h-6 px-1.5 text-[10px] font-semibold border border-cream rounded-full text-earth">
                <?php echo htmlspecialchars((string)$talleNombre, ENT_QUOTES, 'UTF-8'); ?>
            </span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="flex items-baseline gap-2">
            <?php if ($tieneOferta): ?>
            <span class="text-xs line-through text-earth"><?php echo format_price($precioBase); ?></span>
            <span class="text-base font-bold text-accent"><?php echo format_price($precioOferta); ?></span>
            <?php else: ?>
            <span class="text-base font-bold"><?php echo format_price($precioMostrar); ?></span>
            <?php endif; ?>
        </div>
    </div>
</article>
