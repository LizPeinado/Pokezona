<?php
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}

require_once "../php/conexion.php";

// Consultar las categorías
$consulta = $conexion->query("SELECT * FROM categorias ORDER BY nombre");
$categorias = $consulta->fetchAll(PDO::FETCH_ASSOC);

$error = "";

// Comprobar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $descripcion = trim($_POST["descripcion"]);
    $id_categoria = (int) $_POST["id_categoria"];
    $precio = $_POST["precio"];
    $existencia = $_POST["existencia"];
    $imagen = trim($_POST["imagen"]);

    // Validar los datos
    if (
        $nombre == "" ||
        $id_categoria <= 0 ||
        !is_numeric($precio) ||
        $precio < 0 ||
        filter_var($existencia, FILTER_VALIDATE_INT) === false ||
        $existencia < 0
    ){
        $error = "Revisa los datos ingresados.";
    } else {
        try {
            // Insertar producto
            $consulta = $conexion->prepare("
                INSERT INTO productos
                (id_categoria, nombre, descripcion, precio, existencia, imagen)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $consulta->execute([
                $id_categoria,
                $nombre,
                $descripcion,
                $precio,
                $existencia,
                $imagen
            ]);

            // Regresar a la lista de productos
            header("Location: productos.php");
            exit();
        } catch (PDOException $e) {
            $error = "No se pudo registrar el producto. Verifica los datos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar producto - PokéZona</title>

    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <header>
        <h1>PokéZona</h1>
        <p>Agregar producto</p>
    </header>
    <main>
        <h2>Registrar nuevo producto</h2>

        <?php if ($error != ""): ?>
            <p><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Nombre del producto:</label>
            <input
                type="text"
                name="nombre"
                required
                maxlength="150"
            >
            <br><br>

            <label>Descripción:</label>
            <textarea name="descripcion"></textarea>

            <br><br>

            <label>Categoría:</label>

            <select name="id_categoria" required>
                <option value="">Selecciona una categoría</option>

                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?php echo $categoria["id_categoria"]; ?>">
                        <?php echo htmlspecialchars($categoria["nombre"]); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <br><br>

            <label>Precio:</label>
            <input
                type="number"
                name="precio"
                min="0"
                step="0.01"
                required
            >
            <br><br>
            <label>Existencia:</label>
            <input
                type="number"
                name="existencia"
                min="0"
                step="1"
                required
            >

            <br><br>

            <label>Ruta de la imagen:</label>
            <input
                type="text"
                name="imagen"
                placeholder="imagenes/nombre_imagen.jpg"
            >
            <br><br>
            <button type="submit">Guardar producto</button>
        </form>

        <p><a href="productos.php">Volver a productos</a></p>
    </main>
</body>
</html>