<?php
session_start();

require_once "../php/conexion.php";

// Recibir los datos del formulario
$usuario = $_POST["usuario"];
$contrasena = $_POST["contrasena"];

// Buscar al administrador
$consulta = $conexion->prepare("
    SELECT * FROM administradores
    WHERE usuario = ?
");

$consulta->execute([$usuario]);

$administrador = $consulta->fetch(PDO::FETCH_ASSOC);

// Comprobar usuario y contraseña
if ($administrador && password_verify($contrasena, $administrador["contrasena"])) {
    session_regenerate_id(true);

    $_SESSION["admin"] = $administrador["usuario"];

    header("Location: panel.php");
    exit();
} else {
    echo "Usuario o contraseña incorrectos.";
    echo "<br><a href='login.php'>Intentar nuevamente</a>";
}
?>
