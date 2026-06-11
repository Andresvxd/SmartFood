<?php

header('Content-Type: application/json; charset=utf-8');
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {

    echo json_encode([
        "success" => false,
        "message" => "Datos inválidos"
    ]);

    exit;
}

if (!isset($_SESSION["id"])) {

    echo json_encode([
        "success" => false,
        "message" => "Sesión no válida"
    ]);

    exit;
}

$cliente_id = $_SESSION["id"];

$total = floatval($data["total"] ?? 0);

$metodo_pago = $data["metodo_pago"] ?? "Efectivo";

$items = $data["items"] ?? [];

if (empty($items)) {

    echo json_encode([
        "success" => false,
        "message" => "Carrito vacío"
    ]);

    exit;
}

/*
=========================
INSERTAR PEDIDO
=========================
*/

$sql = "INSERT INTO pedidos 
(cliente_id, total, metodo_pago)
VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    echo json_encode([
        "success" => false,
        "message" => $conn->error
    ]);

    exit;
}

$stmt->bind_param(
    "ids",
    $cliente_id,
    $total,
    $metodo_pago
);

if (!$stmt->execute()) {

    echo json_encode([
        "success" => false,
        "message" => $stmt->error
    ]);

    exit;
}

$pedido_id = $conn->insert_id;

/*
=========================
DETALLE PEDIDO
=========================
*/

$detalle_sql = "INSERT INTO detalle_pedido
(pedido_id, producto_id, cantidad, subtotal)
VALUES (?, ?, ?, ?)";

$detalle_stmt = $conn->prepare($detalle_sql);

if (!$detalle_stmt) {

    echo json_encode([
        "success" => false,
        "message" => $conn->error
    ]);

    exit;
}

foreach ($items as $item) {

    $producto_id = intval($item["id"]);
    $cantidad = intval($item["cantidad"]);
    $subtotal = floatval(
        $item["precio"] * $cantidad
    );

    $detalle_stmt->bind_param(
        "iiid",
        $pedido_id,
        $producto_id,
        $cantidad,
        $subtotal
    );

    $detalle_stmt->execute();
}

echo json_encode([
    "success" => true,
    "pedido_id" => $pedido_id
]);

$conn->close();

?>



