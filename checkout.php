<?php
require __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/header.php';

$confirmado = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Aquí, en un proyecto real, se procesaría el pago (Stripe, PayPal, etc.)
    // Por ahora, solo simulamos una confirmación de orden.
    $confirmado = true;
    $_SESSION['carrito'] = [];
}
?>

<h1>Checkout (práctica)</h1>

<?php if ($confirmado): ?>
    <p>¡Gracias! Tu orden de práctica fue registrada (no se procesó ningún pago real).</p>
    <a href="index.php">Volver a la tienda</a>
<?php else: ?>
    <form method="post" class="form-checkout">
        <label>Nombre completo
            <input type="text" name="nombre" required>
        </label>
        <label>Correo electrónico
            <input type="email" name="correo" required>
        </label>
        <label>Dirección de envío
            <input type="text" name="direccion" required>
        </label>
        <button type="submit">Confirmar orden (práctica)</button>
    </form>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
