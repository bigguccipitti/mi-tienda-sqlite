<?php
// functions.php — Funciones compartidas entre index.php, men.php, women.php, product.php

/**
 * Dibuja una tarjeta de producto con datos de archivo (número de catálogo,
 * diseñador, condición) — no solo nombre/precio genéricos.
 *
 * @param array  $p         Fila de la tabla productos
 * @param string $sizeClass Clase opcional para variar el tamaño en grids asimétricos
 */
function render_producto($p, $sizeClass = '') {
    $numeroArchivo = str_pad($p['id'], 3, '0', STR_PAD_LEFT);
    ?>
    <div class="tarjeta-producto reveal <?php echo htmlspecialchars($sizeClass); ?>">
        <a href="product.php?id=<?php echo $p['id']; ?>">
            <div class="frame">
                <span class="codigo-archivo">N° <?php echo $numeroArchivo; ?></span>
                <img
                    src="images/productos/<?php echo htmlspecialchars($p['imagen']); ?>"
                    alt="<?php echo htmlspecialchars($p['disenador'] . ' — ' . $p['nombre']); ?>"
                    loading="lazy"
                >
            </div>
            <span class="designer"><?php echo htmlspecialchars($p['disenador']); ?></span>
            <h3><?php echo htmlspecialchars($p['nombre']); ?></h3>
            <div class="meta-linea">
                <span><?php echo htmlspecialchars($p['condicion'] ?? ''); ?> · <?php echo htmlspecialchars($p['temporada'] ?? ''); ?></span>
            </div>
            <p class="precio" data-precio="<?php echo $p['precio']; ?>">$<?php echo number_format($p['precio'], 2); ?></p>
        </a>
        <form method="post" action="cart.php">
            <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
            <button type="submit" name="agregar" class="boton">Add to Cart</button>
        </form>
    </div>
    <?php
}

/**
 * Devuelve una clase de tamaño repitiendo un patrón (para romper la grilla
 * pareja y que se sienta editorial, no una cuadrícula genérica de e-commerce).
 */
function patron_tamano($indice) {
    $patron = ['size-lg', 'size-sm', 'size-md', 'size-sm'];
    return $patron[$indice % count($patron)];
}
