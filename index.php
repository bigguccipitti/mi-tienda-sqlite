<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';
include __DIR__ . '/includes/header.php';

$stmtBest = $pdo->prepare('SELECT id, disenador, nombre, precio, imagen, condicion, temporada FROM productos WHERE bestseller = 1 ORDER BY id');
$stmtBest->execute();
$bestsellers = $stmtBest->fetchAll();

$stmtTotal = $pdo->query('SELECT COUNT(*) as total FROM productos');
$totalPiezas = $stmtTotal->fetch()['total'];
?>

<section class="hero-santoro">
    <div class="hero-image-block"></div>
    <div class="hero-copy reveal">
        <p class="hero-eyebrow">Selected Pieces — Autumn 2026</p>
        <h1>SANTORO.</h1>
        <p class="hero-santoro-sub">Verified luxury. Street-worn attitude — curated, not manufactured.</p>
        <a href="#best-sellers" class="hero-cta">View the Archive (<?php echo $totalPiezas; ?> pieces)</a>
    </div>
</section>

<section class="seccion-genero" id="best-sellers">
    <div class="seccion-head reveal">
        <h2>Best Sellers</h2>
        <span><?php echo count($bestsellers); ?> pieces</span>
    </div>
    <div class="grid-productos grid-asimetrico">
        <?php foreach ($bestsellers as $i => $p) render_producto($p, patron_tamano($i)); ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
