document
.getElementById("registroForm")
.addEventListener("submit", async (e) => {

    e.preventDefault();

    const datos = {
        nombre: document.getElementById("nombre").value.trim(),
        correo: document.getElementById("correo").value.trim(),
        password: document.getElementById("password").value.trim()
    };

    try {

        const respuesta = await fetch(
            "backend/registro.php",
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(datos)
            }
        );

        const resultado = await respuesta.json();

        alert(resultado.message);

        if (resultado.success) {

            document.getElementById("registroForm").reset();

            window.location.href = "index.html";
        }

    } catch (error) {

        console.error(error);

        alert(
            "Error al conectar con el servidor"
        );
    }
});