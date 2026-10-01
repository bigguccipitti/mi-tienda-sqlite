<?php
/**
 * auth.php — Autenticación del panel /admin/.
 *
 * Capa 1 de seguridad (esta). Capa 2 recomendada: Basic Auth a nivel de
 * Apache sobre /admin/ (ver admin/.htaccess-ejemplo e instrucciones al
 * final de este módulo) — así un bug aquí no es la única barrera.
 */

require_once __DIR__ . '/db.php';

function admin_iniciar_sesion_segura(): void {
    if (session_status() === PHP_SESSION_NONE) {
        // Cookie de sesión endurecida: httponly + samesite estricto.
        // 'secure' se activa solo si ya estás sirviendo bajo HTTPS
        // (Cloudflare Tunnel termina en HTTPS de cara al cliente).
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => true,
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        session_start();
    }
}

function admin_esta_autenticado(): bool {
    admin_iniciar_sesion_segura();
    return !empty($_SESSION['admin_id']);
}

/**
 * Llama esto al inicio de CADA página bajo /admin/ (excepto login.php).
 * Si no hay sesión válida, redirige a login y detiene la ejecución.
 */
function admin_requerir_sesion(): void {
    admin_iniciar_sesion_segura();
    if (empty($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }

    // Expira la sesión admin tras 30 min de inactividad (ajusta si lo prefieres).
    $limiteInactividad = 1800;
    if (isset($_SESSION['admin_ultima_actividad']) && (time() - $_SESSION['admin_ultima_actividad']) > $limiteInactividad) {
        admin_cerrar_sesion();
        header('Location: login.php?expirada=1');
        exit;
    }
    $_SESSION['admin_ultima_actividad'] = time();
}

function admin_intentar_login(string $usuario, string $password): bool {
    global $pdo;

    $stmt = $pdo->prepare('SELECT id, password_hash FROM admins WHERE usuario = ?');
    $stmt->execute([$usuario]);
    $fila = $stmt->fetch();

    if (!$fila || !password_verify($password, $fila['password_hash'])) {
        // Mensaje idéntico tanto si el usuario no existe como si la
        // contraseña es incorrecta — evita revelar qué falló (user enumeration).
        usleep(300000); // pequeño retraso fijo, dificulta timing attacks básicos
        return false;
    }

    admin_iniciar_sesion_segura();
    session_regenerate_id(true); // previene session fixation
    $_SESSION['admin_id'] = $fila['id'];
    $_SESSION['admin_usuario'] = $usuario;
    $_SESSION['admin_ultima_actividad'] = time();
    return true;
}

function admin_cerrar_sesion(): void {
    admin_iniciar_sesion_segura();
    $_SESSION = [];
    session_destroy();
    
    function admin_iniciar_sesion_segura() {
    if (session_status() === PHP_SESSION_NONE) {
        // secure debe ser false si no usas HTTPS (http://192.168.50.233)
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => false, 
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        session_start();
    }
}
