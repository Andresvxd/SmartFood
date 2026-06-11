<?php

header('Content-Type: application/json; charset=utf-8');

require_once 'conexion.php';

/*
=========================
TOTAL PEDIDOS
=========================
*/

$pedidos = 0;

$res = $conn->query(
    "SELECT COUNT(*) AS total FROM pedidos"
);

if ($res) {

    $pedidos = $res->fetch_assoc()["total"];
}

/*
=========================
TOTAL PRODUCTOS
=========================
*/

$productos = 0;

$res = $conn->query(
    "SELECT COUNT(*) AS total FROM productos"
);

if ($res) {

    $productos = $res->fetch_assoc()["total"];
}

/*
=========================
TOTAL INGRESOS
=========================
*/

$ingresos = 0;

$res = $conn->query(
    "SELECT SUM(total) AS total FROM pedidos"
);

if ($res) {

    $ingresos =
        $res->fetch_assoc()["total"] ?? 0;
}

/*
=========================
PEDIDOS RECIENTES
=========================
*/

$recientes = [];

$res = $conn->query(
    "SELECT * FROM pedidos
     ORDER BY id DESC
     LIMIT 5"
);

if ($res) {

    while($row = $res->fetch_assoc()) {

        $recientes[] = $row;
    }
}

echo json_encode([

    "pedidos" => $pedidos,

    "productos" => $productos,

    "ingresos" => $ingresos,

    "recientes" => $recientes

]);

?>
