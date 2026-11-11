<?php
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}

require_once "../php/conexion.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header("Location: productos.php");
    exit();
}

// Consultar producto
$consulta = $conexion->prepare("
    SELECT * FROM productos
    WHERE id_producto = ?
");

$consulta->execute([$id]);
$producto = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    header("Location: productos.php");
    exit();
}

// Desactivar producto al confirmar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $consulta = $conexion->prepare("
        UPDATE productos
        SET activo = 0
        WHERE id_producto = ?
    ");

    $consulta->execute([$id]);

    header("Location: productos.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar producto - PokéZona</title>

    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <header>
        <h1>PokéZona</h1>
        <p>Eliminar producto</p>
    </header>
    <main>
        <h2>¿Deseas eliminar este producto?</h2>

        <p>
            Producto:
            <strong><?php echo htmlspecialchars($producto["nombre"]); ?></strong>
        </p>
        <form method="POST">
            <button type="submit">Confirmar eliminación</button>
        </form>

        <p><a href="productos.php">Cancelar</a></p>
    </main>
</body>
</html>