<?php
/**
 * cron-auto-aprobar.php
 *
 * Se ejecuta cada hora (ver crontab-instrucciones.md). Revisa pedidos en
 * pending_qc cuyas 48 horas ya vencieron sin respuesta del cliente, y los
 * marca como auto_approved.
 *
 * IMPORTANTE: este script NO despacha nada por sí mismo — solo cambia el
 * estado en SQLite. El despacho real hacia KakoBuy sigue siendo manual
 * (tú ves en el dashboard que hay pedidos auto_approved y coordinas el
 * envío), ya que KakoBuy no tiene API. Lo que SÍ automatiza es el
 * "no dejar al cliente esperando indefinidamente" y el registro de auditoría.
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/eventos.php';
require_once __DIR__ . '/includes/mailer.php';

// Evita que dos ejecuciones se pisen si el cron tarda más de una hora
// alguna vez (usa un archivo de lock).
$lockFile = __DIR__ . '/data/cron-auto-aprobar.lock';
$lock = fopen($lockFile, 'c');
if (!flock($lock, LOCK_EX | LOCK_NB)) {
    fwrite(STDOUT, "Ya hay una ejecución en curso. Saliendo.\n");
    exit(0);
}

try {
    $ahora = date('Y-m-d H:i:s');

    $stmtVencidos = $pdo->prepare(
        "SELECT id, shopify_order_id, cliente_email, cliente_nombre FROM pedidos
         WHERE estado = 'pending_qc'
           AND qc_fotos_subidas_en IS NOT NULL
           AND qc_vence_en IS NOT NULL
           AND qc_vence_en <= ?"
    );
    $stmtVencidos->execute([$ahora]);
    $vencidos = $stmtVencidos->fetchAll();

    if (empty($vencidos)) {
        fwrite(STDOUT, "[$ahora] Sin pedidos vencidos.\n");
    }

    $stmtActualizar = $pdo->prepare(
        "UPDATE pedidos SET estado = 'auto_approved', qc_respondido_en = ? WHERE id = ?"
    );

    foreach ($vencidos as $pedido) {
        $stmtActualizar->execute([$ahora, $pedido['id']]);
        registrar_evento(
            $pdo,
            $pedido['id'],
            'auto_approved',
            "Sin respuesta del cliente tras " . QC_HORAS_LIMITE . "h. Auto-aprobado por cron."
        );
        fwrite(STDOUT, "[$ahora] Pedido #{$pedido['id']} (Shopify #{$pedido['shopify_order_id']}) auto-aprobado.\n");

        try {
            enviar_correo_auto_aprobado($pedido['cliente_email'], $pedido['cliente_nombre'], $pedido['shopify_order_id']);
            fwrite(STDOUT, "[$ahora]   Correo de auto-aprobación enviado a {$pedido['cliente_email']}.\n");
        } catch (\Throwable $eCorreo) {
            // Si el correo falla, el pedido YA quedó auto_approved (no se revierte);
            // solo se registra el fallo para que lo revises manualmente.
            registrar_evento($pdo, $pedido['id'], 'auto_approved_email_fallo', $eCorreo->getMessage());
            fwrite(STDERR, "[$ahora]   ERROR enviando correo a {$pedido['cliente_email']}: {$eCorreo->getMessage()}\n");
        }
    }
} finally {
    flock($lock, LOCK_UN);
    fclose($lock);
}
