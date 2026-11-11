<?php
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}

require_once "../php/conexion.php";

// Consultar los pedidos y sus clientes
$consulta = $conexion->query("
    SELECT
        pedidos.id_pedido,
        pedidos.fecha,
        pedidos.total,
        pedidos.estado,
        clientes.nombre AS cliente,
        clientes.correo
    FROM pedidos
    INNER JOIN clientes
    ON pedidos.id_cliente = clientes.id_cliente
    ORDER BY pedidos.fecha DESC
");

$pedidos = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar pedidos - PokéZona</title>

    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <header>
        <h1>PokéZona</h1>
        <p>Administración de pedidos</p>
    </header>
    <main>
        <h2>Pedidos registrados</h2>

        <?php if (count($pedidos) > 0): ?>
            <table border="1" cellpadding="10">
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Correo</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>

                <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td><?php echo $pedido["id_pedido"]; ?></td>

                        <td><?php echo htmlspecialchars($pedido["cliente"]); ?></td>

                        <td><?php echo htmlspecialchars($pedido["correo"]); ?></td>

                        <td><?php echo $pedido["fecha"]; ?></td>

                        <td>$<?php echo number_format($pedido["total"], 2); ?> MXN</td>

                        <td><?php echo htmlspecialchars($pedido["estado"]); ?></td>

                        <td>
                            <a href="detalle_pedido.php?id=<?php echo $pedido["id_pedido"]; ?>">
                                Ver pedido
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

        <?php else: ?>
            <p>Todavía no hay pedidos registrados.</p>
        <?php endif; ?>

        <p><a href="panel.php">Volver al panel</a></p>
    </main>
</body>
</html>
