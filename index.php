<?php
// index.php — Pantalla principal: hero SANTORO + Best Sellers
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';
include __DIR__ . '/includes/header.php';

$stmtBest = $pdo->prepare('SELECT id, disenador, nombre, precio, imagen FROM productos WHERE bestseller = 1 ORDER BY id');
$stmtBest->execute();
$bestsellers = $stmtBest->fetchAll();
?>

<section class="hero-santoro">
    <h1>SANTORO
        
    </h1>
    <p class="hero-santoro-sub">Verified luxury. Street-worn attitude.</p>
</section>

<section class="seccion-genero" id="best-sellers">
    <div class="seccion-head">
        <h2>Best Sellers</h2>
        <span><?php echo count($bestsellers); ?> pieces</span>
    </div>
    <div class="grid-productos">
        <?php foreach ($bestsellers as $p) render_producto($p); ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
