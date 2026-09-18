<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';
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

$stmtRelacionados = $pdo->prepare(
    'SELECT id, disenador, nombre, precio, imagen
     FROM productos
     WHERE categoria = ? AND genero = ? AND id != ?
     ORDER BY id LIMIT 3'
);
$stmtRelacionados->execute([$producto['categoria'], $producto['genero'], $producto['id']]);
$relacionados = $stmtRelacionados->fetchAll();

$numeroArchivo = str_pad($producto['id'], 3, '0', STR_PAD_LEFT);
?>

<nav class="breadcrumb reveal">
    <a href="index.php">Archive</a> /
    <a href="<?php echo strtolower($producto['genero']); ?>.php"><?php echo htmlspecialchars($producto['genero']); ?></a> /
    <span><?php echo htmlspecialchars($producto['categoria']); ?></span>
</nav>

<div class="detalle-producto reveal">
    <div class="frame frame-detalle">
        <span class="codigo-archivo">N° <?php echo $numeroArchivo; ?></span>
        <img
            src="images/productos/<?php echo htmlspecialchars($producto['imagen']); ?>"
            alt="<?php echo htmlspecialchars($producto['disenador'] . ' — ' . $producto['nombre']); ?>"
        >
    </div>
    <div class="info-producto">
        <span class="designer"><?php echo htmlspecialchars($producto['disenador']); ?></span>
        <h1><?php echo htmlspecialchars($producto['nombre']); ?></h1>
        <p class="precio">$<?php echo number_format($producto['precio'], 2); ?></p>

        <dl class="ficha-tecnica">
            <div><dt>Category</dt><dd><?php echo htmlspecialchars($producto['categoria']); ?></dd></div>
            <div><dt>Archive N°</dt><dd><?php echo $numeroArchivo; ?></dd></div>
        </dl>

        <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
        <form method="post" action="cart.php">
            <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
            <button type="submit" name="agregar" class="boton">Add to Cart</button>
        </form>
    </div>
</div>

<?php if (!empty($relacionados)): ?>
<section class="seccion-genero">
    <div class="seccion-head reveal">
        <h2>You May Also Like</h2>
        <span><?php echo count($relacionados); ?> pieces</span>
    </div>
    <div class="grid-productos">
        <?php foreach ($relacionados as $p) render_producto($p); ?>
    </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
