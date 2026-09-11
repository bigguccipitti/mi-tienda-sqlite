<?php
require __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/header.php';

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = []; // formato: [id_producto => cantidad]
}

// Agregar producto al carrito
if (isset($_POST['agregar'])) {
    $id = (int)$_POST['id'];
    $_SESSION['carrito'][$id] = ($_SESSION['carrito'][$id] ?? 0) + 1;
    header('Location: cart.php');
    exit;
}

// Eliminar producto del carrito
if (isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    unset($_SESSION['carrito'][$id]);
    header('Location: cart.php');
    exit;
}

$items = [];
$total = 0;

if (!empty($_SESSION['carrito'])) {
    $ids = array_keys($_SESSION['carrito']);
    $in = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id IN ($in)");
    $stmt->execute($ids);
    $productos = $stmt->fetchAll();

    foreach ($productos as $p) {
        $cantidad = $_SESSION['carrito'][$p['id']];
        $subtotal = $cantidad * $p['precio'];
        $total += $subtotal;
        $items[] = ['producto' => $p, 'cantidad' => $cantidad, 'subtotal' => $subtotal];
    }
}
?>

<h1>Tu carrito</h1>

<?php if (empty($items)): ?>
    <p>Tu carrito está vacío. <a href="index.php">Ver productos</a></p>
<?php else: ?>
    <table class="tabla-carrito">
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
            <th></th>
        </tr>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['producto']['disenador']); ?> — <?php echo htmlspecialchars($item['producto']['nombre']); ?></td>
                <td><?php echo $item['cantidad']; ?></td>
                <td>$<?php echo number_format($item['subtotal'], 2); ?></td>
                <td><a href="cart.php?eliminar=<?php echo $item['producto']['id']; ?>">Quitar</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <p class="total">Total: $<?php echo number_format($total, 2); ?></p>
    <a href="checkout.php" class="boton">Ir a pagar (práctica)</a>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
