<?php
header("Content-Type: application/json; charset=UTF-8");

require_once "../php/conexion.php";

try {
    $consulta = $conexion->query("
        SELECT
            productos.id_producto,
            productos.nombre,
            productos.descripcion,
            productos.precio,
            productos.existencia,
            productos.imagen,
            categorias.nombre AS categoria

        FROM productos

        INNER JOIN categorias
        ON productos.id_categoria = categorias.id_categoria

        WHERE productos.activo = 1
    ");

    $productos = $consulta->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(
        $productos,
        JSON_UNESCAPED_UNICODE |
        JSON_PRETTY_PRINT
    );
} catch (PDOException $error) {
    http_response_code(500);

    echo json_encode([
        "error" => "Error al consultar los productos"
    ]);
}
?>