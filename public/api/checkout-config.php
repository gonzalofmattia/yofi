<?php

declare(strict_types=1);

require_once __DIR__ . '/_bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    api_json(['success' => false, 'message' => 'Método no permitido'], 405);
}

try {
    $pdo = db_ro();
    $stmt = $pdo->query('SELECT codigo, nombre, descripcion FROM tbl_metodos_pago WHERE activo = 1 ORDER BY orden ASC, nombre ASC');
    $metodos = $stmt ? ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: []) : [];

    if ($metodos === []) {
        $metodos = [[
            'codigo' => 'mercadopago',
            'nombre' => 'Mercado Pago',
            'descripcion' => 'Tarjetas, transferencia y dinero en cuenta',
        ]];
    }

    api_json([
        'success' => true,
        'metodos_pago' => $metodos,
    ]);
} catch (Throwable $e) {
    error_log('checkout-config.php: ' . $e->getMessage());
    api_json(['success' => false, 'message' => 'Error al cargar configuración'], 500);
}
