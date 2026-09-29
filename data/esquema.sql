-- ============================================================
-- SANTORO. — Esquema de base de datos (Módulo QC + Pricing)
-- ============================================================

-- ---------- PRODUCTOS ----------
-- 'disenador' fue reemplazado por 'coleccion' (sin marcas registradas).
-- Los campos de costo permiten calcular y CONGELAR el precio de venta.
CREATE TABLE IF NOT EXISTS productos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    coleccion TEXT NOT NULL,                    -- ej. "Studio Line 01", "Archive 004"
    nombre TEXT NOT NULL,                       -- título técnico/neutro, ej. "Heavyweight Washed Vintage Hoodie — 500 GSM"
    descripcion TEXT,                           -- enfocado en construcción/costura/fit/composición
    imagen TEXT,
    genero TEXT NOT NULL DEFAULT 'Unisex',      -- Men / Women / Unisex
    categoria TEXT NOT NULL DEFAULT 'Tees',     -- Tees / Hoodies / Pants / Jackets / Bags ...

    -- ---- Costeo (todo lo que entra en la fórmula) ----
    costo_origen_rmb REAL NOT NULL,
    peso_gramos INTEGER NOT NULL,
    tipo_cambio_usado REAL NOT NULL,            -- congelado al momento de ingresar el producto
    costo_logistica_usd REAL NOT NULL,          -- calculado: (peso_gramos / 1000) * tarifa_por_kg
    multiplicador_ganancia REAL NOT NULL,       -- 2.5 (tees) o 3.0 (hoodies/pants/jackets) por defecto
    precio_venta_usd REAL NOT NULL,             -- resultado FINAL, congelado — no se recalcula solo

    -- ---- Sincronización con Shopify ----
    shopify_product_id TEXT,                    -- se llena tras crear el producto vía Admin API
    shopify_variant_id TEXT,

    bestseller INTEGER NOT NULL DEFAULT 0,
    stock INTEGER DEFAULT 10,
    creado_en TEXT NOT NULL DEFAULT (datetime('now'))
);

-- ---------- PEDIDOS ----------
-- Rastrea el ciclo de vida de cada orden: desde que llega de Shopify
-- hasta que el cliente aprueba/rechaza el QC o se auto-aprueba a las 48h.
CREATE TABLE IF NOT EXISTS pedidos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    shopify_order_id TEXT NOT NULL UNIQUE,
    cliente_email TEXT NOT NULL,
    cliente_nombre TEXT,

    -- Estados posibles: pending_qc | approved | auto_approved | rejected | shipped
    estado TEXT NOT NULL DEFAULT 'pending_qc',

    -- Token único para el enlace /qc.php?token=XYZ (ver includes/tokens.php)
    qc_token TEXT UNIQUE,
    qc_fotos_subidas_en TEXT,                    -- cuándo se subieron las fotos (dispara el correo)
    qc_respondido_en TEXT,                       -- cuándo el cliente aprobó/rechazó
    qc_vence_en TEXT,                            -- qc_fotos_subidas_en + 48 horas (lo revisa el cron)

    tracking_number TEXT,

    creado_en TEXT NOT NULL DEFAULT (datetime('now'))
);

-- ---------- FOTOS QC ----------
-- Varias fotos por pedido (frente, espalda, etiqueta, detalle de costura, etc.)
CREATE TABLE IF NOT EXISTS fotos_qc (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    pedido_id INTEGER NOT NULL,
    url_foto TEXT NOT NULL,
    etiqueta TEXT,                               -- ej. "Frente", "Espalda", "Detalle de costura"
    orden INTEGER DEFAULT 0,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE
);

-- ---------- ADMINISTRADORES ----------
-- Login del panel /admin/ — password_hash(), nunca texto plano.
CREATE TABLE IF NOT EXISTS admins (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    usuario TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    creado_en TEXT NOT NULL DEFAULT (datetime('now'))
);

-- ---------- LOG DE EVENTOS ----------
-- Auditoría simple: cada acción importante queda registrada
-- (aprobación, rechazo, auto-aprobación, reembolso disparado, etc.)
CREATE TABLE IF NOT EXISTS eventos_log (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    pedido_id INTEGER,
    tipo_evento TEXT NOT NULL,                   -- ej. "qc_approved", "qc_rejected", "auto_approved", "refund_triggered"
    detalle TEXT,
    creado_en TEXT NOT NULL DEFAULT (datetime('now')),
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS idx_pedidos_estado ON pedidos(estado);
CREATE INDEX IF NOT EXISTS idx_pedidos_qc_token ON pedidos(qc_token);
CREATE INDEX IF NOT EXISTS idx_fotos_qc_pedido ON fotos_qc(pedido_id);
