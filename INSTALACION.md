# SANTORO. — Sistema QC + Pricing + Shopify — Guía de instalación completa

## 1. Variables de entorno

```bash
cp .env.example .env.local
nano .env.local   # llena todos los valores reales
```

## 2. Instalar PHPMailer

```bash
composer install
```

Si no tienes Composer en el servidor:
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

## 3. Crear la base de datos

```bash
sqlite3 data/tienda.sqlite < data/esquema.sql
```

## 4. Crear tu primer usuario admin (por terminal, nunca por web)

```bash
php crear-admin-cli.php tu_usuario
```

## 5. Configurar el webhook en Shopify

Panel de Shopify → Settings → Notifications → Webhooks → Create webhook
- Event: **Order payment** (orders/paid)
- Format: JSON
- URL: `https://tu-dominio.com/webhook-orden-pagada.php`

Copia el **Signing secret** que te muestra Shopify → pégalo en `.env.local` como `SHOPIFY_WEBHOOK_SECRET`.

## 6. Proteger /admin/ con Basic Auth (capa 2 de seguridad)

Sigue `admin/PROTEGER-CON-APACHE.md`.

## 7. Instalar el cron job

Sigue `crontab-instrucciones.md`.

## 8. Permisos

```bash
sudo chown -R www-data:www-data /var/www/html/mi-tienda-sqlite
sudo chmod -R 755 /var/www/html/mi-tienda-sqlite
sudo chmod -R 775 /var/www/html/mi-tienda-sqlite/data
```

## Flujo de prueba de extremo a extremo

1. Crea un producto de prueba en `/admin/nuevo-producto.php` → confirma que aparece en Shopify.
2. Haz una compra de prueba real en tu tienda Shopify (usa el modo de pago de prueba de Shopify si está disponible en tu plan).
3. Confirma que el webhook creó la fila en `pedidos` (revisa `/admin/index.php`).
4. En `/admin/qc.php`, sube 2-3 URLs de fotos de ejemplo → confirma que llega el correo.
5. Abre el link `/qc.php?token=...` del correo → prueba "Approve" y "Reject" (en pedidos de prueba separados).
6. Para probar el cron sin esperar 48h: en SQLite, cambia manualmente `qc_vence_en` de un pedido a una fecha pasada, y corre `php cron-auto-aprobar.php` manualmente.

## Lo que sigue siendo manual (por diseño, ya que KakoBuy no tiene API)

- Copiar las URLs de fotos QC desde el dashboard de KakoBuy a `/admin/qc.php`.
- Copiar el número de tracking desde KakoBuy a `/admin/qc.php` cuando despachen.
- Decirle a KakoBuy que despache cuando veas un pedido `approved` o `auto_approved` en el dashboard.
