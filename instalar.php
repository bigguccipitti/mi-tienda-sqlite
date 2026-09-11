<?php
// Ejecuta este archivo UNA VEZ para crear la base de datos e insertar los productos de ejemplo:
//   php instalar.php

require __DIR__ . '/includes/db.php';

$sql = file_get_contents(__DIR__ . '/data/productos.sql');
$pdo->exec($sql);

echo "Base de datos creada correctamente en data/tienda.sqlite" . PHP_EOL;

$total = $pdo->query('SELECT COUNT(*) as total FROM productos')->fetch();
echo "Productos en la base de datos: " . $total['total'] . PHP_EOL;
