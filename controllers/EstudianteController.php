<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Estudiante.php";

$database = new Database();
$db = $database->getConnection();
$estudianteObj = new Estudiante($db);

//  Guardar y actualizar estudiante
if(isset($_POST['guardar'])){
    $data = [
        'id_estudiante'   => $_POST['id_estudiante'] ?? null,
        'carnet'          => trim($_POST['carnet']),
        'nombre_completo' => trim($_POST['nombre_completo']),
        'carrera'         => trim($_POST['carrera']),
        'telefono'        => trim($_POST['telefono']),
        'correo'          => trim($_POST['correo']),
        'estado'          => $_POST['estado'] ?? 'ACTIVO'
    ];

    if(!empty($data['id_estudiante'])){
        // Actualizar
        $resultado = $estudianteObj->actualizarEstudiante($data);
    } else {
        // Insertar
        $resultado = $estudianteObj->guardarEstudiante($data);
    }

    if($resultado === true){
        //  Redirige al listado si todo salió bien
        header("Location: ../views/Estudiante/estudiantes.php");
        exit();
    } else {
        //  Guardamos el error en sesión y regresamos al formulario
        $_SESSION['error'] = $resultado;
        header("Location: ../views/Estudiante/estudiante_form.php" . (!empty($data['id_estudiante']) ? "?id=".$data['id_estudiante'] : ""));
        exit();
    }
}

//  Inactivar estudiante 
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $estudianteObj->inactivarEstudiante($id);
    header("Location: ../views/Estudiante/estudiantes.php");
    exit();
}
?>
