<?php

header('Content-Type: application/json; charset=utf-8');

require_once 'conexion.php';

$sql = "SELECT * FROM productos";

$result = $conn->query($sql);

$productos = [];

if ($result && $result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

        $productos[] = [
            "id" => (int)$row["id"],
            "nombre" => $row["nombre"],
            "descripcion" => $row["descripcion"],
            "precio" => (float)$row["precio"],
            "categoria" => $row["categoria"],
            "imagen" => $row["imagen"],
            "disponible" => (int)$row["disponible"]
        ];
    }
}

echo json_encode($productos, JSON_UNESCAPED_UNICODE);

exit;

?>