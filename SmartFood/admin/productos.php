<?php
session_start();

if(!isset($_SESSION['rol'])){

    header("Location: ../index.html");
    exit();
}

if($_SESSION['rol'] != 'admin'){

    header("Location: ../index.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Productos Admin</title>

<style>

body{
    font-family: Arial;
    background:#111;
    color:white;
    padding:20px;
}

h1{
    text-align:center;
}

form{
    display:grid;
    gap:10px;
    margin-bottom:30px;
}

input, select, textarea, button{

    padding:10px;
    border:none;
    border-radius:8px;
}

button{
    background:gold;
    cursor:pointer;
    font-weight:bold;
}

table{
    width:100%;
    border-collapse:collapse;
}

th, td{
    border:1px solid #333;
    padding:10px;
    text-align:center;
}

img{
    width:80px;
    border-radius:10px;
}

.editar{
    background:blue;
    color:white;
}

.eliminar{
    background:red;
    color:white;
}

@media(max-width:768px){

    table{
        font-size:12px;
    }

    img{
        width:50px;
    }
}

</style>

</head>

<body>

<h1>CRUD PRODUCTOS</h1>

<form id="formProducto">

<input type="hidden" id="id">

<input type="text"
id="nombre"
placeholder="Nombre"
required>

<textarea
id="descripcion"
placeholder="Descripción"
required></textarea>

<input type="number"
id="precio"
placeholder="Precio"
required>

<input type="text"
id="categoria"
placeholder="Categoría"
required>

<input type="text"
id="imagen"
placeholder="URL imagen"
required>

<select id="disponible">

    <option value="1">
        Disponible
    </option>

    <option value="0">
        Agotado
    </option>

</select>

<button type="submit">
Guardar Producto
</button>

</form>

<table>

<thead>

<tr>

<th>ID</th>
<th>Imagen</th>
<th>Nombre</th>
<th>Precio</th>
<th>Categoría</th>
<th>Estado</th>
<th>Acciones</th>

</tr>

</thead>

<tbody id="tablaProductos">

</tbody>

</table>

<script>

const tabla =
document.getElementById("tablaProductos");

const form =
document.getElementById("formProducto");

async function cargarProductos(){

    const response =
    await fetch(
    "../backend/obtener_productos_admin.php"
    );

    const productos =
    await response.json();

    tabla.innerHTML = "";

    productos.forEach(producto => {

        tabla.innerHTML += `

        <tr>

        <td>${producto.id}</td>

        <td>
        <img src="${producto.imagen}">
        </td>

        <td>${producto.nombre}</td>

        <td>$${producto.precio}</td>

        <td>${producto.categoria}</td>

        <td>

        ${producto.disponible == 1
        ? "Disponible"
        : "Agotado"}

        </td>

        <td>

        <button
        class="editar"
        onclick='editarProducto(
        ${JSON.stringify(producto)}
        )'>
        Editar
        </button>

        <button
        class="eliminar"
        onclick="eliminarProducto(
        ${producto.id}
        )">
        Eliminar
        </button>

        </td>

        </tr>
        `;
    });
}

cargarProductos();

form.addEventListener("submit",
async (e)=>{

    e.preventDefault();

    const producto = {

        id:
        document.getElementById("id").value,

        nombre:
        document.getElementById("nombre").value,

        descripcion:
        document.getElementById("descripcion").value,

        precio:
        document.getElementById("precio").value,

        categoria:
        document.getElementById("categoria").value,

        imagen:
        document.getElementById("imagen").value,

        disponible:
        document.getElementById("disponible").value
    };

    const url = producto.id
    ? "../backend/editar_producto.php"
    : "../backend/agregar_producto.php";

    await fetch(url,{

        method:"POST",

        headers:{
            "Content-Type":
            "application/json"
        },

        body:JSON.stringify(producto)
    });

    form.reset();

    cargarProductos();
});

function editarProducto(producto){

    document.getElementById("id").value =
    producto.id;

    document.getElementById("nombre").value =
    producto.nombre;

    document.getElementById("descripcion").value =
    producto.descripcion;

    document.getElementById("precio").value =
    producto.precio;

    document.getElementById("categoria").value =
    producto.categoria;

    document.getElementById("imagen").value =
    producto.imagen;

    document.getElementById("disponible").value =
    producto.disponible;
}

async function eliminarProducto(id){

    if(!confirm(
    "¿Eliminar producto?"
    )) return;

    await fetch(
    "../backend/eliminar_producto.php",
    {

        method:"POST",

        headers:{
            "Content-Type":
            "application/json"
        },

        body:JSON.stringify({id})
    });

    cargarProductos();
}

window.history.forward();

</script>

</body>
</html>