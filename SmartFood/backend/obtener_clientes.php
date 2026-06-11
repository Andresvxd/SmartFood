<?php

header("Content-Type: application/json");

include "conexion.php";

$sql = "SELECT * FROM clientes ORDER BY id DESC";

$result = $conn->query($sql);

$clientes = [];

if($result){

    while($row = $result->fetch_assoc()){

        $clientes[] = $row;
    }
}

echo json_encode($clientes);

?>