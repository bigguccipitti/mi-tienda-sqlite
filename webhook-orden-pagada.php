<?php
/**
 * webhook-orden-pagada.php
 *
 * Configúralo en Shopify: Settings → Notifications → Webhooks →
 * Evento: "Order payment" (orders/paid) → URL: BASE_URL/webhook-orden-pagada.php
 * Copia el "Webhook signing secret" que te da Shopify a SHOPIFY_WEBHOOK_SECRET.
 *
 * Este endpoint SOLO crea el registro del pedido en SQLite con estado
 * pending_qc (sin fotos todavía). Las fotos las sube el admin manualmente
 * en admin/qc.php cuando KakoBuy las envíe.
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/webhook_seguridad.php';
require_once __DIR__ . '/includes/eventos.php';

header('Content-Type: application/json');

$payloadRaw = file_get_contents('php://input');
$hmacRecibido = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'] ?? '';

if (!$hmacRecibido || !verificar_firma_webhook_shopify($payloadRaw, $hmacRecibido)) {
    http_response_code(401);
    echo json_encode(['error' => 'Firma inválida.']);
    exit;
}

$datos = json_decode($payloadRaw, true);
if (!$datos || empty($datos['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Payload inválido.']);
    exit;
}

$shopifyOrderId = (string)$datos['id'];
$email = $datos['email'] ?? ($datos['customer']['email'] ?? null);
$nombreCliente = trim(
    ($datos['customer']['first_name'] ?? '') . ' ' . ($datos['customer']['last_name'] ?? '')
) ?: 'Cliente';

if (!$email) {
    http_response_code(400);
    echo json_encode(['error' => 'La orden no tiene email de cliente.']);
    exit;
}

try {
    // idempotencia: si Shopify reintenta el webhook, no dupliques el pedido
    $stmtExiste = $pdo->prepare('SELECT id FROM pedidos WHERE shopify_order_id = ?');
    $stmtExiste->execute([$shopifyOrderId]);
    if ($stmtExiste->fetch()) {
        http_response_code(200);
        echo json_encode(['status' => 'ya existía, ignorado']);
        exit;
    }

    $stmt = $pdo->prepare(
        'INSERT INTO pedidos (shopify_order_id, cliente_email, cliente_nombre, estado)
         VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$shopifyOrderId, $email, $nombreCliente, 'pending_qc']);
    $pedidoId = (int)$pdo->lastInsertId();

    registrar_evento($pdo, $pedidoId, 'orden_recibida', "Orden Shopify #$shopifyOrderId creada desde webhook.");

    http_response_code(200);
    echo json_encode(['status' => 'ok', 'pedido_id' => $pedidoId]);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno.']);
    // En producción: registra $e->getMessage() en un log de errores, no lo devuelvas al cliente.
}
