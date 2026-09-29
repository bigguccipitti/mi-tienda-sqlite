cat << 'EOF' | sudo tee /var/www/html/mi-tienda-sqlite/admin/index.php > /dev/null
<?php
require_once __DIR__ . '/../includes/auth.php';

if (!admin_esta_logueado()) {
    header('Location: login.php');
    exit;
}

$nombre_usuario = $_SESSION['usuario'] ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SANTORO. — Dashboard Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin.css">
    <style>
        :root {
            --bg-main: #0a0a0a;
            --bg-card: #141414;
            --border-color: #262626;
            --text-primary: #ffffff;
            --text-secondary: #a1a1aa;
            --accent-danger: #ef4444;
        }
        body {
            margin: 0;
            padding: 0;
            background-color: var(--bg-main);
            color: var(--text-primary);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 240px;
            background: #000000;
            border-right: 1px solid var(--border-color);
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .brand {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: 2px;
            margin-bottom: 2.5rem;
            color: #fff;
        }
        .menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .menu li { margin-bottom: 0.5rem; }
        .menu a {
            color: var(--text-secondary);
            text-decoration: none;
            display: block;
            padding: 0.75rem 1rem;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .menu a:hover, .menu a.active {
            background: var(--bg-card);
            color: #fff;
        }
        .main-content {
            flex: 1;
            padding: 2.5rem;
            overflow-y: auto;
        }
        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1.5rem;
        }
        .card h3 {
            margin: 0 0 0.5rem 0;
            font-size: 0.85rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .card .number {
            font-size: 1.8rem;
            font-weight: 700;
        }
        .table-container {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1.5rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.9rem;
        }
        th, td {
            padding: 0.85rem;
            border-bottom: 1px solid var(--border-color);
        }
        th { color: var(--text-secondary); font-weight: 500; }
        .btn-logout {
            display: inline-block;
            color: var(--accent-danger);
            text-decoration: none;
            padding: 0.6rem 1rem;
            border: 1px solid var(--accent-danger);
            border-radius: 6px;
            font-size: 0.85rem;
            text-align: center;
            transition: 0.2s;
        }
        .btn-logout:hover {
            background: var(--accent-danger);
            color: #fff;
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div>
            <div class="brand">SANTORO.</div>
            <ul class="menu">
                <li><a href="index.php" class="active">Dashboard</a></li>
                <li><a href="productos.php">Productos</a></li>
                <li><a href="pedidos.php">Pedidos</a></li>
                <li><a href="usuarios.php">Usuarios</a></li>
            </ul>
        </div>
        <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
    </aside>

    <main class="main-content">
        <header class="header-bar">
            <div>
                <h1 style="margin:0; font-size:1.6rem;">Panel de Control</h1>
                <p style="margin:0.25rem 0 0 0; color:var(--text-secondary);">Bienvenido, <?php echo htmlspecialchars($nombre_usuario); ?></p>
            </div>
        </header>

        <section class="grid">
            <div class="card">
                <h3>Ventas Totales</h3>
                <div class="number">$0.00</div>
            </div>
            <div class="card">
                <h3>Pedidos Pendientes</h3>
                <div class="number">0</div>
            </div>
            <div class="card">
                <h3>Productos Activos</h3>
                <div class="number">0</div>
            </div>
        </section>

        <section class="table-container">
            <h3 style="margin-top:0;">Actividad Reciente</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Estado</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4" style="text-align:center; color:var(--text-secondary); padding: 2rem;">No hay pedidos recientes registrados.</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
EOF