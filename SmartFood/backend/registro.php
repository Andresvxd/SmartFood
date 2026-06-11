<?php

header("Content-Type: application/json");

require_once "conexion.php";

/*
=====================================
LEER DATOS
=====================================
*/

$data = json_decode(
    file_get_contents("php://input"),
    true
);

/*
=====================================
VALIDAR DATOS
=====================================
*/

if (
    !$data ||
    empty($data["nombre"]) ||
    empty($data["correo"]) ||
    empty($data["password"])
) {

    echo json_encode([
        "success" => false,
        "message" => "Todos los campos son obligatorios"
    ]);

    exit();
}

/*
=====================================
OBTENER DATOS
=====================================
*/

$nombre = trim($data["nombre"]);
$correo = trim($data["correo"]);
$password = trim($data["password"]);

/*
=====================================
VALIDAR CORREO EXISTENTE
=====================================
*/

$sqlCorreo = "
SELECT id
FROM usuarios
WHERE correo = ?
LIMIT 1
";

$stmtCorreo = $conn->prepare($sqlCorreo);

$stmtCorreo->bind_param(
    "s",
    $correo
);

$stmtCorreo->execute();

$resultCorreo = $stmtCorreo->get_result();

if ($resultCorreo->num_rows > 0) {

    echo json_encode([
        "success" => false,
        "message" => "El correo ya está registrado"
    ]);

    exit();
}

/*
=====================================
REGISTRAR USUARIO
=====================================
*/

$rol = "cliente";

$sql = "
INSERT INTO usuarios
(
    nombre,
    correo,
    password,
    rol
)
VALUES
(
    ?, ?, ?, ?
)
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ssss",
    $nombre,
    $correo,
    $password,
    $rol
);

if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Usuario registrado correctamente"
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Error al registrar usuario"
    ]);
}

$stmt->close();
$conn->close();

?>