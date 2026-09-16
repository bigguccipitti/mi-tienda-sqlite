-- Tabla de productos: disenador, genero (Men/Women), categoria (Shoes/Pants/Bags/Jackets/Accessories), bestseller (3/0), stock (cantidad disponible)  
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

-- ===== WOMEN — SHOES =====
INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Gucci', 'Heeled Loafer', 890.00, 'Loafer con tacón, pieza verificada de archivo.', 'heeled-loafer.jpg', 'Women', 'Shoes', 1
WHERE NOT EXISTS (SELECT 1 FROM productos);

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Maison Margiela', 'Tabi Heel', 1120.00, 'Tacón Tabi, pieza verificada de archivo.', 'tabis1.png', 'Women', 'Shoes', 0
WHERE (SELECT COUNT(*) FROM productos) = 1;

-- ===== WOMEN — PANTS =====
INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Louis Vuitton', 'Tailored Trouser', 980.00, 'Pantalón sastre, pieza verificada de archivo.', 'tailored-trouser-women.jpg', 'Women', 'Pants', 0
WHERE (SELECT COUNT(*) FROM productos) = 2;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Miu Miu', 'Low-Rise Denim', 560.00, 'Denim de tiro bajo, pieza verificada de archivo.', 'low-rise-denim.jpg', 'Women', 'Pants', 1
WHERE (SELECT COUNT(*) FROM productos) = 3;

-- ===== WOMEN — BAGS =====
INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Gucci', 'GG Canvas Bag', 1320.00, 'Bolso GG canvas, pieza verificada de archivo.', 'gg-canvas-bag.jpg', 'Women', 'Bags', 1
WHERE (SELECT COUNT(*) FROM productos) = 4;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Louis Vuitton', 'Monogram Tote', 1890.00, 'Tote monogram, pieza verificada de archivo.', 'monogram-tote.jpg', 'Women', 'Bags', 0
WHERE (SELECT COUNT(*) FROM productos) = 5;

-- ===== MEN — SHOES =====
INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Maison Margiela', 'Tabi Boot', 890.00, 'Bota Tabi, pieza verificada de archivo.', 'tabis1.png', 'Men', 'Shoes', 1
WHERE (SELECT COUNT(*) FROM productos) = 6;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Louis Vuitton', 'Leather Sneaker', 990.00, 'Sneaker de cuero, pieza verificada de archivo.', 'leather-sneaker.jpg', 'Men', 'Shoes', 0
WHERE (SELECT COUNT(*) FROM productos) = 7;

-- ===== MEN — PANTS =====
INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Miu Miu', 'Raw Denim', 640.00, 'Denim crudo, pieza verificada de archivo.', 'raw-denim-men.jpg', 'Men', 'Pants', 0
WHERE (SELECT COUNT(*) FROM productos) = 8;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Gucci', 'Wool Trouser', 720.00, 'Pantalón de lana, pieza verificada de archivo.', 'wool-trouser-men.jpg', 'Men', 'Pants', 1
WHERE (SELECT COUNT(*) FROM productos) = 9;

-- ===== MEN — BAGS =====
INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Louis Vuitton', 'Monogram Backpack', 2140.00, 'Mochila monogram, pieza verificada de archivo.', 'monogram-backpack.jpg', 'Men', 'Bags', 0
WHERE (SELECT COUNT(*) FROM productos) = 10;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Maison Margiela', 'Canvas Crossbody', 70.00, 'Bolso cruzado de canvas, pieza verificada de archivo.', 'canvas-crossbody.jpg', 'Men', 'Bags', 0
WHERE (SELECT COUNT(*) FROM productos) = 11;

