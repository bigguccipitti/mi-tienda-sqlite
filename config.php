<?php
/**
 * config.php
 *
 * IMPORTANTE: Este archivo NO contiene secretos — solo LEE variables de entorno
 * que configuras en el servidor (fuera de Git). Así, aunque el repositorio sea
 * público o se filtre, el token de Shopify y las contraseñas nunca quedan expuestos.
 *
 * Cómo configurar las variables de entorno en Ubuntu con Apache:
 *   sudo nano /etc/apache2/envvars
 *   (agrega al final, ver plantilla en .env.example)
 *   export SHOPIFY_STORE_DOMAIN="tu-tienda.myshopify.com"
 *   export SHOPIFY_ADMIN_TOKEN="shpat_xxxxxxxxxxxx"
 *   export SANTORO_MAIL_HOST="smtp.tu-proveedor.com"
 *   export SANTORO_MAIL_USER="..."
 *   export SANTORO_MAIL_PASS="..."
 *   export SANTORO_ADMIN_SESSION_KEY="una-cadena-aleatoria-larga"
 *   sudo systemctl restart apache2
 *
 * Alternativa (si prefieres no tocar Apache): usar un archivo .env.local
 * fuera del repo (ver carga con parse_ini_file más abajo) — asegúrate de
 * agregar ".env.local" a .gitignore.
 */

// --- Intenta cargar un .env.local si existe (para desarrollo local) ---
$envLocalPath = __DIR__ . '/.env.local';
if (file_exists($envLocalPath)) {
    $valores = parse_ini_file($envLocalPath);
    foreach ($valores as $clave => $valor) {
        if (getenv($clave) === false) {
            putenv("$clave=$valor");
        }
    }
}

function env_requerido(string $clave): string {
    $valor = getenv($clave);
    if ($valor === false || $valor === '') {
        throw new RuntimeException("Falta la variable de entorno requerida: $clave. Revisa config.php.");
    }
    return $valor;
}

function env_opcional(string $clave, string $default = ''): string {
    $valor = getenv($clave);
    return $valor === false ? $default : $valor;
}

// ---------- URL BASE (cambia UNA línea cuando tengas dominio definitivo) ----------
define('BASE_URL', env_opcional('SANTORO_BASE_URL', 'https://tu-url-temporal.trycloudflare.com'));

// ---------- Shopify Admin API ----------
// Se leen bajo demanda (no al cargar config.php) para que páginas que no
// necesitan Shopify no fallen si la variable no está configurada todavía.
function shopify_store_domain(): string { return env_requerido('SHOPIFY_STORE_DOMAIN'); }
function shopify_admin_token(): string { return env_requerido('SHOPIFY_ADMIN_TOKEN'); }
define('SHOPIFY_API_VERSION', '2024-10');

// ---------- Correo transaccional ----------
function mail_host(): string { return env_requerido('SANTORO_MAIL_HOST'); }
function mail_user(): string { return env_requerido('SANTORO_MAIL_USER'); }
function mail_pass(): string { return env_requerido('SANTORO_MAIL_PASS'); }
define('MAIL_FROM_NAME', env_opcional('SANTORO_MAIL_FROM_NAME', 'SANTORO.'));
define('MAIL_FROM_ADDRESS', env_opcional('SANTORO_MAIL_FROM_ADDRESS', 'no-reply@santoro.example'));

// ---------- Motor de precios (valores por defecto, editables aquí) ----------
define('TARIFA_LOGISTICA_USD_POR_KG', 15.0);
define('MULTIPLICADOR_TEES', 2.5);
define('MULTIPLICADOR_DEFAULT', 3.0); // hoodies, pants, jackets, bags

// ---------- QC ----------
define('QC_HORAS_LIMITE', 48);
