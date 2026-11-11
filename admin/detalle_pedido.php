<?php
/*mostrar los productos que pertenecen a cada pedido 
y el formulario para cambiar su estado. */
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}

require_once "../php/conexion.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header("Location: pedidos.php");
    exit();
}

// Actualizar el estado del pedido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $estado = $_POST["estado"];

    $estadosPermitidos = ["Pendiente", "Preparado", "Entregado"];
    if (in_array($estado, $estadosPermitidos, true)) {
        $consulta = $conexion->prepare("
            UPDATE pedidos
            SET estado = ?
            WHERE id_pedido = ?
        ");
        $consulta->execute([$estado, $id]);
    }

    header("Location: detalle_pedido.php?id=" . $id);
    exit();
}

// Consultar información del pedido
$consulta = $conexion->prepare("
    SELECT
        pedidos.*,
        clientes.nombre AS cliente,
        clientes.correo,
        clientes.telefono
    FROM pedidos
    INNER JOIN clientes
    ON pedidos.id_cliente = clientes.id_cliente
    WHERE pedidos.id_pedido = ?
");

$consulta->execute([$id]);
$pedido = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    header("Location: pedidos.php");
    exit();
}

// Consultar productos del pedido
$consulta = $conexion->prepare("
    SELECT
        detalle_pedido.cantidad,
        detalle_pedido.precio_unitario,
        detalle_pedido.subtotal,
        productos.nombre AS producto
    FROM detalle_pedido
    INNER JOIN productos
    ON detalle_pedido.id_producto = productos.id_producto
    WHERE detalle_pedido.id_pedido = ?
");

$consulta->execute([$id]);
$detalles = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del pedido - PokéZona</title>

    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <header>
        <h1>PokéZona</h1>
        <p>Detalle del pedido</p>
    </header>

    <main>
        <h2>Pedido #<?php echo $pedido["id_pedido"]; ?></h2>

        <h3>Información del cliente</h3>

        <p>Nombre: <?php echo htmlspecialchars($pedido["cliente"]); ?></p>

        <p>Correo: <?php echo htmlspecialchars($pedido["correo"]); ?></p>

        <p>Teléfono: <?php echo htmlspecialchars($pedido["telefono"] ?? "No registrado"); ?></p>

        <p>Fecha: <?php echo $pedido["fecha"]; ?></p>

        <h3>Productos del pedido</h3>

        <?php if (count($detalles) > 0): ?>
            <table border="1" cellpadding="10">
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio unitario</th>
                    <th>Subtotal</th>
                </tr>

                <?php foreach ($detalles as $detalle): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($detalle["producto"]); ?></td>

                        <td><?php echo $detalle["cantidad"]; ?></td>

                        <td>$<?php echo number_format($detalle["precio_unitario"], 2); ?></td>

                        <td>$<?php echo number_format($detalle["subtotal"], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

        <?php else: ?>
            <p>Este pedido no tiene productos registrados.</p>
        <?php endif; ?>

        <h3>Total: $<?php echo number_format($pedido["total"], 2); ?> MXN</h3>

        <h3>Estado del pedido</h3>

        <form method="POST">
            <label>Seleccionar estado:</label>

            <select name="estado">
                <option value="Pendiente"
                    <?php if ($pedido["estado"] == "Pendiente") echo "selected"; ?>>
                    Pendiente
                </option>

                <option value="Preparado"
                    <?php if ($pedido["estado"] == "Preparado") echo "selected"; ?>>
                    Preparado
                </option>

                <option value="Entregado"
                    <?php if ($pedido["estado"] == "Entregado") echo "selected"; ?>>
                    Entregado
                </option>
            </select>

            <button type="submit">Actualizar estado</button>
        </form>

        <p><a href="pedidos.php">Volver a pedidos</a></p>
    </main>
</body>
</html>