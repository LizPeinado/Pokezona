<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso de administrador - PokéZona</title>

    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <header>
        <h1>PokéZona</h1>
        <p>Acceso de administrador</p>
    </header>
    <main>
        <h2>Iniciar sesión</h2>
        <form action="verificar.php" method="POST">
            <label>Usuario:</label>
            <input type="text" name="usuario" required>

            <label>Contraseña:</label>
            <input type="password" name="contrasena" required>

            <button type="submit">Entrar</button>
        </form>

        <p><a href="../index.html">Volver a la tienda</a></p>
    </main>
</body>
</html>
