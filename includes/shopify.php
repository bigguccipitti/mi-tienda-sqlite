<?php
/**
 * shopify.php — Wrapper mínimo de la Shopify Admin API (REST).
 *
 * Requiere una Custom App con scopes: read_products, write_products,
 * read_orders, write_orders, write_refunds (ya los confirmaste creados).
 */

require_once __DIR__ . '/../config.php';

class ShopifyApiException extends RuntimeException {}

/**
 * Hace una llamada HTTP a la Admin API de Shopify con manejo de errores.
 */
function shopify_request(string $metodo, string $ruta, ?array $cuerpo = null): array {
    $dominio = shopify_store_domain();
    $token   = shopify_admin_token();
    $url = "https://{$dominio}/admin/api/" . SHOPIFY_API_VERSION . "/{$ruta}";

    $ch = curl_init($url);
    $headers = [
        'X-Shopify-Access-Token: ' . $token,
        'Content-Type: application/json',
    ];

    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST  => $metodo,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    if ($cuerpo !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($cuerpo));
    }

    $respuestaRaw = curl_exec($ch);
    $curlError = curl_error($ch);
    $codigoHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($respuestaRaw === false) {
        throw new ShopifyApiException("Error de conexión con Shopify: $curlError");
    }

    $respuesta = json_decode($respuestaRaw, true);

    if ($codigoHttp >= 400) {
        $mensaje = $respuesta['errors'] ?? $respuestaRaw;
        throw new ShopifyApiException(
            "Shopify respondió $codigoHttp: " . (is_array($mensaje) ? json_encode($mensaje) : $mensaje)
        );
    }

    return $respuesta ?? [];
}

/**
 * Crea un producto en Shopify a partir de una fila de la tabla `productos`.
 * Devuelve ['product_id' => ..., 'variant_id' => ...]
 */
function shopify_crear_producto(array $producto): array {
    $cuerpo = [
        'product' => [
            'title'       => $producto['nombre'],
            'body_html'   => nl2br(htmlspecialchars($producto['descripcion'] ?? '')),
            'vendor'      => 'SANTORO.',
            'product_type'=> $producto['categoria'],
            'status'      => 'active',
            'variants'    => [[
                'price'                => number_format($producto['precio_venta_usd'], 2, '.', ''),
                'inventory_management' => 'shopify',
                'inventory_quantity'   => (int)($producto['stock'] ?? 10),
            ]],
        ],
    ];

    $respuesta = shopify_request('POST', 'products.json', $cuerpo);

    if (empty($respuesta['product']['id'])) {
        throw new ShopifyApiException('Shopify no devolvió un product_id al crear el producto.');
    }

    $productId = $respuesta['product']['id'];
    $variantId = $respuesta['product']['variants'][0]['id'] ?? null;

    return ['product_id' => $productId, 'variant_id' => $variantId];
}

/**
 * Dispara un reembolso total de una orden (usado cuando el cliente
 * rechaza el QC). Requiere el order_id de Shopify.
 */
function shopify_reembolsar_orden(string $shopifyOrderId): array {
    // Primero se consulta la orden para saber el monto exacto pagado (transactions).
    $orden = shopify_request('GET', "orders/{$shopifyOrderId}/transactions.json");

    $transaccionOriginal = null;
    foreach ($orden['transactions'] ?? [] as $t) {
        if ($t['kind'] === 'sale' || $t['kind'] === 'capture') {
            $transaccionOriginal = $t;
            break;
        }
    }

    if (!$transaccionOriginal) {
        throw new ShopifyApiException('No se encontró una transacción original que reembolsar para esta orden.');
    }

    $cuerpo = [
        'refund' => [
            'transactions' => [[
                'parent_id' => $transaccionOriginal['id'],
                'amount'    => $transaccionOriginal['amount'],
                'kind'      => 'refund',
            ]],
        ],
    ];

    return shopify_request('POST', "orders/{$shopifyOrderId}/refunds.json", $cuerpo);
}

/**
 * Marca la orden como cumplida/enviada en Shopify con el número de tracking.
 */
function shopify_marcar_enviado(string $shopifyOrderId, string $trackingNumber): array {
    // Requiere el fulfillment_order_id asociado — se consulta primero.
    $fulfillmentOrders = shopify_request('GET', "orders/{$shopifyOrderId}/fulfillment_orders.json");
    $fulfillmentOrderId = $fulfillmentOrders['fulfillment_orders'][0]['id'] ?? null;

    if (!$fulfillmentOrderId) {
        throw new ShopifyApiException('No se encontró un fulfillment_order para esta orden.');
    }

    $cuerpo = [
        'fulfillment' => [
            'line_items_by_fulfillment_order' => [
                ['fulfillment_order_id' => $fulfillmentOrderId],
            ],
            'tracking_info' => [
                'number' => $trackingNumber,
                'company'=> 'KakoBuy / Línea Internacional',
            ],
            'notify_customer' => true,
        ],
    ];

    return shopify_request('POST', 'fulfillments.json', $cuerpo);
}
