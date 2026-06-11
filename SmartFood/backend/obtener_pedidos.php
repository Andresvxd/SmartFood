<?php

header('Content-Type: application/json; charset=utf-8');

require_once 'conexion.php';

$sql = "SELECT * FROM pedidos ORDER BY id DESC";

$result = $conn->query($sql);

$pedidos = [];

if ($result) {

    while ($row = $result->fetch_assoc()) {

        $pedidos[] = [
            "id" => $row["id"],
            "cliente_id" => $row["cliente_id"],
            "total" => $row["total"],
            "metodo_pago" => $row["metodo_pago"],
            "fecha" => $row["fecha"]
        ];
    }
}

echo json_encode($pedidos);

?>