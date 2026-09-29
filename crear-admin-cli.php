<?php
/**
 * crear-admin-cli.php
 *
 * Uso (SOLO por terminal, nunca accesible por navegador):
 *   php crear-admin-cli.php tu_usuario
 *
 * Te pedirá la contraseña de forma interactiva (no queda en el historial
 * de bash como pasaría si la pasaras como argumento).
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('Este script solo puede ejecutarse por línea de comandos.');
}

require_once __DIR__ . '/includes/db.php';

$usuario = $argv[1] ?? null;
if (!$usuario) {
    fwrite(STDERR, "Uso: php crear-admin-cli.php <usuario>\n");
    exit(1);
}

fwrite(STDOUT, "Contraseña para '$usuario': ");
system('stty -echo');
$password = trim(fgets(STDIN));
system('stty echo');
fwrite(STDOUT, "\n");

if (strlen($password) < 12) {
    fwrite(STDERR, "La contraseña debe tener al menos 12 caracteres.\n");
    exit(1);
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare('INSERT INTO admins (usuario, password_hash) VALUES (?, ?)');
$stmt->execute([$usuario, $hash]);

fwrite(STDOUT, "Admin '$usuario' creado correctamente.\n");
