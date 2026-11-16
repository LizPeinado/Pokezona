let imagenActual = 0;

const imagenes = document.querySelectorAll("#imagenesCarrusel img");
const indicadores = document.getElementById("indicadores");

// Crear indicadores
imagenes.forEach((imagen, indice) => {

    const punto = document.createElement("button");

    punto.addEventListener("click", () => {
        mostrarImagen(indice);
    });

    indicadores.appendChild(punto);
});

// Mostrar imagen
function mostrarImagen(indice) {
    imagenActual = (indice + imagenes.length) % imagenes.length;

    imagenes.forEach((imagen, i) => {
        imagen.classList.toggle("activa", i === imagenActual);
    });

    indicadores.querySelectorAll("button").forEach((punto, i) => {
        punto.classList.toggle("activo", i === imagenActual);
    });
}

// Cambiar imagen con flechas
function cambiarImagen(direccion) {
    mostrarImagen(imagenActual + direccion);
}

// Iniciar carrusel
mostrarImagen(0);

// Avance automático cada 4 segundos
setInterval(() => {
    cambiarImagen(1);
}, 4000);

// Consultar productos para las categorías
async function cargarCategorias() {
    const contenedor = document.getElementById("categorias");

    try {
        const respuesta = await fetch("api/productos.php");
        const productos = await respuesta.json();

        contenedor.innerHTML = "";

        const categorias = [
            "Peluches",
            "Ropa",
            "Accesorios",
            "Figuras",
            "Cartas"
        ];

        categorias.forEach(categoria => {
            const productosCategoria = productos.filter(
                producto => producto.categoria.toLowerCase() === categoria.toLowerCase()
            );
            if (productosCategoria.length === 0) return;

            const seccion = document.createElement("section");
            seccion.className = "categoria";

            const titulo = document.createElement("h2");
            titulo.textContent = categoria;

            const lista = document.createElement("div");
            lista.className = "lista-productos";

            productosCategoria.slice(0, 3).forEach(producto => {
                const tarjeta = document.createElement("article");
                tarjeta.className = "tarjeta";
                tarjeta.style.cursor = "pointer";

                tarjeta.addEventListener("click", () => {
                    window.location.href = "productos.html";
                });

                const imagen = document.createElement("img");
                imagen.src = producto.imagen ? producto.imagen : "";
                imagen.alt = producto.nombre;

                const nombre = document.createElement("h3");
                nombre.textContent = producto.nombre;

                const precio = document.createElement("p");
                precio.textContent = "$" + Number(producto.precio).toFixed(2) + " MXN";

                tarjeta.append(imagen, nombre, precio);
                lista.appendChild(tarjeta);
            });

            seccion.append(titulo, lista);
            contenedor.appendChild(seccion);
        });
    } catch (error) {
        contenedor.textContent = "No se pudieron cargar los productos.";
        console.log(error);
    }
}

cargarCategorias();
