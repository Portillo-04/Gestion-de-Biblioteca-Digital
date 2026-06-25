<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Prestamo.php";

$database = new Database();
$db = $database->getConnection();
$prestamoObj = new Prestamo($db);

//  Registrar préstamo
if(isset($_POST['guardar'])){
    $id_estudiante = $_POST['id_estudiante'];
    $id_usuario    = $_SESSION['id_usuario']; // bibliotecario que registra
    $id_libro      = $_POST['id_libro'];

    // Validar si el estudiante tiene préstamo pendiente
    if($prestamoObj->tienePrestamoPendiente($id_estudiante)){
        header("Location: ../views/Prestamo/prestamo_form.php?error=pendiente_libro");
        exit();
    }

    // Crear cabecera en tabla prestamos
    $queryCabecera = "INSERT INTO prestamos (id_estudiante, id_usuario) VALUES (:id_estudiante, :id_usuario)";
    $stmt = $db->prepare($queryCabecera);
    $stmt->bindParam(":id_estudiante", $id_estudiante);
    $stmt->bindParam(":id_usuario", $id_usuario);
    $stmt->execute();
    $id_prestamo = $db->lastInsertId();

    // Calcular fecha de entrega (30 días habiles a partir de la fecha actual)
    $fechaEntrega = date('Y-m-d', strtotime('+30 days'));

    // Registrar detalle del préstamo
    $queryDetalle = "INSERT INTO detalle_prestamo (id_prestamo, id_libro, fecha_prestamo, fecha_entrega, estado_libro)
                     VALUES (:id_prestamo, :id_libro, NOW(), :fecha_entrega, 'PRESTADO')";
    $stmtDet = $db->prepare($queryDetalle);
    $stmtDet->bindParam(":id_prestamo", $id_prestamo);
    $stmtDet->bindParam(":id_libro", $id_libro);
    $stmtDet->bindParam(":fecha_entrega", $fechaEntrega);
    $stmtDet->execute();

    //  Actualizar estado del libro en la tabla libros
    $queryLibro = "UPDATE libros SET estado = 'PRESTADO' WHERE id_libro = :id_libro";
    $stmtLibro = $db->prepare($queryLibro);
    $stmtLibro->bindParam(":id_libro", $id_libro);
    $stmtLibro->execute();

    header("Location: ../views/Prestamo/prestamos.php?success=1");
    exit();
}

//  Registrar devolución
if(isset($_GET['devolver'])){
    $idDetalle = (int)$_GET['devolver'];
    $prestamoObj->registrarDevolucion($idDetalle);

    //  Obtener el libro asociado y actualizar estado
    $queryGetLibro = "SELECT id_libro FROM detalle_prestamo WHERE id_detalle = :id";
    $stmtGet = $db->prepare($queryGetLibro);
    $stmtGet->bindParam(":id", $idDetalle, PDO::PARAM_INT);
    $stmtGet->execute();
    $row = $stmtGet->fetch(PDO::FETCH_ASSOC);

    if($row){
        $queryLibro = "UPDATE libros SET estado = 'DISPONIBLE' WHERE id_libro = :id_libro";
        $stmtLibro = $db->prepare($queryLibro);
        $stmtLibro->bindParam(":id_libro", $row['id_libro']);
        $stmtLibro->execute();
    }

    header("Location: ../views/Prestamo/prestamos.php?devolucion=1");
    exit();
}

//  Para validación AJAX (cuando seleccionas estudiante en el form)
if(isset($_GET['checkPendiente'])){
    $id_estudiante = (int)$_GET['checkPendiente'];
    $pendiente = $prestamoObj->tienePrestamoPendiente($id_estudiante);
    echo json_encode(['pendiente' => $pendiente]);
    exit();
}
?>
