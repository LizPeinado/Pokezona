<?php
$servidor = "localhost";
$usuario = "root";
$contrasena = "";
$base_datos = "pokezona";

try {
    $conexion = new PDO(
        "mysql:host=$servidor;dbname=$base_datos;charset=utf8mb4",
        $usuario,
        $contrasena
    );
    $conexion->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );
} catch (PDOException $error) {
    die("Error de conexion: " . $error->getMessage());
}
?>
