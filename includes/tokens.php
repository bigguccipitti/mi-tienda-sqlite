<?php
/**
 * tokens.php — Tokens únicos para /qc.php?token=XYZ
 *
 * Usa random_bytes() (CSPRNG), NO uniqid() ni rand() — esos son
 * predecibles y un atacante podría adivinar el token de otro cliente
 * y ver/aprobar/rechazar su pedido.
 */

function generar_token_qc_seguro(): string {
    return bin2hex(random_bytes(32)); // 64 caracteres hex, 256 bits de entropía
}
