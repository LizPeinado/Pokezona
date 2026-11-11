// Obtener el carrito guardado
let carrito = JSON.parse(localStorage.getItem("carrito")) || [];

// Guardar los cambios
function guardarCarrito() {
    localStorage.setItem("carrito", JSON.stringify(carrito));
    mostrarCarrito();
}

// Agregar producto
function agregarCarrito(producto) {
    let existente = carrito.find(p => p.id_producto == producto.id_producto);

    if (existente) {
        existente.cantidad++;
    } else {
        carrito.push({
            id_producto: producto.id_producto,
            nombre: producto.nombre,
            precio: Number(producto.precio),
            cantidad: 1
        });
    }
    guardarCarrito();

    alert("Producto agregado al carrito");
}

// Mostrar productos del carrito
function mostrarCarrito() {
    const contenedor = document.getElementById("carrito");
    const total = document.getElementById("totalCarrito");

    if (!contenedor || !total) return;
    contenedor.innerHTML = "";
    let suma = 0;

    carrito.forEach((producto, indice) => {
        let subtotal = producto.precio * producto.cantidad;
        suma += subtotal;

        const elemento = document.createElement("div");

        const nombre = document.createElement("p");
        nombre.textContent = producto.nombre;

        const cantidad = document.createElement("input");
        cantidad.type = "number";
        cantidad.min = "1";
        cantidad.value = producto.cantidad;

        cantidad.addEventListener("change", () => {
            producto.cantidad = Math.max(1, parseInt(cantidad.value) || 1);
            guardarCarrito();
        });

        const precio = document.createElement("p");
        precio.textContent = "$" + subtotal.toFixed(2) + " MXN";

        const eliminar = document.createElement("button");
        eliminar.textContent = "Quitar";

        eliminar.addEventListener("click", () => {
            carrito.splice(indice, 1);
            guardarCarrito();
        });

        elemento.append(nombre, cantidad, precio, eliminar);
        contenedor.appendChild(elemento);
    });
    total.textContent = "$" + suma.toFixed(2) + " MXN";
}

// Mostrar carrito al cargar la página
mostrarCarrito();
