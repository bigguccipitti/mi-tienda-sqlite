<?php
/**
 * pricing.php — Motor de precios de SANTORO.
 *
 * Fórmula (congelada por producto al momento de publicar):
 *   costo_logistica_usd = (peso_gramos / 1000) * TARIFA_LOGISTICA_USD_POR_KG
 *   precio_venta_usd    = ceil( ((costo_origen_rmb / tipo_cambio) + costo_logistica_usd) * multiplicador )
 *
 * El resultado SIEMPRE se redondea hacia ARRIBA al entero siguiente (.00),
 * nunca hacia abajo — así el margen mínimo garantizado nunca se erosiona
 * por redondeo, y el precio final es limpio (ej. $85.00, no $84.32).
 */

require_once __DIR__ . '/../config.php';

/**
 * Determina el multiplicador de ganancia según la categoría del producto.
 */
function multiplicador_por_categoria(string $categoria): float {
    $categoria = strtolower(trim($categoria));
    if (in_array($categoria, ['tees', 'tee', 'camisetas', 't-shirts'], true)) {
        return MULTIPLICADOR_TEES;
    }
    return MULTIPLICADOR_DEFAULT; // hoodies, pants, jackets, bags, etc.
}

/**
 * Calcula el costo logístico en USD a partir del peso en gramos.
 */
function calcular_costo_logistica_usd(int $pesoGramos): float {
    if ($pesoGramos <= 0) {
        throw new InvalidArgumentException('El peso en gramos debe ser mayor a 0.');
    }
    return ($pesoGramos / 1000) * TARIFA_LOGISTICA_USD_POR_KG;
}

/**
 * Calcula el precio de venta final en USD, redondeado hacia arriba a .00.
 *
 * @return array{
 *   costo_logistica_usd: float,
 *   costo_base_usd: float,
 *   precio_venta_usd: float,
 *   multiplicador_usado: float
 * }
 */
function calcular_precio_venta(
    float $costoOrigenRmb,
    int $pesoGramos,
    float $tipoCambio,
    string $categoria,
    ?float $multiplicadorManual = null
): array {
    if ($costoOrigenRmb <= 0) {
        throw new InvalidArgumentException('El costo de origen en RMB debe ser mayor a 0.');
    }
    if ($tipoCambio <= 0) {
        throw new InvalidArgumentException('El tipo de cambio debe ser mayor a 0.');
    }

    $costoLogisticaUsd = calcular_costo_logistica_usd($pesoGramos);
    $costoOrigenUsd    = $costoOrigenRmb / $tipoCambio;
    $costoBaseUsd      = $costoOrigenUsd + $costoLogisticaUsd;

    $multiplicador = $multiplicadorManual ?? multiplicador_por_categoria($categoria);

    $precioSinRedondear = $costoBaseUsd * $multiplicador;
    $precioVentaUsd     = ceil($precioSinRedondear); // redondeo HACIA ARRIBA, termina en .00

    return [
        'costo_origen_usd'     => round($costoOrigenUsd, 2),
        'costo_logistica_usd'  => round($costoLogisticaUsd, 2),
        'costo_base_usd'       => round($costoBaseUsd, 2),
        'multiplicador_usado'  => $multiplicador,
        'precio_venta_usd'     => $precioVentaUsd,
    ];
}
