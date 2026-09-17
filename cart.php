<?php
require __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/header.php';

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

if (isset($_POST['agregar'])) {
    $id = (int)$_POST['id'];
    $_SESSION['carrito'][$id] = ($_SESSION['carrito'][$id] ?? 0) + 1;
    header('Location: cart.php');
    exit;
}

if (isset($_GET['sumar'])) {
    $id = (int)$_GET['sumar'];
    if (isset($_SESSION['carrito'][$id])) $_SESSION['carrito'][$id]++;
    header('Location: cart.php');
    exit;
}

if (isset($_GET['restar'])) {
    $id = (int)$_GET['restar'];
    if (isset($_SESSION['carrito'][$id])) {
        $_SESSION['carrito'][$id]--;
        if ($_SESSION['carrito'][$id] <= 0) unset($_SESSION['carrito'][$id]);
    }
    header('Location: cart.php');
    exit;
}

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

<h1 class="reveal">Cart</h1>

<?php if (empty($items)): ?>
    <p>Your cart is empty. <a href="index.php">View products</a></p>
<?php else: ?>
    <table class="tabla-carrito reveal">
        <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Subtotal</th>
            <th></th>
        </tr>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['producto']['nombre']); ?></td>
                <td>
                    <div class="stepper">
                        <a href="cart.php?restar=<?php echo $item['producto']['id']; ?>" class="stepper-btn">−</a>
                        <span><?php echo $item['cantidad']; ?></span>
                        <a href="cart.php?sumar=<?php echo $item['producto']['id']; ?>" class="stepper-btn">+</a>
                    </div>
                </td>
                <td>$<?php echo number_format($item['subtotal'], 2); ?></td>
                <td><a href="cart.php?eliminar=<?php echo $item['producto']['id']; ?>" class="quitar">Remove</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <p class="total">Total: $<?php echo number_format($total, 2); ?></p>
    <a href="checkout.php" class="boton">Checkout</a>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>