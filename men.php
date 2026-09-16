<?php
// men.php — Todos los productos de hombre, divididos en Shoes / Pants / Bags / Jackets / Accessories / T-Shirts / Hoodies / Sweaters / Shorts
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';
include __DIR__ . '/includes/header.php';

$categorias = ['Shoes', 'Pants', 'Bags', 'Jackets', 'Accessories', 'T-Shirts', 'Hoodies', 'Sweaters', 'Shorts'];
$productosPorCategoria = [];

foreach ($categorias as $cat) {
    $stmt = $pdo->prepare('SELECT id, disenador, nombre, precio, imagen FROM productos WHERE genero = ? AND categoria = ? ORDER BY id');
    $stmt->execute(['Men', $cat]);
    $productosPorCategoria[$cat] = $stmt->fetchAll();
}
?>

<h1>Men</h1>

<?php foreach ($categorias as $cat): ?>
    <section class="seccion-genero" id="<?php echo strtolower($cat); ?>">
        <div class="seccion-head">
            <h2><?php echo $cat; ?></h2>
            <span><?php echo count($productosPorCategoria[$cat]); ?> pieces</span>
        </div>
        <div class="grid-productos">
            <?php foreach ($productosPorCategoria[$cat] as $p) render_producto($p); ?>
        </div>
    </section>
<?php endforeach; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
