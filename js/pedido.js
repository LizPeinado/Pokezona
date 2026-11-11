const formulario = document.getElementById("formularioPedido");

formulario.addEventListener("submit", async function(evento) {
    evento.preventDefault();
    const mensaje = document.getElementById("mensajePedido");

    if (carrito.length === 0) {
        mensaje.textContent = "Agrega productos antes de confirmar el pedido.";
        return;
    }

    const datosFormulario = new FormData(formulario);

    const datos = {
        nombre: datosFormulario.get("nombre"),
        correo: datosFormulario.get("correo"),
        telefono: datosFormulario.get("telefono"),
        observaciones: datosFormulario.get("observaciones"),
        productos: carrito
    };

    try {
        const respuesta = await fetch("api/crear_pedido.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(datos)
        });

        const resultado = await respuesta.json();

        if (!respuesta.ok) {
            throw new Error(resultado.error);
        }

        mensaje.textContent =
            "¡Pedido #" + resultado.id_pedido +
            " registrado! Total: $" +
            Number(resultado.total).toFixed(2) +
            " MXN. Recoge tu pedido en tienda.";

        // Vaciar carrito
        carrito = [];
        guardarCarrito();

        formulario.reset();
    } catch (error) {
        mensaje.textContent = error.message;
    }
});
