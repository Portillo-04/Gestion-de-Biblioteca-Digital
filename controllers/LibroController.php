<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Libro.php";

$database = new Database();
$db = $database->getConnection();
$libroObj = new Libro($db);

// Guardar nuevo libro
if(isset($_POST['guardar'])){
    $codigo     = $_POST['codigo_libro'];
    $titulo     = $_POST['titulo'];
    $editorial  = $_POST['editorial'];
    $stock      = $_POST['stock'];
    $estado     = $_POST['estado'];
    $categoria  = $_POST['categoria']; 

    try {
        $libroObj->agregarLibro($codigo, $titulo, $editorial, $stock, $estado, $categoria);
        $id_libro = $db->lastInsertId();

        // Guardar autores (autor1 obligatorio, autor2 opcional)
        if(!empty($_POST['autor1'])){
            $query = "INSERT INTO libro_autor (id_libro, id_autor) VALUES (:id_libro, :id_autor)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":id_libro", $id_libro, PDO::PARAM_INT);
            $stmt->bindParam(":id_autor", $_POST['autor1'], PDO::PARAM_INT);
            $stmt->execute();
        }
        if(!empty($_POST['autor2'])){
            $query = "INSERT INTO libro_autor (id_libro, id_autor) VALUES (:id_libro, :id_autor)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":id_libro", $id_libro, PDO::PARAM_INT);
            $stmt->bindParam(":id_autor", $_POST['autor2'], PDO::PARAM_INT);
            $stmt->execute();
        }

        header("Location: ../views/Libro/libros.php?success=1");
        exit();
    } catch (Exception $e) {
        header("Location: ../views/Libro/libro_form.php?error=codigo");
        exit();
    }
}

// Editar libro
if(isset($_POST['editar'])){
    $id_libro   = $_POST['id_libro'];
    $codigo     = $_POST['codigo_libro'];
    $titulo     = $_POST['titulo'];
    $editorial  = $_POST['editorial'];
    $stock      = $_POST['stock'];
    $estado     = $_POST['estado'];
    $categoria  = $_POST['categoria']; 

    try {
        $libroObj->editarLibro($id_libro, $codigo, $titulo, $editorial, $stock, $estado, $categoria);

        // Primero eliminar autores actuales
        $queryDel = "DELETE FROM libro_autor WHERE id_libro = :id_libro";
        $stmtDel = $db->prepare($queryDel);
        $stmtDel->bindParam(":id_libro", $id_libro, PDO::PARAM_INT);
        $stmtDel->execute();

        // Guardar autores nuevos
        if(!empty($_POST['autor1'])){
            $query = "INSERT INTO libro_autor (id_libro, id_autor) VALUES (:id_libro, :id_autor)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":id_libro", $id_libro, PDO::PARAM_INT);
            $stmt->bindParam(":id_autor", $_POST['autor1'], PDO::PARAM_INT);
            $stmt->execute();
        }
        if(!empty($_POST['autor2'])){
            $query = "INSERT INTO libro_autor (id_libro, id_autor) VALUES (:id_libro, :id_autor)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":id_libro", $id_libro, PDO::PARAM_INT);
            $stmt->bindParam(":id_autor", $_POST['autor2'], PDO::PARAM_INT);
            $stmt->execute();
        }

        header("Location: ../views/Libro/libros.php?update=1");
        exit();
    } catch (Exception $e) {
        header("Location: ../views/Libro/libro_form.php?id=$id_libro&error=codigo");
        exit();
    }
}

// Inactivar libro
if(isset($_GET['eliminar'])){
    $id_libro = (int)$_GET['eliminar'];
    $libroObj->inactivarLibro($id_libro);
    header("Location: ../views/Libro/libros.php?inactivo=1");
    exit();
}

// Marcar libro prestado
if(isset($_GET['prestado'])){
    $id_libro = (int)$_GET['prestado'];
    $libroObj->marcarPrestado($id_libro);
    header("Location: ../views/Libro/libros.php?prestado=1");
    exit();
}

// Marcar libro disponible
if(isset($_GET['disponible'])){
    $id_libro = (int)$_GET['disponible'];
    $libroObj->marcarDisponible($id_libro);
    header("Location: ../views/Libro/libros.php?disponible=1");
    exit();
}
?>
