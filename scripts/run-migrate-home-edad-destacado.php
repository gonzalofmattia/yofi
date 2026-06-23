<?php
require_once dirname(__DIR__) . '/admin/include/includes.php';

$sql = file_get_contents(__DIR__ . '/migrate-home-edad-destacado.sql');
if ($sql === false) {
    fwrite(STDERR, "No se pudo leer migrate-home-edad-destacado.sql\n");
    exit(1);
}

if (!mysqli_multi_query($con, $sql)) {
    fwrite(STDERR, 'Error SQL: ' . mysqli_error($con) . PHP_EOL);
    exit(1);
}

do {
    if ($result = mysqli_store_result($con)) {
        mysqli_free_result($result);
    }
} while (mysqli_next_result($con));

if (mysqli_errno($con)) {
    fwrite(STDERR, 'Error SQL: ' . mysqli_error($con) . PHP_EOL);
    exit(1);
}

echo "Migración OK\n";
