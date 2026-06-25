<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../../models/Prestamo.php";

$database = new Database();
$db = $database->getConnection();
$prestamoObj = new Prestamo($db);

$prestamos = $prestamoObj->listarPrestamos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Préstamos</title>
    <link href="../../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Biblioteca Digital</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><span class="nav-link">Usuario: <?php echo $_SESSION['usuario']; ?></span></li>
        <li class="nav-item"><a class="nav-link" href="../dashboard.php">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="../../logout.php">Cerrar Sesión</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
    <h3 class="text-center">Gestión de Préstamos</h3>

    <div class="d-flex justify-content-between mb-3">
        <a href="prestamo_form.php" class="btn btn-primary btn-sm">Nuevo Préstamo</a>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID Detalle</th>
                <th>Estudiante</th>
                <th>Usuario</th>
                <th>Libro</th>
                <th>Fecha Préstamo</th>
                <th>Fecha Entrega</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($prestamos)): ?>
                <?php foreach($prestamos as $p): ?>
                <tr>
                    <td><?= $p['id_detalle']; ?></td>
                    <td><?= $p['nombre_completo']; ?> (<?= $p['carnet']; ?>)</td>
                    <td><?= $p['usuario']; ?></td>
                    <td><?= $p['libro']; ?></td>
                    <td><?= $p['fecha_prestamo']; ?></td>
                    <td><?= $p['fecha_entrega'] ?? '-'; ?></td>
                    <td>
                        <?php if($p['estado_libro']=='PRESTADO'): ?>
                            <span class="badge bg-warning text-dark">PRESTADO</span>
                        <?php elseif($p['estado_libro']=='DEVUELTO'): ?>
                            <span class="badge bg-success">DEVUELTO</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($p['estado_libro']=='PRESTADO'): ?>
                            <a href="../../controllers/PrestamoController.php?devolver=<?= $p['id_detalle']; ?>" 
                               class="btn btn-success btn-sm"
                               onclick="return confirm('¿Registrar devolución de este préstamo?');">
                               Registrar Devolución
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8" class="text-center">No hay préstamos registrados</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
