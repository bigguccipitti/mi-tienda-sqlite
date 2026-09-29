<?php
require_once __DIR__ . '/../includes/auth.php';
admin_requerir_sesion();

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/tokens.php';
require_once __DIR__ . '/../includes/mailer.php';
require_once __DIR__ . '/../includes/shopify.php';
require_once __DIR__ . '/../includes/eventos.php';
require_once __DIR__ . '/../config.php';

$error = '';
$exito = '';

$pedidoId = isset($_GET['pedido_id']) ? (int)$_GET['pedido_id'] : (int)($_POST['pedido_id'] ?? 0);

// ---------- Acción: subir fotos + enviar correo ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'subir_fotos') {
    try {
        $urls = array_filter(array_map('trim', $_POST['foto_url'] ?? []));
        $etiquetas = $_POST['foto_etiqueta'] ?? [];

        if (empty($urls)) {
            throw new InvalidArgumentException('Agrega al menos una URL de foto.');
        }

        $pdo->beginTransaction();

        $stmtFoto = $pdo->prepare('INSERT INTO fotos_qc (pedido_id, url_foto, etiqueta, orden) VALUES (?, ?, ?, ?)');
        $i = 0;
        foreach ($urls as $idx => $url) {
            $stmtFoto->execute([$pedidoId, $url, $etiquetas[$idx] ?? '', $i]);
            $i++;
        }

        $token = generar_token_qc_seguro();
        $ahora = date('Y-m-d H:i:s');
        $vence = date('Y-m-d H:i:s', strtotime("+" . QC_HORAS_LIMITE . " hours"));

        $stmtPedido = $pdo->prepare(
            'UPDATE pedidos SET qc_token = ?, qc_fotos_subidas_en = ?, qc_vence_en = ? WHERE id = ?'
        );
        $stmtPedido->execute([$token, $ahora, $vence, $pedidoId]);

        $pdo->commit();

        // Enviar correo (fuera de la transacción — si falla el correo, las fotos ya quedaron guardadas)
        $stmtCliente = $pdo->prepare('SELECT cliente_email, cliente_nombre FROM pedidos WHERE id = ?');
        $stmtCliente->execute([$pedidoId]);
        $cliente = $stmtCliente->fetch();

        $qcUrl = BASE_URL . '/qc.php?token=' . $token;
        $fotosParaCorreo = array_map(fn($url, $idx) => ['url' => $url, 'etiqueta' => $etiquetas[$idx] ?? ''], $urls, array_keys($urls));

        enviar_correo_qc_listo($cliente['cliente_email'], $cliente['cliente_nombre'], $qcUrl, $fotosParaCorreo);

        registrar_evento($pdo, $pedidoId, 'fotos_qc_subidas', 'Fotos subidas, token generado, correo enviado al cliente.');

        $exito = "Fotos guardadas y correo enviado. El cliente tiene hasta {$vence} para responder.";
    } catch (\Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $error = 'Error: ' . $e->getMessage();
    }
}

