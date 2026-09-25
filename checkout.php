<?php
require __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/header.php';

$confirmado = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['consentimiento'])) {
        $error = 'You must agree to the Privacy Policy and Terms & Conditions to place a practice order.';
    } else {
        $confirmado = true;
        $_SESSION['carrito'] = [];
    }
}
?>

<h1 class="reveal">Checkout</h1>

<?php if ($confirmado): ?>
    <p>Thank you — your practice order was recorded (no real payment was processed).</p>
    <a href="index.php">Back to shop</a>
<?php else: ?>
    <?php if ($error): ?>
        <p class="mensaje-error" role="alert"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <!--
        Solo se piden los datos estrictamente necesarios para procesar un pedido:
        nombre, correo (confirmación de orden) y dirección de envío.
        No se solicita teléfono, fecha de nacimiento ni ningún otro dato no necesario.
    -->
    <form method="post" class="form-checkout reveal">
        <label>Full name
            <input type="text" name="nombre" required autocomplete="name">
        </label>
        <label>Email
            <input type="email" name="correo" required autocomplete="email">
        </label>
        <label>Shipping address
            <input type="text" name="direccion" required autocomplete="street-address">
        </label>

        <div class="consentimiento-grupo">
            <label class="checkbox-linea">
                <input type="checkbox" name="consentimiento" value="1" required>
                <span>I agree to the <a href="privacy-policy.php">Privacy Policy</a> and <a href="terms.php">Terms &amp; Conditions</a>. Required to place an order.</span>
            </label>

            <label class="checkbox-linea">
                <input type="checkbox" name="marketing" value="1">
                <span>Send me occasional updates about new arrivals. Optional — you can unsubscribe anytime.</span>
            </label>
        </div>

        <button type="submit">Confirm order (practice)</button>
    </form>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
