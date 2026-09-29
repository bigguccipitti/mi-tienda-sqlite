<?php
/**
 * webhook_seguridad.php — Verifica que un webhook realmente viene de Shopify.
 *
 * Sin esto, cualquiera podría hacer un POST a webhook-orden-pagada.php
 * simulando una orden pagada falsa (o alterando el monto/email).
 * Shopify firma cada webhook con HMAC-SHA256 usando el secreto que tú
 * defines al crear el webhook — verificamos esa firma aquí.
 */

require_once __DIR__ . '/../config.php';

function shopify_webhook_secret(): string {
    return env_requerido('SHOPIFY_WEBHOOK_SECRET');
}

/**
 * @param string $payloadRaw   El cuerpo crudo de la petición (file_get_contents('php://input'))
 * @param string $hmacRecibido El valor del header 'X-Shopify-Hmac-Sha256'
 */
function verificar_firma_webhook_shopify(string $payloadRaw, string $hmacRecibido): bool {
    $hmacCalculado = base64_encode(
        hash_hmac('sha256', $payloadRaw, shopify_webhook_secret(), true)
    );

    // hash_equals() compara en tiempo constante — evita timing attacks.
    return hash_equals($hmacCalculado, $hmacRecibido);
}
