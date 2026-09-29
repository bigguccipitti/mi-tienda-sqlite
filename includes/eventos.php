<?php
/**
 * eventos.php — Auditoría simple. Cada acción importante del sistema
 * (aprobación, rechazo, auto-aprobación, reembolso) queda registrada aquí.
 */

function registrar_evento(PDO $pdo, ?int $pedidoId, string $tipoEvento, string $detalle = ''): void {
    $stmt = $pdo->prepare(
        'INSERT INTO eventos_log (pedido_id, tipo_evento, detalle) VALUES (?, ?, ?)'
    );
    $stmt->execute([$pedidoId, $tipoEvento, $detalle]);
}
