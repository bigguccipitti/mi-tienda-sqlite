# Instalar el cron job de auto-aprobación (48 horas)

Corre cada hora, revisa pedidos vencidos y los marca `auto_approved`.
Debe correr como `www-data` (el mismo usuario que Apache) para que tenga
permiso de escritura sobre `data/tienda.sqlite`.

## 1. Editar el crontab de www-data

```bash
sudo crontab -u www-data -e
```

## 2. Agregar esta línea (ajusta la ruta si tu proyecto vive en otro lugar)

```cron
0 * * * * /usr/bin/php /var/www/html/mi-tienda-sqlite/cron-auto-aprobar.php >> /var/www/html/mi-tienda-sqlite/data/cron.log 2>&1
```

Esto lo corre en el minuto 0 de cada hora (1:00, 2:00, 3:00...).

## 3. Verificar que el log se está generando

Después de la primera hora:

```bash
tail -f /var/www/html/mi-tienda-sqlite/data/cron.log
```

Deberías ver líneas como:
```
[2026-09-26 15:00:01] Sin pedidos vencidos.
```

## 4. Verificar que www-data puede escribir la base de datos

```bash
sudo -u www-data php /var/www/html/mi-tienda-sqlite/cron-auto-aprobar.php
```

Si esto corre sin error de permisos, el cron real también funcionará.

## Nota sobre precisión

Un cron que corre "cada hora" no revisa exactamente a las 48h00m00s —
puede haber hasta ~59 minutos de margen (ej. si venció a las 14:05, el
cron de las 15:00 lo detecta, no el de las 14:00). Si necesitas mayor
precisión, cambia `0 * * * *` por `*/15 * * * *` (cada 15 minutos) — el
script ya soporta correr con más frecuencia sin problema, gracias al lock.
