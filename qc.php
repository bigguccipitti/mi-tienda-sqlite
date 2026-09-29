<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/shopify.php';
require_once __DIR__ . '/includes/eventos.php';

$token = $_GET['token'] ?? ($_POST['token'] ?? '');
$mensaje = '';
$error = '';

if (!$token) {
    http_response_code(400);
    exit('Enlace inválido.');
}

$stmt = $pdo->prepare('SELECT * FROM pedidos WHERE qc_token = ?');
$stmt->execute([$token]);
$pedido = $stmt->fetch();

if (!$pedido) {
    http_response_code(404);
    exit('Este enlace no es válido o ya expiró.');
}

// ---------- Procesar respuesta del cliente ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pedido['estado'] === 'pending_qc') {
    $decision = $_POST['decision'] ?? '';

    try {
        if ($decision === 'aprobar') {
            $stmtUpdate = $pdo->prepare("UPDATE pedidos SET estado = 'approved', qc_respondido_en = datetime('now') WHERE id = ?");
            $stmtUpdate->execute([$pedido['id']]);
            registrar_evento($pdo, $pedido['id'], 'qc_approved', 'Cliente aprobó las fotos QC.');
            $mensaje = 'Gracias — tu pedido fue aprobado y se despachará en breve.';
            $pedido['estado'] = 'approved';

        } elseif ($decision === 'rechazar') {
            shopify_reembolsar_orden($pedido['shopify_order_id']);

            $stmtUpdate = $pdo->prepare("UPDATE pedidos SET estado = 'rejected', qc_respondido_en = datetime('now') WHERE id = ?");
            $stmtUpdate->execute([$pedido['id']]);
            registrar_evento($pdo, $pedido['id'], 'qc_rejected', 'Cliente rechazó — reembolso disparado en Shopify.');
            $mensaje = 'Tu solicitud de devolución fue procesada. El reembolso aparecerá en tu método de pago original en los próximos días.';
            $pedido['estado'] = 'rejected';

        } else {
            $error = 'Selecciona una opción válida.';
        }
    } catch (\Throwable $e) {
        $error = 'No pudimos procesar tu solicitud. Por favor contáctanos directamente.';
        // En producción: registrar $e->getMessage() en un log interno.
    }
}

$stmtFotos = $pdo->prepare('SELECT * FROM fotos_qc WHERE pedido_id = ? ORDER BY orden');
$stmtFotos->execute([$pedido['id']]);
$fotos = $stmtFotos->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SANTORO. — Quality Inspection</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="site-header">
        <a href="index.php" class="logo">SANTORO.</a>
    </header>
    <main class="container" id="contenido">
        <h1>Quality Inspection — Order #<?php echo htmlspecialchars($pedido['shopify_order_id']); ?></h1>

        <?php if ($mensaje): ?>
            <p class="mensaje-exito" role="status"><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p class="mensaje-error" role="alert"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if ($pedido['estado'] !== 'pending_qc'): ?>
            <p>Current status: <strong><?php echo htmlspecialchars($pedido['estado']); ?></strong></p>
        <?php else: ?>
            <div class="fotos-qc-grid">
                <?php foreach ($fotos as $f): ?>
                    <div>
                        <img src="<?php echo htmlspecialchars($f['url_foto']); ?>" alt="<?php echo htmlspecialchars($f['etiqueta']); ?>">
                        <p><?php echo htmlspecialchars($f['etiqueta']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <p class="nota-practica">
                Quality Inspection Policy: We inspect every piece before dispatch. You have 48 hours to review and approve or request a return.
                If we don't hear from you within that window, the order will be automatically approved and shipped.
            </p>

            <form method="post" class="acciones-form">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <button type="submit" name="decision" value="aprobar">Approve &amp; Ship</button>
                <button type="submit" name="decision" value="rechazar" class="boton-secundario">Reject &amp; Request Refund</button>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>
