<?php
require_once __DIR__ . '/../includes/auth.php';
admin_requerir_sesion();

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/pricing.php';
require_once __DIR__ . '/../includes/shopify.php';
require_once __DIR__ . '/../includes/eventos.php';

$error = '';
$exito = '';
$previsualizacion = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $coleccion       = trim($_POST['coleccion'] ?? '');
    $nombre          = trim($_POST['nombre'] ?? '');
    $descripcion     = trim($_POST['descripcion'] ?? '');
    $imagen          = trim($_POST['imagen'] ?? '');
    $genero          = $_POST['genero'] ?? 'Unisex';
    $categoria       = trim($_POST['categoria'] ?? '');
    $costoOrigenRmb  = (float)($_POST['costo_origen_rmb'] ?? 0);
    $pesoGramos      = (int)($_POST['peso_gramos'] ?? 0);
    $tipoCambio      = (float)($_POST['tipo_cambio'] ?? 0);
    $multiplicadorManual = isset($_POST['multiplicador_manual']) && $_POST['multiplicador_manual'] !== ''
        ? (float)$_POST['multiplicador_manual']
        : null;
    $accion = $_POST['accion'] ?? 'calcular'; // 'calcular' (solo preview) o 'publicar' (guarda + Shopify)

    try {
        if ($coleccion === '' || $nombre === '' || $categoria === '') {
            throw new InvalidArgumentException('Colección, nombre y categoría son obligatorios.');
        }

        $calculo = calcular_precio_venta($costoOrigenRmb, $pesoGramos, $tipoCambio, $categoria, $multiplicadorManual);
        $previsualizacion = $calculo;

        if ($accion === 'publicar') {
            // 1. Guardar en SQLite (precio CONGELADO — no se recalcula solo después)
            $stmt = $pdo->prepare(
                'INSERT INTO productos
                 (coleccion, nombre, descripcion, imagen, genero, categoria,
                  costo_origen_rmb, peso_gramos, tipo_cambio_usado, costo_logistica_usd,
                  multiplicador_ganancia, precio_venta_usd)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $coleccion, $nombre, $descripcion, $imagen, $genero, $categoria,
                $costoOrigenRmb, $pesoGramos, $tipoCambio, $calculo['costo_logistica_usd'],
                $calculo['multiplicador_usado'], $calculo['precio_venta_usd'],
            ]);
            $productoId = (int)$pdo->lastInsertId();

            // 2. Crear en Shopify
            $productoParaShopify = [
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'categoria' => $categoria,
                'precio_venta_usd' => $calculo['precio_venta_usd'],
                'stock' => 10,
            ];
            $resultadoShopify = shopify_crear_producto($productoParaShopify);

            // 3. Guardar los IDs de Shopify de vuelta en SQLite
            $stmtUpdate = $pdo->prepare(
                'UPDATE productos SET shopify_product_id = ?, shopify_variant_id = ? WHERE id = ?'
            );
            $stmtUpdate->execute([
                $resultadoShopify['product_id'],
                $resultadoShopify['variant_id'],
                $productoId,
            ]);

            registrar_evento($pdo, null, 'producto_creado', "Producto #$productoId ($nombre) creado y sincronizado con Shopify (product_id: {$resultadoShopify['product_id']}).");

            $exito = "Producto \"$nombre\" creado — precio final \${$calculo['precio_venta_usd']} USD. Sincronizado con Shopify (ID: {$resultadoShopify['product_id']}).";
        }
    } catch (\Throwable $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SANTORO. — Nuevo producto</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">
<div class="admin-wrap">
    <nav class="admin-nav">
        <a href="index.php">Dashboard</a>
        <a href="nuevo-producto.php" class="activo">Nuevo producto</a>
        <a href="qc.php">Panel QC</a>
        <a href="logout.php">Salir</a>
    </nav>

    <h1>Nuevo producto</h1>

    <?php if ($error): ?><p class="mensaje-error" role="alert"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
    <?php if ($exito): ?><p class="mensaje-exito" role="status"><?php echo htmlspecialchars($exito); ?></p><?php endif; ?>

    <form method="post" class="form-admin">
        <div class="grid-2">
            <label>Colección
                <input type="text" name="coleccion" placeholder="Studio Line 01" required value="<?php echo htmlspecialchars($_POST['coleccion'] ?? ''); ?>">
            </label>
            <label>Categoría
                <select name="categoria" required>
                    <?php foreach (['Tees', 'Hoodies', 'Pants', 'Jackets', 'Bags'] as $cat): ?>
                        <option value="<?php echo $cat; ?>" <?php echo (($_POST['categoria'] ?? '') === $cat) ? 'selected' : ''; ?>><?php echo $cat; ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>

        <label>Nombre técnico/neutro
            <input type="text" name="nombre" placeholder="Heavyweight Washed Vintage Hoodie — 500 GSM" required value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>">
        </label>

        <label>Descripción (construcción, costura, fit, composición)
            <textarea name="descripcion" rows="4"><?php echo htmlspecialchars($_POST['descripcion'] ?? ''); ?></textarea>
        </label>

        <div class="grid-2">
            <label>Género
                <select name="genero">
                    <option value="Unisex">Unisex</option>
                    <option value="Men">Men</option>
                    <option value="Women">Women</option>
                </select>
            </label>
            <label>Imagen (nombre de archivo)
                <input type="text" name="imagen" placeholder="hoodie-01.jpg" value="<?php echo htmlspecialchars($_POST['imagen'] ?? ''); ?>">
            </label>
        </div>

        <h2>Costeo</h2>
        <div class="grid-3">
            <label>Costo origen (RMB)
                <input type="number" step="0.01" name="costo_origen_rmb" required value="<?php echo htmlspecialchars($_POST['costo_origen_rmb'] ?? ''); ?>">
            </label>
            <label>Peso (gramos)
                <input type="number" name="peso_gramos" required value="<?php echo htmlspecialchars($_POST['peso_gramos'] ?? ''); ?>">
            </label>
            <label>Tipo de cambio (RMB por 1 USD)
                <input type="number" step="0.0001" name="tipo_cambio" required value="<?php echo htmlspecialchars($_POST['tipo_cambio'] ?? '7.2'); ?>">
            </label>
        </div>

        <label>Multiplicador manual (opcional — deja vacío para usar el default por categoría: 2.5x Tees, 3.0x el resto)
            <input type="number" step="0.1" name="multiplicador_manual" value="<?php echo htmlspecialchars($_POST['multiplicador_manual'] ?? ''); ?>">
        </label>

        <?php if ($previsualizacion): ?>
            <div class="previsualizacion-precio">
                <h2>Previsualización</h2>
                <dl class="ficha-tecnica">
                    <div><dt>Costo origen USD</dt><dd>$<?php echo number_format($previsualizacion['costo_origen_usd'], 2); ?></dd></div>
                    <div><dt>Costo logística USD</dt><dd>$<?php echo number_format($previsualizacion['costo_logistica_usd'], 2); ?></dd></div>
                    <div><dt>Costo base USD</dt><dd>$<?php echo number_format($previsualizacion['costo_base_usd'], 2); ?></dd></div>
                    <div><dt>Multiplicador usado</dt><dd><?php echo $previsualizacion['multiplicador_usado']; ?>x</dd></div>
                </dl>
                <p class="precio-final">Precio de venta final: $<?php echo number_format($previsualizacion['precio_venta_usd'], 2); ?> USD</p>
            </div>
        <?php endif; ?>

        <div class="acciones-form">
            <button type="submit" name="accion" value="calcular" class="boton-secundario">Solo calcular (preview)</button>
            <button type="submit" name="accion" value="publicar">Publicar y sincronizar con Shopify</button>
        </div>
    </form>
</div>
</body>
</html>
