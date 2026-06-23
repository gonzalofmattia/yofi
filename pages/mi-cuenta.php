<?php

require_once __DIR__ . '/../src/php/auth.php';
require_once __DIR__ . '/../src/php/users.php';
require_once __DIR__ . '/../src/php/addresses.php';
require_once __DIR__ . '/../src/php/wishlist.php';
require_once __DIR__ . '/../src/php/products.php';

$tab = preg_replace('/[^a-z]/', '', strtolower((string) ($_GET['tab'] ?? 'perfil')));
$allowedTabs = ['perfil', 'direcciones', 'pedidos', 'pedido', 'deseos', 'logout'];
if (!in_array($tab, $allowedTabs, true)) {
    $tab = 'perfil';
}

$requiresLogin = $tab !== 'deseos';
$loggedIn = isUserLoggedIn();
$userId = getLoggedInUserId();
$userData = null;

if ($requiresLogin && !$loggedIn) {
    header('Location: ' . page_path('login') . '&redirect=mi-cuenta');
    exit;
}

if ($loggedIn && $userId) {
    $userData = getUserData($userId);
    if (!$userData) {
        logoutUser();
        header('Location: ' . page_path('login'));
        exit;
    }
}

$page_title = 'Mi cuenta | ' . SITE_NAME;
$flashError = null;
$flashSuccess = null;

if (isset($_GET['welcome'])) {
    $flashSuccess = '¡Bienvenido/a a Yofi!';
}
if (isset($_GET['password_set'])) {
    $flashSuccess = 'Contraseña actualizada correctamente.';
}

// Logout
if ($tab === 'logout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (validatePublicCsrfToken($_POST['csrf_token'] ?? null)) {
        logoutUser();
        header('Location: ' . page_path('login') . '&logout=1');
        exit;
    }
    $flashError = 'No se pudo cerrar sesión. Intentá de nuevo.';
    $tab = 'perfil';
}

// POST handlers (logged in only)
if ($loggedIn && $userId && $_SERVER['REQUEST_METHOD'] === 'POST' && $tab !== 'logout') {
    if (!validatePublicCsrfToken($_POST['csrf_token'] ?? null)) {
        $flashError = 'La sesión expiró. Recargá la página.';
    } else {
        $formAction = $_POST['form_action'] ?? '';

        if ($formAction === 'profile') {
            $result = updateUserProfile($userId, $_POST);
            $flashSuccess = $result['success'] ? $result['message'] : null;
            $flashError = $result['success'] ? null : $result['message'];
            $userData = getUserData($userId);
            $tab = 'perfil';
        } elseif ($formAction === 'password') {
            $result = changeUserPassword($userId, (string) ($_POST['current_password'] ?? ''), (string) ($_POST['new_password'] ?? ''));
            $flashSuccess = $result['success'] ? $result['message'] : null;
            $flashError = $result['success'] ? null : $result['message'];
            $tab = 'perfil';
        } elseif ($formAction === 'address_save') {
            $addrId = isset($_POST['id_direccion']) && $_POST['id_direccion'] !== '' ? (int) $_POST['id_direccion'] : null;
            $result = saveUserAddress($userId, $_POST, $addrId);
            $flashSuccess = $result['success'] ? $result['message'] : null;
            $flashError = $result['success'] ? null : $result['message'];
            $tab = 'direcciones';
        } elseif ($formAction === 'address_delete') {
            $result = deleteUserAddress($userId, (int) ($_POST['id_direccion'] ?? 0));
            $flashSuccess = $result['success'] ? $result['message'] : null;
            $flashError = $result['success'] ? null : $result['message'];
            $tab = 'direcciones';
        } elseif ($formAction === 'address_default') {
            $result = setDefaultUserAddress($userId, (int) ($_POST['id_direccion'] ?? 0));
            $flashSuccess = $result['success'] ? $result['message'] : null;
            $flashError = $result['success'] ? null : $result['message'];
            $tab = 'direcciones';
        } elseif ($formAction === 'wishlist_remove' && $userId) {
            removeWishlistItem($userId, (int) ($_POST['producto_id'] ?? 0));
            $flashSuccess = 'Producto quitado de favoritos';
            $tab = 'deseos';
        }
    }
}