-- ===== MEN — JACKETS =====
INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Balenciaga', 'Triple S Jacket', 2490.00, 'Chaqueta Triple S, pieza verificada de archivo.', 'triple-s-jacket.jpg', 'Men', 'Jackets', 1
WHERE (SELECT COUNT(*) FROM productos) = 12;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Off-White', 'Industrial Jacket', 1990.00, 'Chaqueta industrial, pieza verificada de archivo.', 'industrial-jacket.jpg', 'Men', 'Jackets', 0
WHERE (SELECT COUNT(*) FROM productos) = 13;

-- ===== MEN — ACCESSORIES =====
INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Gucci', 'Ace Hat', 320.00, 'Gorra Ace, pieza verificada de archivo.', 'ace-hat.jpg', 'Men', 'Accessories', 1
WHERE (SELECT COUNT(*) FROM productos) = 14;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Louis Vuitton', 'Monogram Scarf', 450.00, 'Bufanda monogram, pieza verificada de archivo.', 'monogram-scarf.jpg', 'Men', 'Accessories', 0
WHERE (SELECT COUNT(*) FROM productos) = 15;

-- ===== MEN — T-SHIRTS =====
INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Balenciaga', 'Logo T-Shirt', 490.00, 'Camiseta con logo, pieza verificada de archivo.', 'logo-t-shirt.jpg', 'Men', 'T-Shirts', 1
WHERE (SELECT COUNT(*) FROM productos) = 16;

-- ===== MEN — HOODIES =====
INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Off-White', 'Diagonal Hoodie', 890.00, 'Sudadera con diseño diagonal, pieza verificada de archivo.', 'diagonal-hoodie.jpg', 'Men', 'Hoodies', 0
WHERE (SELECT COUNT(*) FROM productos) = 17;

-- ===== MEN — SWEATERS =====
INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Gucci', 'Wool Sweater', 1200.00, 'Suéter de lana, pieza verificada de archivo.', 'wool-sweater.jpg', 'Men', 'Sweaters', 1
WHERE (SELECT COUNT(*) FROM productos) = 18;

-- ===== WOMEN — MORE PRODUCTS =====

INSERT INTO productos
(disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Balenciaga', 'Hourglass Jacket', 1890.00,
       'Chaqueta estructurada de archivo.', 'hourglass-jacket.jpg',
       'Women', 'Jackets', 1
WHERE NOT EXISTS (
    SELECT 1 FROM productos WHERE nombre = 'Hourglass Jacket'
);

INSERT INTO productos
(disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Gucci', 'Silk Blouse', 760.00,
       'Blusa de seda de archivo.', 'silk-blouse.jpg',
       'Women', 'T-Shirts', 0
WHERE NOT EXISTS (
    SELECT 1 FROM productos WHERE nombre = 'Silk Blouse'
);

INSERT INTO productos
(disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Miu Miu', 'Cropped Cardigan', 680.00,
       'Cárdigan corto de archivo.', 'cropped-cardigan.jpg',
       'Women', 'Sweaters', 1
WHERE NOT EXISTS (
    SELECT 1 FROM productos WHERE nombre = 'Cropped Cardigan'
);

INSERT INTO productos
(disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Louis Vuitton', 'Leather Shoulder Bag', 1650.00,
       'Bolso de hombro de cuero.', 'leather-shoulder-bag.jpg',
       'Women', 'Bags', 1
WHERE NOT EXISTS (
    SELECT 1 FROM productos WHERE nombre = 'Leather Shoulder Bag'
);

INSERT INTO productos
(disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Maison Margiela', 'Tabi Flat', 820.00,
       'Bailarina Tabi de archivo.', 'tabi-flat.png',
       'Women', 'Shoes', 1
WHERE NOT EXISTS (
    SELECT 1 FROM productos WHERE nombre = 'Tabi Flat'
);

INSERT INTO productos
(disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Off-White', 'Oversized Hoodie', 790.00,
       'Sudadera oversize de archivo.', 'oversized-hoodie.jpg',
       'Women', 'Hoodies', 0
WHERE NOT EXISTS (
    SELECT 1 FROM productos WHERE nombre = 'Oversized Hoodie'
);