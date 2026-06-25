<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Autor.php";

$database = new Database();
$db = $database->getConnection();
$autorObj = new Autor($db);

// Guarda y actualiza autor
if(isset($_POST['guardar'])){
    $resultado = $autorObj->guardarAutor($_POST);

    if($resultado === true){
        header("Location: ../views/Autor/autores.php");
        exit();
    } else {
        // Mostrar el error en pantalla
        echo "<pre>$resultado</pre>";
        exit();
    }
}

// Marcar INACTIVO
if(isset($_GET['delete'])){
    $autorObj->eliminarAutor($_GET['delete']);
    header("Location: ../views/Autor/autores.php?inactivo=1");
    exit();
}
