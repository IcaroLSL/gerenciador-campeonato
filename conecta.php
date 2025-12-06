<?php 
    $comp = "localhost";
    $user = "root";
    $senha = "";
    $db = "db_campeonato";

    $conn = new mysqli($comp, $user, $senha, $db);

    if ($conn->connect_error){
        die("Falha na conexão: " . $conn->connect_error);
    }

    $conn->set_charset("utf8");
?>