// ---------- Acción: marcar como enviado (tracking) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'marcar_enviado') {
    try {
        $tracking = trim($_POST['tracking_number'] ?? '');
        if ($tracking === '') {
            throw new InvalidArgumentException('Ingresa el número de tracking.');
        }

        $stmtPedidoInfo = $pdo->prepare('SELECT shopify_order_id FROM pedidos WHERE id = ?');
        $stmtPedidoInfo->execute([$pedidoId]);
        $infoPedido = $stmtPedidoInfo->fetch();

        shopify_marcar_enviado($infoPedido['shopify_order_id'], $tracking);

        $stmtUpdate = $pdo->prepare('UPDATE pedidos SET tracking_number = ?, estado = ? WHERE id = ?');
        $stmtUpdate->execute([$tracking, 'shipped', $pedidoId]);

        registrar_evento($pdo, $pedidoId, 'marcado_enviado', "Tracking: $tracking");

        $exito = 'Pedido marcado como enviado en Shopify y notificado al cliente.';
    } catch (\Throwable $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// ---------- Cargar datos del pedido seleccionado ----------
$pedido = null;
$fotosExistentes = [];
if ($pedidoId) {
    $stmt = $pdo->prepare('SELECT * FROM pedidos WHERE id = ?');
    $stmt->execute([$pedidoId]);
    $pedido = $stmt->fetch();

    if ($pedido) {
        $stmtFotos = $pdo->prepare('SELECT * FROM fotos_qc WHERE pedido_id = ? ORDER BY orden');
        $stmtFotos->execute([$pedidoId]);
        $fotosExistentes = $stmtFotos->fetchAll();
    }
}

$todosPedidos = $pdo->query("SELECT id, shopify_order_id, cliente_email, estado FROM pedidos ORDER BY creado_en DESC LIMIT 30")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SANTORO. — Panel QC</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">
<div class="admin-wrap">
    <nav class="admin-nav">
        <a href="index.php">Dashboard</a>
        <a href="nuevo-producto.php">Nuevo producto</a>
        <a href="qc.php" class="activo">Panel QC</a>
        <a href="logout.php">Salir</a>
    </nav>

    <h1>Panel QC</h1>

    <?php if ($error): ?><p class="mensaje-error" role="alert"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
    <?php if ($exito): ?><p class="mensaje-exito" role="status"><?php echo htmlspecialchars($exito); ?></p><?php endif; ?>

    <h2>Seleccionar pedido</h2>
    <table class="tabla-admin">
        <tr><th>ID</th><th>Orden Shopify</th><th>Cliente</th><th>Estado</th><th></th></tr>
        <?php foreach ($todosPedidos as $p): ?>
            <tr>
                <td>#<?php echo $p['id']; ?></td>
                <td><?php echo htmlspecialchars($p['shopify_order_id']); ?></td>
                <td><?php echo htmlspecialchars($p['cliente_email']); ?></td>
                <td><?php echo htmlspecialchars($p['estado']); ?></td>
                <td><a href="qc.php?pedido_id=<?php echo $p['id']; ?>">Gestionar</a></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php if ($pedido): ?>
        <h2>Pedido #<?php echo $pedido['id']; ?> — <?php echo htmlspecialchars($pedido['cliente_email']); ?></h2>
        <p>Estado actual: <strong><?php echo htmlspecialchars($pedido['estado']); ?></strong></p>

        <?php if (!empty($fotosExistentes)): ?>
            <h3>Fotos ya subidas</h3>
            <div class="fotos-qc-grid">
                <?php foreach ($fotosExistentes as $f): ?>
                    <div>
                        <img src="<?php echo htmlspecialchars($f['url_foto']); ?>" alt="<?php echo htmlspecialchars($f['etiqueta']); ?>">
                        <span><?php echo htmlspecialchars($f['etiqueta']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <p>Token QC: <code><?php echo htmlspecialchars($pedido['qc_token']); ?></code></p>
            <p>Vence: <?php echo htmlspecialchars($pedido['qc_vence_en']); ?></p>
        <?php else: ?>
            <h3>Subir fotos de QC (copia las URLs desde el dashboard de KakoBuy)</h3>
            <form method="post" class="form-admin">
                <input type="hidden" name="accion" value="subir_fotos">
                <input type="hidden" name="pedido_id" value="<?php echo $pedido['id']; ?>">

                <?php for ($i = 0; $i < 4; $i++): ?>
                    <div class="grid-2">
                        <label>URL foto <?php echo $i + 1; ?>
                            <input type="url" name="foto_url[]" placeholder="https://...">
                        </label>
                        <label>Etiqueta
                            <input type="text" name="foto_etiqueta[]" placeholder="Frente / Espalda / Etiqueta / Costura">
                        </label>
                    </div>
                <?php endfor; ?>

                <button type="submit">Guardar fotos y enviar correo al cliente</button>
            </form>
        <?php endif; ?>

        <?php if ($pedido['estado'] === 'approved' || $pedido['estado'] === 'auto_approved'): ?>
            <h3>Marcar como enviado</h3>
            <form method="post" class="form-admin">
                <input type="hidden" name="accion" value="marcar_enviado">
                <input type="hidden" name="pedido_id" value="<?php echo $pedido['id']; ?>">
                <label>Número de tracking (KakoBuy / línea internacional)
                    <input type="text" name="tracking_number" required value="<?php echo htmlspecialchars($pedido['tracking_number'] ?? ''); ?>">
                </label>
                <button type="submit">Confirmar despacho en Shopify</button>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>
