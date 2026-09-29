cat << 'EOF' > /var/www/html/mi-tienda-sqlite/includes/functions.php
<?php
// includes/functions.php - Funciones auxiliares de SANTORO.

function sanitizar($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
EOF