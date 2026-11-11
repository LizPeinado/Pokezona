<?php
session_start();

// Comprobar que exista una sesión de administrador
if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de administración - PokéZona</title>

    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <header>
        <h1>PokéZona</h1>
        <p>Panel de administración</p>
    </header>
    <main>
        <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION["admin"]); ?></h2>
        <p>Has iniciado sesión correctamente.</p>
        <h3>Administración de la tienda</h3>

        <p>
            <a href="pedidos.php">Administrar pedidos</a>
        </p>
        <p>
            <a href="productos.php">Administrar productos</a>
        </p>
        <p>
            <a href="../index.html">Ver tienda pública</a>
        </p>

        <p>Desde aquí se podrán gestionar los productos y pedidos.</p>

        <p><a href="../index.html">Ver tienda pública</a></p>

        <p><a href="cerrar_sesion.php">Cerrar sesión</a></p>
    </main>
</body>
</html>