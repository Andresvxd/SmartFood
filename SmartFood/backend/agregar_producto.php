<?php

header('Content-Type: application/json');

require_once 'conexion.php';

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$nombre = $data["nombre"];
$descripcion = $data["descripcion"];
$precio = $data["precio"];
$categoria = $data["categoria"];
$imagen = $data["imagen"];
$disponible = $data["disponible"];

$sql = "INSERT INTO productos
(nombre,descripcion,precio,categoria,imagen,disponible)
VALUES (?,?,?,?,?,?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ssdssi",
    $nombre,
    $descripcion,
    $precio,
    $categoria,
    $imagen,
    $disponible
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