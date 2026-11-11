async function cargarProductos() {
    const contenedor = document.getElementById("productos");
    try {
        const respuesta = await fetch("api/productos.php");

        if (!respuesta.ok) {
            throw new Error("Error al consultar los productos");
        }

        const productos = await respuesta.json();
        contenedor.innerHTML = "";

        productos.forEach(producto => {

            const boton = document.createElement("button");
            boton.textContent = "Agregar al carrito";

            boton.addEventListener("click", () => {
                agregarCarrito(producto);
            });

            const tarjeta = document.createElement("div");
            tarjeta.className = "producto";

            const imagen = document.createElement("img");
            imagen.src = producto.imagen || "";
            imagen.alt = producto.nombre;

            const nombre = document.createElement("h3");
            nombre.textContent = producto.nombre;

            const descripcion = document.createElement("p");
            descripcion.textContent = producto.descripcion || "";

            const categoria = document.createElement("p");
            categoria.textContent = "Categoría: " + producto.categoria;

            const precio = document.createElement("p");
            precio.textContent = "$" + producto.precio + " MXN";

            const existencia = document.createElement("p");
            existencia.textContent = "Existencias: " + producto.existencia;

            tarjeta.append(
                imagen,
                nombre,
                descripcion,
                categoria,
                precio,
                existencia,
                boton
            );

            contenedor.appendChild(tarjeta);
        });

        if (productos.length === 0) {
            contenedor.textContent = "No hay productos disponibles.";
        }

    } catch (error) {
        contenedor.textContent = "No se pudieron cargar los productos.";
        console.error(error);
    }
}

cargarProductos();
