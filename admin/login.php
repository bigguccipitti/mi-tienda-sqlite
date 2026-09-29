<?php
require_once __DIR__ . '/../includes/auth.php';

admin_iniciar_sesion_segura();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($usuario === '' || $password === '') {
        $error = 'Ingresa usuario y contraseña.';
    } elseif (admin_intentar_login($usuario, $password)) {
        header('Location: index.php');
        exit;
    } else {
        $error = 'Credenciales incorrectas.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SANTORO. — Admin Login</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">
    <div class="admin-login-box">
        <h1>SANTORO. <span>Admin</span></h1>

        <?php if (!empty($_GET['expirada'])): ?>
            <p class="mensaje-error">Tu sesión expiró por inactividad. Inicia sesión de nuevo.</p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p class="mensaje-error" role="alert"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="post" class="form-checkout">
            <label>Usuario
                <input type="text" name="usuario" required autocomplete="username" autofocus>
            </label>
            <label>Contraseña
                <input type="password" name="password" required autocomplete="current-password">
            </label>
            <button type="submit">Ingresar</button>
        </form>
    </div>
</body>
</html>
