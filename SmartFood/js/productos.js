
async function cargarProductos() {

    const respuesta = await fetch("backend/productos.php");
    const productos = await respuesta.json();

    const contenedor = document.getElementById("productos-container");

    contenedor.innerHTML = "";

    productos.forEach(producto => {

        contenedor.innerHTML += `
        
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow">

                <img src="${producto.imagen}" 
                     class="card-img-top"
                     style="height:250px; object-fit:cover;">

                <div class="card-body">

                    <h5>${producto.nombre}</h5>

                    <p>${producto.descripcion}</p>

                    <h4>$${producto.precio}</h4>

                    <button 
                        class="btn btn-warning w-100"
                        onclick="agregarAlCarrito(
                            ${producto.id},
                            '${producto.nombre}',
                            ${producto.precio}
                        )">

                        Agregar
                    </button>

                </div>
            </div>
        </div>

        `;
    });
}

cargarProductos();

