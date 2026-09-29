# Capa 2 de seguridad para /admin/ — Basic Auth a nivel de Apache

Esto protege el panel admin con una segunda contraseña, ANTES de que la
petición siquiera llegue a PHP. Así, un bug en el login de PHP no es la
única barrera para entrar a /admin/.

## 1. Crear el archivo de contraseñas (una sola vez)

```bash
sudo apt install apache2-utils -y   # trae el comando htpasswd, si no lo tienes
sudo htpasswd -c /etc/apache2/.htpasswd-santoro-admin tu_usuario
```

Te pedirá la contraseña. Si más adelante quieres agregar un segundo
usuario, usa el mismo comando SIN el `-c` (para no sobreescribir el
archivo):

```bash
sudo htpasswd /etc/apache2/.htpasswd-santoro-admin otro_usuario
```

## 2. Proteger la carpeta /admin/ en la configuración de Apache

Edita el archivo de tu sitio (ajusta la ruta si es distinta):

```bash
sudo nano /etc/apache2/sites-available/mitienda.conf
```

Agrega este bloque DENTRO de tu `<VirtualHost>`:

```apache
<Directory /var/www/html/mi-tienda-sqlite/admin>
    AuthType Basic
    AuthName "SANTORO. Admin — acceso restringido"
    AuthUserFile /etc/apache2/.htpasswd-santoro-admin
    Require valid-user
</Directory>
```

## 3. Aplicar los cambios

```bash
sudo apache2ctl configtest
sudo systemctl reload apache2
```

## Resultado

A partir de ahora, entrar a `https://tu-dominio.com/admin/` pedirá
PRIMERO la contraseña de Apache (ventana emergente del navegador), y
DESPUÉS el login de PHP que ya construimos. Dos contraseñas distintas,
dos sistemas distintos — si uno falla, el otro sigue protegiendo.
