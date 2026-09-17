<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SANTORO. — Archive</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="cursor-dot" id="cursorDot"></div>

    <header class="site-header">
        <a href="index.php" class="logo">SANTORO.</a>
        <nav>
            <a href="women.php">Women</a>
            <a href="men.php">Men</a>
            <a href="journal.php">Journal</a>
            <a href="cart.php">Cart (<?php echo isset($_SESSION['carrito']) ? array_sum($_SESSION['carrito']) : 0; ?>)</a>
        </nav>
    </header>
    <main class="container">
