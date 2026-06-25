<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Categoria.php";

$database = new Database();
$db = $database->getConnection();
$categoriaObj = new Categoria($db);

// Guarda y actualiza categoría
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'id_categoria'     => $_POST['id_categoria'] ?? null,
        'nombre_categoria' => $_POST['nombre_categoria'] ?? '',
        'descripcion'      => $_POST['descripcion'] ?? '',
        'estado'           => $_POST['estado'] ?? 'ACTIVO'
    ];

    $resultado = $categoriaObj->guardarCategoria($data);

    if ($resultado) {
        header("Location: ../views/Categoria/categorias.php");
        exit();
    } else {
        echo "Error al guardar la categoría.";
    }
}

// Inactiva categoría
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $categoriaObj->inactivarCategoria($id);
    header("Location: ../views/Categoria/categorias.php");
    exit();
}
?>
