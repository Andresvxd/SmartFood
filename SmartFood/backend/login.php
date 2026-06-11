<?php

session_start();

header("Content-Type: application/json");

require_once "conexion.php";

/*
=====================================
LEER DATOS JSON
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
    empty($data["correo"]) ||
    empty($data["password"])
) {

    echo json_encode([
        "success" => false,
        "message" => "Datos incompletos"
    ]);

    exit();
}

/*
=====================================
LIMPIAR DATOS
=====================================
*/

$correo = trim($data["correo"]);
$password = trim($data["password"]);

/*
=====================================
BUSCAR USUARIO
=====================================
*/

$sql = "
    SELECT id, nombre, correo, password, rol
    FROM usuarios
    WHERE correo = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    echo json_encode([
        "success" => false,
        "message" => "Error en la consulta"
    ]);

    exit();
}

$stmt->bind_param("s", $correo);
$stmt->execute();

$result = $stmt->get_result();

/*
=====================================
VALIDAR USUARIO
=====================================
*/

if ($result->num_rows === 0) {

    echo json_encode([
        "success" => false,
        "message" => "Correo no encontrado"
    ]);

    exit();
}

$user = $result->fetch_assoc();

/*
=====================================
VALIDAR CONTRASEÑA
=====================================
*/

if ($password !== $user["password"]) {

    echo json_encode([
        "success" => false,
        "message" => "Contraseña incorrecta"
    ]);

    exit();
}

/*
=====================================
CREAR SESIÓN
=====================================
*/

$_SESSION["id"] = $user["id"];
$_SESSION["nombre"] = $user["nombre"];
$_SESSION["correo"] = $user["correo"];
$_SESSION["rol"] = $user["rol"];

/*
=====================================
REDIRECCIÓN POR ROL
=====================================
*/

$redirect = "cliente/menu.html";

if ($user["rol"] === "admin") {
    $redirect = "admin/dashboard.php";
}

/*
=====================================
RESPUESTA EXITOSA
=====================================
*/

echo json_encode([
    "success" => true,
    "message" => "Login exitoso",
    "redirect" => $redirect,
    "user" => [
        "id" => $user["id"],
        "nombre" => $user["nombre"],
        "correo" => $user["correo"],
        "rol" => $user["rol"]
    ]
]);

$stmt->close();
$conn->close();

?>
 