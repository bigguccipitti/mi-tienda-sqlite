# Mi Tienda — Proyecto de práctica (PHP + SQLite)

Versión con **SQLite** en lugar de MariaDB/MySQL — no necesitas instalar ni compilar ningún servidor de base de datos. SQLite ya viene incluido con PHP.

## Estructura

```
mi-tienda-sqlite/
├── instalar.php         # Ejecutar UNA VEZ para crear la base de datos
├── index.php             # Página principal (cuadrícula de productos)
├── product.php            # Detalle de un producto
├── cart.php                # Carrito de compras (usa sesiones PHP)
├── checkout.php            # Formulario de "pago" de práctica
├── includes/
│   ├── db.php                 # Conexión a la base de datos (SQLite)
│   ├── header.php             # Encabezado compartido
│   └── footer.php              # Pie de página compartido
├── css/style.css             # Estilos
├── js/cart.js                 # Espacio para JS adicional
├── images/productos/         # Aquí van las imágenes de los productos
└── data/
    ├── productos.sql            # SQL para crear tabla y productos de ejemplo
    └── tienda.sqlite            # (se crea automáticamente al instalar)
```

## Pasos para correrlo

### 1. Verifica que PHP tenga soporte SQLite (normalmente ya lo trae)
```bash
php -m | grep sqlite
```
Deberías ver `pdo_sqlite` y `sqlite3` en la lista.

### 2. Crear la base de datos (solo la primera vez)
Desde la carpeta `mi-tienda-sqlite/`:
```bash
php instalar.php
```
Esto crea el archivo `data/tienda.sqlite` con la tabla `productos` y 4 productos de ejemplo.

### 3. Agregar imágenes de ejemplo
Coloca imágenes llamadas `camiseta.png`, `tenis.png`, `gorra.png`, `mochila.png` dentro de `images/productos/`. Puedes usar cualquier imagen placeholder mientras practicas.

### 4. Levantar el servidor local de PHP
```bash
php -S localhost:8000
```

### 5. Abrir en el navegador
```
http://localhost:8000/index.php
```

## Diferencias con la versión MySQL
- No necesitas `brew install mariadb` ni tener el servicio corriendo.
- La base de datos es un solo archivo (`data/tienda.sqlite`), fácil de borrar y recrear si algo sale mal (solo borra el archivo y vuelve a correr `php instalar.php`).
- La sintaxis SQL es casi idéntica — lo que cambia son detalles como `AUTOINCREMENT` en vez de `AUTO_INCREMENT`.

## Qué practica cada archivo
- **index.php** → consultas SQL (`SELECT`), bucles PHP, mostrar datos dinámicos
- **product.php** → parámetros por URL (`$_GET`), consultas con parámetros preparados
- **cart.php** → sesiones PHP (`$_SESSION`), lógica de agregar/quitar
- **checkout.php** → formularios (`$_POST`), validación básica

## Siguientes pasos sugeridos
1. Agregar más productos editando `data/productos.sql` y volviendo a correr `instalar.php` (borra `tienda.sqlite` primero si quieres empezar de cero).
2. Agregar validaciones (ej. no permitir agregar si no hay stock).
3. Si más adelante instalas Xcode completo, puedes migrar a MySQL/MariaDB sin cambiar casi nada de la lógica — solo `includes/db.php`.
4. Cuando quieras subir esto a producción, considerar Shopify para la parte de tienda y usar PHP solo para funciones extra.
