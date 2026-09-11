<?php
// functions.php — Funciones compartidas entre index.php, men.php y women.php

function render_producto($p) {
    ?>
    <div class="tarjeta-producto">
        <a href="product.php?id=<?php echo $p['id']; ?>">
            <img
                src="images/productos/<?php echo htmlspecialchars($p['imagen']); ?>"
                alt="<?php echo htmlspecialchars($p['disenador'] . ' — ' . $p['nombre']); ?>"
            >
            <span class="designer"><?php echo htmlspecialchars($p['disenador']); ?></span>
            <h3><?php echo htmlspecialchars($p['nombre']); ?></h3>
            <p class="precio">$<?php echo number_format($p['precio'], 2); ?></p>
        </a>
        <form method="post" action="cart.php">
            <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
            <button type="submit" name="agregar" class="boton">Add to Cart</button>
        </form>
    </div>
    <?php
}
