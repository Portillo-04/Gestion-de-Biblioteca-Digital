<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Editorial.php";

$database = new Database();
$db = $database->getConnection();
$editorialObj = new Editorial($db);

//  Guarda y actualiza editorial
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'id_editorial'    => $_POST['id_editorial'] ?? null,
        'nombre_editorial'=> trim($_POST['nombre_editorial']),
        'estado'          => $_POST['estado'] ?? 'ACTIVO'
    ];

    $resultado = $editorialObj->guardarEditorial($data);

    if ($resultado) {
        header("Location: ../views/Editorial/editoriales.php");
        exit();
    } else {
        $_SESSION['error'] = "Error al guardar la editorial.";
        header("Location: ../views/Editorial/editorial_form.php" . (!empty($data['id_editorial']) ? "?id=".$data['id_editorial'] : ""));
        exit();
    }
}

//  Inactiva editorial
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $editorialObj->eliminarEditorial($id);
    header("Location: ../views/Editorial/editoriales.php");
    exit();
}

?>
