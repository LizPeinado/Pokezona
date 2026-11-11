<?php
header("Content-Type: application/json; charset=UTF-8");

require_once "../php/conexion.php";

// Recibir información enviada por JavaScript
$datos = json_decode(file_get_contents("php://input"), true);

// Comprobar los datos
if (
    !$datos ||
    empty($datos["nombre"]) ||
    empty($datos["correo"]) ||
    empty($datos["productos"]) ||
    !filter_var($datos["correo"], FILTER_VALIDATE_EMAIL)
) {
    http_response_code(400);

    echo json_encode([
        "error" => "Completa tus datos y agrega productos al carrito."
    ]);

    exit();
}

$nombre = trim($datos["nombre"]);
$correo = trim($datos["correo"]);
$telefono = trim($datos["telefono"] ?? "");
$observaciones = trim($datos["observaciones"] ?? "");
$productos = $datos["productos"];

if (strlen($nombre) > 100 || strlen($correo) > 150 || strlen($telefono) > 20) {
    http_response_code(400);
    echo json_encode(["error" => "Los datos ingresados son demasiado largos."]);
    exit();
}

try {
    $conexion->beginTransaction();

    // Buscar al cliente por su correo
    $consulta = $conexion->prepare("
        SELECT id_cliente FROM clientes
        WHERE correo = ?
        LIMIT 1
    ");

    $consulta->execute([$correo]);

    $cliente = $consulta->fetch(PDO::FETCH_ASSOC);

    if ($cliente) {
        $id_cliente = $cliente["id_cliente"];

        // Actualizar sus datos
        $consulta = $conexion->prepare("
            UPDATE clientes
            SET nombre = ?, telefono = ?
            WHERE id_cliente = ?
        ");

        $consulta->execute([$nombre, $telefono, $id_cliente]);
    } else {
        // Registrar nuevo cliente
        $consulta = $conexion->prepare("
            INSERT INTO clientes (nombre, correo, telefono)
            VALUES (?, ?, ?)
        ");
        $consulta->execute([$nombre, $correo, $telefono]);

        $id_cliente = $conexion->lastInsertId();
    }
    $total = 0;
    $detalles = [];

    // Comprobar productos y existencias
    foreach ($productos as $producto) {
        $id_producto = (int) $producto["id_producto"];
        $cantidad = filter_var(
            $producto["cantidad"],
            FILTER_VALIDATE_INT
        );

        if ($id_producto <= 0 || $cantidad === false || $cantidad <= 0) {
            throw new Exception("Hay un producto con datos incorrectos.");
        }

        $consulta = $conexion->prepare("
            SELECT nombre, precio, existencia
            FROM productos
            WHERE id_producto = ? AND activo = 1
            FOR UPDATE
        ");

        $consulta->execute([$id_producto]);

        $productoBD = $consulta->fetch(PDO::FETCH_ASSOC);

        if (!$productoBD) {
            throw new Exception("Uno de los productos ya no está disponible.");
        }

        if ($productoBD["existencia"] < $cantidad) {
            throw new Exception(
                "No hay suficiente existencia de " . $productoBD["nombre"]
            );
        }

        $precio = $productoBD["precio"];
        $subtotal = $precio * $cantidad;

        $total += $subtotal;

        $detalles[] = [
            "id_producto" => $id_producto,
            "cantidad" => $cantidad,
            "precio" => $precio,
            "subtotal" => $subtotal
        ];
    }

    // Crear el pedido
    $consulta = $conexion->prepare("
        INSERT INTO pedidos
        (id_cliente, total, estado, observaciones)
        VALUES (?, ?, 'Pendiente', ?)
    ");

    $consulta->execute([
        $id_cliente,
        $total,
        $observaciones
    ]);

    $id_pedido = $conexion->lastInsertId();

    // Guardar detalles y actualizar existencias
    foreach ($detalles as $detalle) {
        $consulta = $conexion->prepare("
            INSERT INTO detalle_pedido
            (id_pedido, id_producto, cantidad, precio_unitario, subtotal)
            VALUES (?, ?, ?, ?, ?)
        ");

        $consulta->execute([
            $id_pedido,
            $detalle["id_producto"],
            $detalle["cantidad"],
            $detalle["precio"],
            $detalle["subtotal"]
        ]);

        $consulta = $conexion->prepare("
            UPDATE productos
            SET existencia = existencia - ?
            WHERE id_producto = ?
        ");

        $consulta->execute([
            $detalle["cantidad"],
            $detalle["id_producto"]
        ]);
    }

    // Confirmar todas las operaciones
    $conexion->commit();

    echo json_encode([
        "mensaje" => "Pedido registrado correctamente.",
        "id_pedido" => $id_pedido,
        "total" => $total
    ]);

} catch (Throwable $error) {

    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }

    http_response_code(400);

    echo json_encode([
        "error" => $error->getMessage()
    ]);
}
?>
