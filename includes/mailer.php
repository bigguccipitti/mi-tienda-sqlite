<?php
/**
 * mailer.php — Envío de correo transaccional vía SMTP (PHPMailer).
 *
 * Requiere: composer install (ver composer.json) antes de usar este archivo.
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

/**
 * Envía el correo de "tus fotos de QC están listas" con el link único.
 *
 * @param array $fotos cada elemento: ['url' => ..., 'etiqueta' => ...]
 * @throws PHPMailerException si el envío falla
 */
function enviar_correo_qc_listo(string $email, string $nombreCliente, string $qcUrl, array $fotos): void {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = mail_host();
    $mail->SMTPAuth   = true;
    $mail->Username   = mail_user();
    $mail->Password   = mail_pass();
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
    $mail->addAddress($email, $nombreCliente);

    $mail->Subject = 'Tus fotos de inspección (QC) están listas — SANTORO.';

    $miniaturas = '';
    foreach ($fotos as $foto) {
        $urlFoto = htmlspecialchars($foto['url']);
        $etiqueta = htmlspecialchars($foto['etiqueta'] ?? '');
        $miniaturas .= "<div style='margin-bottom:12px;'><img src='{$urlFoto}' style='max-width:260px;display:block;margin-bottom:4px;'><span style='font-size:12px;color:#666;'>{$etiqueta}</span></div>";
    }

    $mail->isHTML(true);
    $mail->Body = "
        <div style='font-family:Arial,sans-serif;max-width:520px;margin:0 auto;'>
            <h2 style='font-weight:normal;'>SANTORO.</h2>
            <p>Hola " . htmlspecialchars($nombreCliente) . ",</p>
            <p>Tu prenda ya llegó a control de calidad. Estas son las fotos de inspección:</p>
            {$miniaturas}
            <p>Tienes <strong>48 horas</strong> para revisarlas y aprobar el envío o solicitar la devolución.
               Si no respondes en ese plazo, la orden se aprobará automáticamente y se despachará.</p>
            <p style='margin:24px 0;'>
                <a href='" . htmlspecialchars($qcUrl) . "' style='background:#141210;color:#fff;padding:12px 20px;text-decoration:none;display:inline-block;'>Revisar mis fotos</a>
            </p>
            <p style='font-size:12px;color:#888;'>Si el botón no funciona, copia este enlace: " . htmlspecialchars($qcUrl) . "</p>
        </div>
    ";
    $mail->AltBody = "Hola {$nombreCliente},\n\nTus fotos de inspección QC están listas. Revísalas aquí: {$qcUrl}\n\nTienes 48 horas para aprobar o solicitar devolución. Sin respuesta, se aprueba automáticamente.";

    $mail->send();
}

/**
 * Envía el correo de confirmación cuando el cron auto-aprueba un pedido
 * porque el cliente no respondió dentro de las 48 horas.
 */
function enviar_correo_auto_aprobado(string $email, string $nombreCliente, string $shopifyOrderId): void {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = mail_host();
    $mail->SMTPAuth   = true;
    $mail->Username   = mail_user();
    $mail->Password   = mail_pass();
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
    $mail->addAddress($email, $nombreCliente);

    $mail->Subject = 'Tu pedido fue aprobado automáticamente — SANTORO.';

    $mail->isHTML(true);
    $mail->Body = "
        <div style='font-family:Arial,sans-serif;max-width:520px;margin:0 auto;'>
            <h2 style='font-weight:normal;'>SANTORO.</h2>
            <p>Hola " . htmlspecialchars($nombreCliente) . ",</p>
            <p>No recibimos respuesta dentro de las 48 horas para revisar las fotos de inspección
               de tu orden #" . htmlspecialchars($shopifyOrderId) . ", así que según nuestra política,
               fue <strong>aprobada automáticamente</strong> y ya está en proceso de despacho.</p>
            <p>Si aún quieres reportar un problema con la pieza, contáctanos lo antes posible — una vez
               despachado el proceso de devolución es distinto al de esta ventana de inspección.</p>
        </div>
    ";
    $mail->AltBody = "Hola {$nombreCliente},\n\nNo recibimos respuesta dentro de las 48 horas, así que tu orden #{$shopifyOrderId} fue aprobada automáticamente y está en proceso de despacho.";

    $mail->send();
}
