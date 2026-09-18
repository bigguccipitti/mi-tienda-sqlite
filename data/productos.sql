CREATE TABLE IF NOT EXISTS productos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    disenador TEXT NOT NULL,
    nombre TEXT NOT NULL,
    precio REAL NOT NULL,
    descripcion TEXT,
    imagen TEXT,
    genero TEXT NOT NULL DEFAULT 'Women',
    categoria TEXT NOT NULL DEFAULT 'Shoes',
    bestseller INTEGER NOT NULL DEFAULT 0,
    stock INTEGER DEFAULT 10
);

-- ================= WOMEN — SHOES =================
INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Gucci', 'Heeled Loafer', 890.00, 'Loafer con tacón de metal, piel granulada. Pieza verificada de archivo.', 'heeled-loafer.jpg', 'Women', 'Shoes', 1
WHERE NOT EXISTS (SELECT 1 FROM productos);

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Maison Margiela', 'Tabi Heel', 1120.00, 'Tacón Tabi de cuero partido, silueta original. Pieza verificada de archivo.', 'tabi-heel.jpg', 'Women', 'Shoes', 0
WHERE (SELECT COUNT(*) FROM productos) = 1;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Miu Miu', 'Ballet Flat', 540.00, 'Bailarina de satén con moño, punta redonda. Pieza verificada de archivo.', 'ballet-flat.jpg', 'Women', 'Shoes', 0
WHERE (SELECT COUNT(*) FROM productos) = 2;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Louis Vuitton', 'Monogram Boot', 1340.00, 'Bota alta con lona monogram y suela track. Pieza verificada de archivo.', 'monogram-boot.jpg', 'Women', 'Shoes', 0
WHERE (SELECT COUNT(*) FROM productos) = 3;

-- ================= WOMEN — PANTS =================
INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Louis Vuitton', 'Tailored Trouser', 980.00, 'Pantalón sastre de lana fría, corte recto. Pieza verificada de archivo.', 'tailored-trouser-women.jpg', 'Women', 'Pants', 0
WHERE (SELECT COUNT(*) FROM productos) = 4;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Miu Miu', 'Low-Rise Denim', 560.00, 'Denim de tiro bajo, lavado crudo. Pieza verificada de archivo.', 'low-rise-denim.jpg', 'Women', 'Pants', 1
WHERE (SELECT COUNT(*) FROM productos) = 5;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Gucci', 'Wide-Leg Wool', 890.00, 'Pantalón ancho de lana con pinzas. Pieza verificada de archivo.', 'wide-leg-wool.jpg', 'Women', 'Pants', 0
WHERE (SELECT COUNT(*) FROM productos) = 6;

-- ================= WOMEN — BAGS =================
INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Gucci', 'GG Canvas Bag', 1320.00, 'Bolso GG canvas con asa de cadena. Pieza verificada de archivo.', 'gg-canvas-bag.jpg', 'Women', 'Bags', 1
WHERE (SELECT COUNT(*) FROM productos) = 7;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Louis Vuitton', 'Monogram Tote', 1890.00, 'Tote monogram estructurado, interior de lona. Pieza verificada de archivo.', 'monogram-tote.jpg', 'Women', 'Bags', 0
WHERE (SELECT COUNT(*) FROM productos) = 8;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Maison Margiela', 'Glam Slam Bag', 1560.00, 'Bolso acolchado con cierre metálico. Pieza verificada de archivo.', 'glam-slam-bag.jpg', 'Women', 'Bags', 0
WHERE (SELECT COUNT(*) FROM productos) = 9;

-- ================= MEN — SHOES =================
INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Maison Margiela', 'Tabi Boot', 890.00, 'Bota Tabi de cuero, silueta partida original. Pieza verificada de archivo.', 'tabi-boot.jpg', 'Men', 'Shoes', 1
WHERE (SELECT COUNT(*) FROM productos) = 10;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Louis Vuitton', 'Leather Sneaker', 990.00, 'Sneaker de cuero con suela de goma. Pieza verificada de archivo.', 'leather-sneaker.jpg', 'Men', 'Shoes', 0
WHERE (SELECT COUNT(*) FROM productos) = 11;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Gucci', 'Horsebit Loafer', 720.00, 'Loafer con horsebit de metal, piel lisa. Pieza verificada de archivo.', 'horsebit-loafer.jpg', 'Men', 'Shoes', 0
WHERE (SELECT COUNT(*) FROM productos) = 12;

-- ================= MEN — PANTS =================
INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Miu Miu', 'Raw Denim', 640.00, 'Denim crudo sin lavar, corte recto. Pieza verificada de archivo.', 'raw-denim-men.jpg', 'Men', 'Pants', 0
WHERE (SELECT COUNT(*) FROM productos) = 13;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Gucci', 'Wool Trouser', 720.00, 'Pantalón de lana con pinzas, corte clásico. Pieza verificada de archivo.', 'wool-trouser-men.jpg', 'Men', 'Pants', 1
WHERE (SELECT COUNT(*) FROM productos) = 14;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Maison Margiela', 'Cargo Pant', 680.00, 'Pantalón cargo con costuras expuestas. Pieza verificada de archivo.', 'cargo-pant.jpg', 'Men', 'Pants', 0
WHERE (SELECT COUNT(*) FROM productos) = 15;

-- ================= MEN — BAGS =================
INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Louis Vuitton', 'Monogram Backpack', 2140.00, 'Mochila monogram estructurada, correas ajustables. Pieza verificada de archivo.', 'monogram-backpack.jpg', 'Men', 'Bags', 0
WHERE (SELECT COUNT(*) FROM productos) = 16;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Maison Margiela', 'Canvas Crossbody', 780.00, 'Bolso cruzado de canvas con detalle de cuero. Pieza verificada de archivo.', 'canvas-crossbody.jpg', 'Men', 'Bags', 0
WHERE (SELECT COUNT(*) FROM productos) = 17;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Gucci', 'GG Belt Bag', 890.00, 'Riñonera GG canvas con correa ajustable. Pieza verificada de archivo.', 'gg-belt-bag.jpg', 'Men', 'Bags', 1
WHERE (SELECT COUNT(*) FROM productos) = 18;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Louis Vuitton', 'Weekend Duffel', 2680.00, 'Bolso de viaje monogram, capacidad de fin de semana. Pieza verificada de archivo.', 'weekend-duffel.jpg', 'Men', 'Bags', 0
WHERE (SELECT COUNT(*) FROM productos) = 19;
