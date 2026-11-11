const http = require("http");
const mysql = require("mysql2/promise");

// Conectar con MySQL
const conexion = mysql.createPool({
    host: "localhost",
    user: "root",
    password: "",
    database: "pokezona"
});

// Crear servidor
const servidor = http.createServer(async (req, res) => {
    res.setHeader("Access-Control-Allow-Origin", "http://localhost");
    res.setHeader("Content-Type", "application/json");

    if (req.url === "/estadisticas" && req.method === "GET") {
        try {
            const [productos] = await conexion.query(
                "SELECT COUNT(*) AS total FROM productos"
            );

            const [clientes] = await conexion.query(
                "SELECT COUNT(*) AS total FROM clientes"
            );

            const [pedidos] = await conexion.query(
                "SELECT COUNT(*) AS total FROM pedidos"
            );

            res.end(JSON.stringify({
                productos: productos[0].total,
                clientes: clientes[0].total,
                pedidos: pedidos[0].total
            }));

        } catch (error) {
            res.statusCode = 500;
            res.end(JSON.stringify({
                error: "No se pudieron consultar las estadísticas"
            }));
            console.log(error);
        }

    } else {
        res.statusCode = 404;
        res.end(JSON.stringify({
            error: "Ruta no encontrada"
        }));
    }
});

// Iniciar servidor
servidor.listen(3000, () => {
    console.log("Servidor Node.js funcionando en el puerto 3000");
});