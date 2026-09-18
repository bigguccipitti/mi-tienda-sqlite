<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';
include __DIR__ . '/includes/header.php';

$categorias = ['Shoes', 'Pants', 'Bags'];
$productosPorCategoria = [];

foreach ($categorias as $cat) {
    $stmt = $pdo->prepare('SELECT id, disenador, nombre, precio, imagen FROM productos WHERE genero = ? AND categoria = ? ORDER BY id');
    $stmt->execute(['Women', $cat]);
    $productosPorCategoria[$cat] = $stmt->fetchAll();
}
?>

<div class="pagina-head reveal">
    <h1>Women</h1>
    <div class="ordenar-control">
        <label for="ordenar">Sort</label>
        <select id="ordenar">
            <option value="default">Featured</option>
            <option value="asc">Price: Low to High</option>
            <option value="desc">Price: High to Low</option>
        </select>
    </div>
</div>

<?php foreach ($categorias as $cat): ?>
    <section class="seccion-genero" id="<?php echo strtolower($cat); ?>">
        <div class="seccion-head reveal">
            <h2><?php echo $cat; ?></h2>
            <span><?php echo count($productosPorCategoria[$cat]); ?> pieces</span>
        </div>
        <div class="grid-productos" data-ordenable="true">
            <?php foreach ($productosPorCategoria[$cat] as $p) render_producto($p); ?>
        </div>
    </section>
<?php endforeach; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