$addresses = ($loggedIn && $userId) ? getUserAddresses($userId) : [];
if ($loggedIn && $userId) {
    syncUserOrdersByEmail($userId);
}
$orders = ($loggedIn && $userId) ? getUserOrders($userId) : [];

$orderDetail = null;
if ($tab === 'pedido' && $loggedIn && $userId) {
    $orderId = (int) ($_GET['id'] ?? 0);
    $orderDetail = getUserOrderDetail($userId, $orderId);
    if (!$orderDetail) {
        $flashError = 'Pedido no encontrado';
        $tab = 'pedidos';
    }
}

// Wishlist products
$wishlistIds = [];
if ($loggedIn && $userId) {
    $wishlistIds = getWishlistProductIds($userId);
}
$wishlistProducts = get_products_for_wishlist($wishlistIds);

$estadoLabels = [
    'pendiente' => 'Pendiente',
    'confirmado' => 'Confirmado',
    'en_preparacion' => 'En preparación',
    'preparando_envio' => 'Preparando envío',
    'enviado' => 'Enviado',
    'entregado' => 'Entregado',
    'cancelado' => 'Cancelado',
];

function account_tab_url(string $t): string
{
    return page_path('mi-cuenta') . '&tab=' . urlencode($t);
}

$navItems = [
    'perfil' => 'Datos personales',
    'direcciones' => 'Direcciones',
    'pedidos' => 'Pedidos',
    'deseos' => 'Lista de deseos',
];
?>
<section class="w-full px-6 md:px-8 py-8 md:py-12">
    <div class="max-w-5xl mx-auto">
        <h1 class="text-2xl md:text-3xl font-bold text-dark mb-6">Mi cuenta</h1>

        <?php if ($flashError): ?>
        <div class="mb-4 rounded-lg border border-accent/30 bg-accent/10 text-sm px-4 py-3" role="alert"><?php echo htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if ($flashSuccess): ?>
        <div class="mb-4 rounded-lg border border-secondary/40 bg-secondary/10 text-sm px-4 py-3" role="status"><?php echo htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <div class="flex flex-col md:flex-row gap-8">
            <nav class="md:w-56 shrink-0" aria-label="Sección de cuenta">
                <ul class="flex md:flex-col gap-1 overflow-x-auto md:overflow-visible pb-2 md:pb-0">
                    <?php foreach ($navItems as $key => $label): ?>
                    <li>
                        <a href="<?php echo htmlspecialchars(account_tab_url($key), ENT_QUOTES, 'UTF-8'); ?>"
                           class="block whitespace-nowrap px-4 py-2.5 rounded-full text-sm font-semibold transition-colors <?php echo $tab === $key ? 'bg-primary text-dark' : 'text-earth hover:bg-cream'; ?>">
                            <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                    <?php if ($loggedIn): ?>
                    <li class="md:mt-4 md:border-t md:border-cream md:pt-4">
                        <form method="post" action="<?php echo account_tab_url('logout'); ?>">
                            <?php echo public_csrf_field(); ?>
                            <button type="submit" class="block w-full text-left px-4 py-2.5 rounded-full text-sm font-semibold text-accent hover:bg-cream">Cerrar sesión</button>
                        </form>
                    </li>
                    <?php else: ?>
                    <li class="md:mt-4">
                        <a href="<?php echo page_path('login'); ?>&redirect=mi-cuenta" class="block px-4 py-2.5 text-sm font-semibold text-accent underline">Ingresar</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>

            <div class="flex-1 min-w-0">
                <?php if ($tab === 'perfil' && $userData): ?>
                <div class="space-y-8">
                    <div class="bg-white border border-cream rounded-2xl p-6">
                        <h2 class="text-lg font-bold mb-4">Datos personales</h2>
                        <form method="post" class="space-y-4">
                            <?php echo public_csrf_field(); ?>
                            <input type="hidden" name="form_action" value="profile">
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold mb-1" for="nombre">Nombre</label>
                                    <input type="text" id="nombre" name="nombre" required class="w-full border border-cream rounded-lg px-3 py-2 text-sm" value="<?php echo htmlspecialchars($userData['nombre'], ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1" for="apellido">Apellido</label>
                                    <input type="text" id="apellido" name="apellido" required class="w-full border border-cream rounded-lg px-3 py-2 text-sm" value="<?php echo htmlspecialchars($userData['apellido'], ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1" for="email">Email</label>
                                <input type="email" id="email" name="email" required class="w-full border border-cream rounded-lg px-3 py-2 text-sm" value="<?php echo htmlspecialchars($userData['email'], ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold mb-1" for="telefono">Teléfono</label>
                                    <input type="tel" id="telefono" name="telefono" class="w-full border border-cream rounded-lg px-3 py-2 text-sm" value="<?php echo htmlspecialchars($userData['telefono'], ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1" for="dni">DNI</label>
                                    <input type="text" id="dni" name="dni" class="w-full border border-cream rounded-lg px-3 py-2 text-sm" value="<?php echo htmlspecialchars($userData['dni'], ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                            </div>
                            <button type="submit" class="bg-accent text-white font-bold px-6 py-2.5 rounded-full text-sm">Guardar cambios</button>
                        </form>
                    </div>

                    <div class="bg-white border border-cream rounded-2xl p-6">
                        <h2 class="text-lg font-bold mb-4">Cambiar contraseña</h2>
                        <?php
                        $hasPassword = false;
                        try {
                            $pwRow = db_ro()->prepare('SELECT password_hash FROM tbl_usuarios WHERE id_usuario = ?');
                            $pwRow->execute([$userId]);
                            $hasPassword = !empty($pwRow->fetchColumn());
                        } catch (Throwable $e) {
                            $hasPassword = true;
                        }
                        ?>
                        <?php if (!$hasPassword): ?>
                        <p class="text-sm text-earth mb-4">Tu cuenta no tiene contraseña todavía. Creá una nueva abajo.</p>
                        <?php endif; ?>
                        <form method="post" class="space-y-4 max-w-md">
                            <?php echo public_csrf_field(); ?>
                            <input type="hidden" name="form_action" value="password">
                            <?php if ($hasPassword): ?>
                            <div>
                                <label class="block text-sm font-semibold mb-1" for="current_password">Contraseña actual</label>
                                <input type="password" id="current_password" name="current_password" required autocomplete="current-password" class="w-full border border-cream rounded-lg px-3 py-2 text-sm">
                            </div>
                            <?php endif; ?>
                            <div>
                                <label class="block text-sm font-semibold mb-1" for="new_password">Contraseña nueva</label>
                                <input type="password" id="new_password" name="new_password" required minlength="8" autocomplete="new-password" class="w-full border border-cream rounded-lg px-3 py-2 text-sm">
                            </div>
                            <button type="submit" class="bg-dark text-white font-bold px-6 py-2.5 rounded-full text-sm">Actualizar contraseña</button>
                        </form>
                    </div>
                </div>

                <?php elseif ($tab === 'direcciones' && $userId): ?>
                <?php
                $editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
                $editAddr = $editId > 0 ? getUserAddress($userId, $editId) : null;
                ?>
                <div class="space-y-6">
                    <div class="bg-white border border-cream rounded-2xl p-6">
                        <h2 class="text-lg font-bold mb-4"><?php echo $editAddr ? 'Editar dirección' : 'Nueva dirección'; ?></h2>
                        <form method="post" class="space-y-4">
                            <?php echo public_csrf_field(); ?>
                            <input type="hidden" name="form_action" value="address_save">
                            <?php if ($editAddr): ?>
                            <input type="hidden" name="id_direccion" value="<?php echo (int) $editAddr['id_direccion']; ?>">
                            <?php endif; ?>
                            <div class="grid sm:grid-cols-3 gap-4">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-semibold mb-1">Calle</label>
                                    <input type="text" name="calle" required class="w-full border border-cream rounded-lg px-3 py-2 text-sm" value="<?php echo htmlspecialchars((string) ($editAddr['calle'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1">Número</label>
                                    <input type="text" name="numero" class="w-full border border-cream rounded-lg px-3 py-2 text-sm" value="<?php echo htmlspecialchars((string) ($editAddr['numero'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                            </div>
                            <div class="grid sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold mb-1">Depto / Piso</label>
                                    <input type="text" name="depto" class="w-full border border-cream rounded-lg px-3 py-2 text-sm" value="<?php echo htmlspecialchars((string) ($editAddr['depto'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1">Ciudad</label>
                                    <input type="text" name="ciudad" required class="w-full border border-cream rounded-lg px-3 py-2 text-sm" value="<?php echo htmlspecialchars((string) ($editAddr['ciudad'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1">Provincia</label>
                                    <input type="text" name="provincia" required class="w-full border border-cream rounded-lg px-3 py-2 text-sm" value="<?php echo htmlspecialchars((string) ($editAddr['provincia'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                            </div>
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold mb-1">Código postal</label>
                                    <input type="text" name="cp" required class="w-full border border-cream rounded-lg px-3 py-2 text-sm" value="<?php echo htmlspecialchars((string) ($editAddr['cp'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="flex items-end">
                                    <label class="inline-flex items-center gap-2 text-sm">
                                        <input type="checkbox" name="predeterminada" value="1" <?php echo ($editAddr['predeterminada'] ?? 0) ? 'checked' : ''; ?> class="rounded border-cream">
                                        Marcar como predeterminada
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="bg-accent text-white font-bold px-6 py-2.5 rounded-full text-sm">Guardar dirección</button>
                            <?php if ($editAddr): ?>
                            <a href="<?php echo account_tab_url('direcciones'); ?>" class="ml-3 text-sm text-earth underline">Cancelar</a>
                            <?php endif; ?>
                        </form>
                    </div>

                    <?php if ($addresses === []): ?>
                    <p class="text-earth text-sm">Todavía no guardaste direcciones.</p>
                    <?php else: ?>
                    <ul class="space-y-3">
                        <?php foreach ($addresses as $addr): ?>
                        <li class="bg-white border border-cream rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <?php if ((int) $addr['predeterminada'] === 1): ?>
                                <span class="inline-block text-[10px] font-bold uppercase tracking-wider bg-primary text-dark px-2 py-0.5 rounded-full mb-1">Predeterminada</span>
                                <?php endif; ?>
                                <p class="text-sm font-semibold"><?php echo htmlspecialchars(trim($addr['calle'] . ' ' . $addr['numero']), ENT_QUOTES, 'UTF-8'); ?><?php echo $addr['depto'] ? ', ' . htmlspecialchars((string) $addr['depto'], ENT_QUOTES, 'UTF-8') : ''; ?></p>
                                <p class="text-sm text-earth"><?php echo htmlspecialchars($addr['ciudad'] . ', ' . $addr['provincia'] . ' (' . $addr['cp'] . ')', ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <a href="<?php echo account_tab_url('direcciones'); ?>&edit=<?php echo (int) $addr['id_direccion']; ?>" class="text-xs font-semibold text-accent underline">Editar</a>
                                <?php if ((int) $addr['predeterminada'] !== 1): ?>
                                <form method="post" class="inline">
                                    <?php echo public_csrf_field(); ?>
                                    <input type="hidden" name="form_action" value="address_default">
                                    <input type="hidden" name="id_direccion" value="<?php echo (int) $addr['id_direccion']; ?>">
                                    <button type="submit" class="text-xs font-semibold text-earth underline">Predeterminada</button>
                                </form>
                                <?php endif; ?>
                                <form method="post" class="inline" onsubmit="return confirm('¿Eliminar esta dirección?');">
                                    <?php echo public_csrf_field(); ?>
                                    <input type="hidden" name="form_action" value="address_delete">
                                    <input type="hidden" name="id_direccion" value="<?php echo (int) $addr['id_direccion']; ?>">
                                    <button type="submit" class="text-xs font-semibold text-accent underline">Eliminar</button>
                                </form>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>

                <?php elseif ($tab === 'pedidos' && $loggedIn): ?>
                <?php if ($orders === []): ?>
                <p class="text-earth text-sm">Todavía no tenés pedidos asociados a tu cuenta.</p>
                <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-cream text-left text-earth">
                                <th class="py-2 pr-4">Pedido</th>
                                <th class="py-2 pr-4">Fecha</th>
                                <th class="py-2 pr-4">Estado</th>
                                <th class="py-2 pr-4">Total</th>
                                <th class="py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $ord): ?>
                            <tr class="border-b border-cream/60">
                                <td class="py-3 pr-4 font-semibold"><?php echo htmlspecialchars((string) $ord['numero_orden'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="py-3 pr-4"><?php echo htmlspecialchars(date('d/m/Y', strtotime((string) $ord['fecha_creacion'])), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="py-3 pr-4"><?php echo htmlspecialchars($estadoLabels[$ord['estado']] ?? ucfirst((string) $ord['estado']), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="py-3 pr-4 font-bold"><?php echo format_price((float) $ord['total']); ?></td>
                                <td class="py-3"><a class="text-accent underline font-semibold" href="<?php echo account_tab_url('pedido'); ?>&id=<?php echo (int) $ord['id_orden']; ?>">Ver detalle</a></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

                <?php elseif ($tab === 'pedido' && $orderDetail): ?>
                <?php $items = json_decode((string) $orderDetail['items'], true) ?: []; ?>
                <div class="mb-4">
                    <a href="<?php echo account_tab_url('pedidos'); ?>" class="text-sm text-accent underline">← Volver a pedidos</a>
                </div>
                <div class="bg-white border border-cream rounded-2xl p-6 space-y-4">
                    <div class="flex flex-wrap justify-between gap-2">
                        <div>
                            <h2 class="text-lg font-bold"><?php echo htmlspecialchars((string) $orderDetail['numero_orden'], ENT_QUOTES, 'UTF-8'); ?></h2>
                            <p class="text-sm text-earth"><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime((string) $orderDetail['fecha_creacion'])), ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                        <span class="inline-flex items-center h-8 px-3 rounded-full bg-cream text-sm font-semibold"><?php echo htmlspecialchars($estadoLabels[$orderDetail['estado']] ?? ucfirst((string) $orderDetail['estado']), ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <ul class="divide-y divide-cream border-t border-cream pt-4">
                        <?php foreach ($items as $item): ?>
                        <li class="py-3 flex gap-3 text-sm">
                            <?php if (!empty($item['imagen'])): ?>
                            <img src="<?php echo htmlspecialchars(order_item_image_url((string) $item['imagen']), ENT_QUOTES, 'UTF-8'); ?>" alt="" class="w-14 h-16 object-cover bg-cream/50 shrink-0 rounded">
                            <?php endif; ?>
                            <div class="flex-1 flex justify-between gap-4 min-w-0">
                            <div>
                                <p class="font-semibold"><?php echo htmlspecialchars((string) ($item['nombre'] ?? 'Producto'), ENT_QUOTES, 'UTF-8'); ?></p>
                                <p class="text-earth text-xs"><?php
                                    $parts = array_filter([$item['color_nombre'] ?? '', $item['talle_nombre'] ?? '']);
                                    echo htmlspecialchars(implode(' · ', $parts) . ' × ' . (int) ($item['cantidad'] ?? 1), ENT_QUOTES, 'UTF-8');
                                ?></p>
                            </div>
                            <p class="font-bold shrink-0"><?php echo format_price((float) ($item['precio_unitario'] ?? 0) * (int) ($item['cantidad'] ?? 1)); ?></p>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="border-t border-cream pt-4 space-y-1 text-sm">
                        <div class="flex justify-between"><span>Subtotal</span><span><?php echo format_price((float) $orderDetail['subtotal']); ?></span></div>
                        <div class="flex justify-between"><span>Envío</span><span><?php echo format_price((float) $orderDetail['envio']); ?></span></div>
                        <div class="flex justify-between font-bold text-base pt-2"><span>Total</span><span><?php echo format_price((float) $orderDetail['total']); ?></span></div>
                    </div>
                    <?php if (!empty($orderDetail['tracking_number'])): ?>
                    <p class="text-sm"><strong>Seguimiento:</strong> <?php echo htmlspecialchars((string) $orderDetail['tracking_number'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>
                </div>

                <?php elseif ($tab === 'deseos'): ?>
                <div id="account-wishlist-root" data-server-count="<?php echo count($wishlistProducts); ?>">
                    <?php if ($loggedIn && $wishlistProducts !== []): ?>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6">
                        <?php foreach ($wishlistProducts as $producto): ?>
                        <?php include __DIR__ . '/../partials/product-card.php'; ?>
                        <?php endforeach; ?>
                    </div>
                    <?php elseif ($loggedIn): ?>
                    <p class="text-earth text-sm" data-wishlist-empty-server>Tu lista de deseos está vacía.</p>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6 hidden" data-wishlist-grid></div>
                    <?php else: ?>
                    <p class="text-earth text-sm mb-4">Mostrando favoritos guardados en este dispositivo. <a href="<?php echo page_path('login'); ?>" class="text-accent underline">Ingresá</a> para sincronizarlos con tu cuenta.</p>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6" data-wishlist-grid></div>
                    <p class="text-earth text-sm hidden mt-4" data-wishlist-empty-guest>No tenés productos en favoritos todavía.</p>
                    <?php endif; ?>
                </div>
                <script>
                (function () {
                    var root = document.getElementById('account-wishlist-root');
                    if (!root || !window.YofiWishlist) return;
                    var grid = root.querySelector('[data-wishlist-grid]');
                    if (!grid) return;

                    function renderGuestWishlist() {
                        var ids = window.YofiWishlist.getWishlistIds();
                        var emptyEl = root.querySelector('[data-wishlist-empty-guest]');
                        if (ids.length === 0) {
                            grid.innerHTML = '';
                            if (emptyEl) emptyEl.classList.remove('hidden');
                            return;
                        }
                        if (emptyEl) emptyEl.classList.add('hidden');
                        var url = (window.YOFI && window.YOFI.apiWishlistProducts) + '?ids=' + ids.join(',');
                        fetch(url).then(function (r) { return r.json(); }).then(function (data) {
                            if (!data.products || !data.products.length) return;
                            grid.innerHTML = data.products.map(function (p) {
                                var price = p.precio_oferta && parseFloat(p.precio_oferta) > 0 && parseFloat(p.precio_oferta) < parseFloat(p.precio_base)
                                    ? p.precio_oferta : p.precio_base;
                                var img = p.imagen_principal || '';
                                var slug = p.slug || '';
                                var detail = 'index.php?p=producto&slug=' + encodeURIComponent(slug);
                                return '<article class="group relative bg-white" data-product-id="' + p.id_prod + '">' +
                                    '<div class="relative overflow-hidden aspect-[3/4] bg-[#f6f3ef]">' +
                                    '<a href="' + detail + '"><img src="' + img + '" alt="" class="absolute inset-0 h-full w-full object-cover"></a>' +
                                    '<button type="button" class="absolute top-3 right-3 h-8 w-8 grid place-items-center rounded-full bg-white/90 z-10" data-action="wishlist-toggle" aria-label="Quitar de favoritos">' +
                                    '<svg class="w-4 h-4 text-accent" fill="currentColor" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg></button></div>' +
                                    '<div class="pt-3 pb-6"><h3 class="text-sm font-semibold truncate">' + (p.nombre_display || p.nombre) + '</h3>' +
                                    '<p class="text-base font-bold mt-1">$' + Math.round(parseFloat(price)).toLocaleString('es-AR') + '</p></div></article>';
                            }).join('');
                            window.YofiWishlist.updateWishlistUi();
                        });
                    }

                    if (document.body.getAttribute('data-logged-in') !== '1') {
                        renderGuestWishlist();
                        document.addEventListener('wishlist:updated', renderGuestWishlist);
                    } else if (parseInt(root.getAttribute('data-server-count'), 10) === 0) {
                        renderGuestWishlist();
                    }
                })();
                </script>

                <?php elseif ($requiresLogin && !$loggedIn): ?>
                <p class="text-earth">Necesitás <a href="<?php echo page_path('login'); ?>" class="text-accent underline">iniciar sesión</a> para ver esta sección.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
