<?php
require_once dirname(__DIR__) . '/include/includes.php';
require_once dirname(__DIR__) . '/check_session.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

$file = $_FILES['imagen'] ?? null;
if (!$file) {
    echo json_encode(['success' => false, 'error' => 'Imagen requerida']);
    exit;
}

$result = yofi_upload_imgprod_file($file, 'slide');
if (!$result['success']) {
    echo json_encode(['success' => false, 'error' => $result['error'] ?? 'Error al subir']);
    exit;
}

echo json_encode([
    'success' => true,
    'filename' => $result['filename'],
    'url' => imgprod_path($result['filename']),
]);
