<?php
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}

require_once "../php/conexion.php";

// Consultar los productos y sus categorías
$consulta = $conexion->query("
    SELECT
        productos.id_producto,
        productos.nombre,
        productos.precio,
        productos.existencia,
        productos.imagen,
        productos.activo,
        categorias.nombre AS categoria
    FROM productos
    INNER JOIN categorias
    ON productos.id_categoria = categorias.id_categoria
    ORDER BY productos.id_producto DESC
");

$productos = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar productos - PokéZona</title>

    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <header>
        <h1>PokéZona</h1>
        <p>Administración de productos</p>
    </header>
    <main>
        <h2>Productos registrados</h2>
        <p>
            <a href="agregar_producto.php">Agregar producto</a>
        </p>

        <table border="1" cellpadding="10">
            <tr>
                <th>Estado</th>
                <th>ID</th>
                <th>Imagen</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Precio</th>
                <th>Existencia</th>
                <th>Acciones</th>
            </tr>

            <?php foreach ($productos as $producto): ?>
                <tr>
                    <td><?php echo $producto["id_producto"]; ?></td>

                    <td>
                        <?php if ($producto["imagen"]): ?>
                            <img
                                src="../<?php echo htmlspecialchars($producto["imagen"]); ?>"
                                width="70"
                                alt="Imagen del producto"
                            >
                        <?php endif; ?>
                    </td>

                    <td><?php echo htmlspecialchars($producto["nombre"]); ?></td>

                    <td><?php echo htmlspecialchars($producto["categoria"]); ?></td>

                    <td>$<?php echo number_format($producto["precio"], 2); ?></td>

                    <td><?php echo $producto["existencia"]; ?></td>

                    <td>
                        <?php
                        echo $producto["activo"] == 1 ? "Activo" : "Inactivo";
                        ?>
                    </td>

                    <td>
                        <a href="editar_producto.php?id=<?php echo $producto["id_producto"]; ?>">
                            Editar
                        </a>
                        <a href="eliminar_producto.php?id=<?php echo $producto["id_producto"]; ?>">
                            Eliminar
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <?php if (count($productos) == 0): ?>
            <p>No hay productos registrados.</p>
        <?php endif; ?>

        <p><a href="panel.php">Volver al panel</a></p>
    </main>
</body>
</html>
