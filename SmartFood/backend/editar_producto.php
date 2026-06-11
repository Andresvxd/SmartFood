<?php

header('Content-Type: application/json');

require_once 'conexion.php';

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$id = $data["id"];
$nombre = $data["nombre"];
$descripcion = $data["descripcion"];
$precio = $data["precio"];
$categoria = $data["categoria"];
$imagen = $data["imagen"];
$disponible = $data["disponible"];

$sql = "UPDATE productos SET
nombre=?,
descripcion=?,
precio=?,
categoria=?,
imagen=?,
disponible=?
WHERE id=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ssdssii",
    $nombre,
    $descripcion,
    $precio,
    $categoria,
    $imagen,
    $disponible,
    $id
);

if($stmt->execute()){

    echo json_encode([
        "success" => true
    ]);

}else{

    echo json_encode([
        "success" => false
    ]);
}

?>