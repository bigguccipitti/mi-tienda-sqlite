<?php
require __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/header.php';

$confirmado = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $confirmado = true;
    $_SESSION['carrito'] = [];
}
?>

<h1 class="reveal">Checkout</h1>

<?php if ($confirmado): ?>
    <p>Thank you — your practice order was recorded (no real payment was processed).</p>
    <a href="index.php">Back to shop</a>
<?php else: ?>
    <form method="post" class="form-checkout reveal">
        <label>Full name
            <input type="text" name="nombre" required>
        </label>
        <label>Email
            <input type="email" name="correo" required>
        </label>
        <label>Shipping address
            <input type="text" name="direccion" required>
        </label>
        <button type="submit">Confirm order (practice)</button>
    </form>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
