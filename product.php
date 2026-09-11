<?php
// product.php — Detalle de un producto
// Se conecta con: includes/db.php, includes/header.php, includes/footer.php, css/style.css

require __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare('SELECT * FROM productos WHERE id = ?');
$stmt->execute([$id]);
$producto = $stmt->fetch();

if (!$producto) {
    echo '<p>Producto no encontrado.</p>';
    include __DIR__ . '/includes/footer.php';
    exit;
}
?>

<div class="detalle-producto">
    <img
        src="images/productos/<?php echo htmlspecialchars($producto['imagen']); ?>"
        alt="<?php echo htmlspecialchars($producto['disenador'] . ' — ' . $producto['nombre']); ?>"
    >
    <div class="info-producto">
        <span class="designer">
            <?php echo htmlspecialchars($producto['disenador']); ?> · <?php echo htmlspecialchars($producto['genero']); ?>
        </span>
        <h1><?php echo htmlspecialchars($producto['nombre']); ?></h1>
        <p class="precio">$<?php echo number_format($producto['precio'], 2); ?></p>
        <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
        <form method="post" action="cart.php">
            <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
            <button type="submit" name="agregar" class="boton">Add to Cart</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
