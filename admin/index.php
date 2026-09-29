<?php
require_once __DIR__ . '/../includes/auth.php';
admin_requerir_sesion();
require_once __DIR__ . '/../includes/db.php';

$pendientes = $pdo->query("SELECT * FROM pedidos WHERE estado = 'pending_qc' ORDER BY creado_en DESC")->fetchAll();
$totalProductos = $pdo->query('SELECT COUNT(*) as total FROM productos')->fetch()['total'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SANTORO. — Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">
<div class="admin-wrap">
    <nav class="admin-nav">
        <a href="index.php" class="activo">Dashboard</a>
        <a href="nuevo-producto.php">Nuevo producto</a>
        <a href="qc.php">Panel QC</a>
        <a href="logout.php">Salir (<?php echo htmlspecialchars($_SESSION['admin_usuario']); ?>)</a>
    </nav>

    <h1>Dashboard</h1>

    <div class="tarjetas-resumen">
        <div class="tarjeta-resumen">
            <span class="numero"><?php echo count($pendientes); ?></span>
            <span class="etiqueta">Pedidos esperando aprobación QC</span>
        </div>
        <div class="tarjeta-resumen">
            <span class="numero"><?php echo $totalProductos; ?></span>
            <span class="etiqueta">Productos en catálogo</span>
        </div>
    </div>

    <h2>Pedidos pendientes de QC</h2>
    <?php if (empty($pendientes)): ?>
        <p>No hay pedidos esperando aprobación en este momento.</p>
    <?php else: ?>
        <table class="tabla-admin">
            <tr><th>Orden Shopify</th><th>Cliente</th><th>Fotos subidas</th><th>Vence</th><th></th></tr>
            <?php foreach ($pendientes as $p): ?>
                <tr>
                    <td>#<?php echo htmlspecialchars($p['shopify_order_id']); ?></td>
                    <td><?php echo htmlspecialchars($p['cliente_email']); ?></td>
                    <td><?php echo $p['qc_fotos_subidas_en'] ? htmlspecialchars($p['qc_fotos_subidas_en']) : '— aún no subidas —'; ?></td>
                    <td><?php echo $p['qc_vence_en'] ? htmlspecialchars($p['qc_vence_en']) : '—'; ?></td>
                    <td><a href="qc.php?pedido_id=<?php echo $p['id']; ?>">Gestionar</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
