<?php
// Conexión a la base de datos usando SQLite (no requiere servidor ni instalación extra)
$dbPath = __DIR__ . '/../data/tienda.sqlite';
$dsn = "sqlite:$dbPath";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, null, null, $options);
    $pdo->exec('PRAGMA foreign_keys = ON;');
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
