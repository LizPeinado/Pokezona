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

// Consultar el producto
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

// Consultar categorías
$consulta = $conexion->query("SELECT * FROM categorias ORDER BY nombre");
$categorias = $consulta->fetchAll(PDO::FETCH_ASSOC);

$error = "";

// Actualizar producto
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $descripcion = trim($_POST["descripcion"]);
    $id_categoria = (int) $_POST["id_categoria"];
    $precio = $_POST["precio"];
    $existencia = $_POST["existencia"];
    $imagen = trim($_POST["imagen"]);

    if (
        $nombre == "" ||
        $id_categoria <= 0 ||
        !is_numeric($precio) ||
        $precio < 0 ||
        filter_var($existencia, FILTER_VALIDATE_INT) === false ||
        $existencia < 0
    ) {
        $error = "Revisa los datos ingresados.";
    } else {
        try {
            $consulta = $conexion->prepare("
                UPDATE productos SET
                    id_categoria = ?,
                    nombre = ?,
                    descripcion = ?,
                    precio = ?,
                    existencia = ?,
                    imagen = ?
                WHERE id_producto = ?
            ");

            $consulta->execute([
                $id_categoria,
                $nombre,
                $descripcion,
                $precio,
                $existencia,
                $imagen,
                $id
            ]);
            header("Location: productos.php");

            exit();
        } catch (PDOException $e) {
            $error = "No se pudo actualizar el producto. Verifica los datos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar producto - PokéZona</title>

    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <header>
        <h1>PokéZona</h1>
        <p>Editar producto</p>
    </header>
    <main>
        <h2>Modificar producto</h2>

        <?php if ($error != ""): ?>
            <p><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Nombre:</label>
            <input
                type="text"
                name="nombre"
                value="<?php echo htmlspecialchars($producto["nombre"]); ?>"
                maxlength="150"
                required
            >

            <br><br>

            <label>Descripción:</label>
            <textarea name="descripcion"><?php echo htmlspecialchars($producto["descripcion"] ?? ""); ?></textarea>

            <br><br>

            <label>Categoría:</label>

            <select name="id_categoria" required>
                <?php foreach ($categorias as $categoria): ?>
                    <option
                        value="<?php echo $categoria["id_categoria"]; ?>"
                        <?php if ($categoria["id_categoria"] == $producto["id_categoria"]) echo "selected"; ?>
                    >
                        <?php echo htmlspecialchars($categoria["nombre"]); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <br><br>

            <label>Precio:</label>
            <input type="number" name="precio" min="0" step="0.01" value="<?php echo htmlspecialchars($producto["precio"]); ?>" required>

            <br><br>

            <label>Existencia:</label>
            <input type="number" name="existencia" min="0" step="1" value="<?php echo htmlspecialchars($producto["existencia"]); ?>" required>

            <br><br>

            <label>Ruta de la imagen:</label>
            <input type="text" name="imagen" value="<?php echo htmlspecialchars($producto["imagen"] ?? ""); ?>">

            <br><br>

            <button type="submit">Guardar cambios</button>
        </form>
        
        <p><a href="productos.php">Cancelar</a></p>
    </main>
</body>
</html>
