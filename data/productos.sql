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

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Gucci', 'Heeled Loafer', 890.00, 'Loafer con tacón, pieza verificada de archivo.', 'heeled-loafer.jpg', 'Women', 'Shoes', 1
WHERE NOT EXISTS (SELECT 1 FROM productos);

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Maison Margiela', 'Tabi Heel', 1120.00, 'Tacón Tabi, pieza verificada de archivo.', 'tabi-heel.jpg', 'Women', 'Shoes', 0
WHERE (SELECT COUNT(*) FROM productos) = 1;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Louis Vuitton', 'Tailored Trouser', 980.00, 'Pantalón sastre, pieza verificada de archivo.', 'tailored-trouser-women.jpg', 'Women', 'Pants', 0
WHERE (SELECT COUNT(*) FROM productos) = 2;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Miu Miu', 'Low-Rise Denim', 560.00, 'Denim de tiro bajo, pieza verificada de archivo.', 'low-rise-denim.jpg', 'Women', 'Pants', 1
WHERE (SELECT COUNT(*) FROM productos) = 3;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Gucci', 'GG Canvas Bag', 1320.00, 'Bolso GG canvas, pieza verificada de archivo.', 'gg-canvas-bag.jpg', 'Women', 'Bags', 1
WHERE (SELECT COUNT(*) FROM productos) = 4;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Louis Vuitton', 'Monogram Tote', 1890.00, 'Tote monogram, pieza verificada de archivo.', 'monogram-tote.jpg', 'Women', 'Bags', 0
WHERE (SELECT COUNT(*) FROM productos) = 5;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Maison Margiela', 'Tabi Boot', 890.00, 'Bota Tabi, pieza verificada de archivo.', 'tabi-boot.jpg', 'Men', 'Shoes', 1
WHERE (SELECT COUNT(*) FROM productos) = 6;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Louis Vuitton', 'Leather Sneaker', 990.00, 'Sneaker de cuero, pieza verificada de archivo.', 'leather-sneaker.jpg', 'Men', 'Shoes', 0
WHERE (SELECT COUNT(*) FROM productos) = 7;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Miu Miu', 'Raw Denim', 640.00, 'Denim crudo, pieza verificada de archivo.', 'raw-denim-men.jpg', 'Men', 'Pants', 0
WHERE (SELECT COUNT(*) FROM productos) = 8;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Gucci', 'Wool Trouser', 720.00, 'Pantalón de lana, pieza verificada de archivo.', 'wool-trouser-men.jpg', 'Men', 'Pants', 1
WHERE (SELECT COUNT(*) FROM productos) = 9;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Louis Vuitton', 'Monogram Backpack', 2140.00, 'Mochila monogram, pieza verificada de archivo.', 'monogram-backpack.jpg', 'Men', 'Bags', 0
WHERE (SELECT COUNT(*) FROM productos) = 10;

INSERT INTO productos (disenador, nombre, precio, descripcion, imagen, genero, categoria, bestseller)
SELECT 'Maison Margiela', 'Canvas Crossbody', 780.00, 'Bolso cruzado de canvas, pieza verificada de archivo.', 'canvas-crossbody.jpg', 'Men', 'Bags', 0
WHERE (SELECT COUNT(*) FROM productos) = 11;
