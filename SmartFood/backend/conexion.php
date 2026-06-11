<?php

/*
========================================
CONFIGURACIÓN MYSQL
========================================
*/

$host = "127.0.0.1";
$user = "root";
$password = "";
$database = "smartfood";
$port = 3307;



mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try{

  

    $conn = new mysqli(

        $host,
        $user,
        $password,
        $database,
        $port

    );

  

    $conn->set_charset("utf8");

}catch(Exception $e){

    die(
        "Error de conexión: " .
        $e->getMessage()
    );
}

?>