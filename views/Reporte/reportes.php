<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes de Biblioteca</title>
    <link href="../../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">Biblioteca Digital</a>
        <div class="collapse navbar-collapse">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
              <span class="nav-link">Usuario: <?php echo $_SESSION['usuario']; ?></span>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="../dashboard.php">Inicio</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="../../logout.php">Cerrar Sesión</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

<div class="container mt-4">
    <h3 class="text-center">Reportes de Biblioteca</h3>

    <!-- Botones de reportes -->
    <div class="d-flex flex-column align-items-center mt-3">
        <a href="../../controllers/ReporteController.php?accion=prestamos_activos" 
           class="btn btn-success mb-2" target="_blank">
           Generar Reporte de Préstamos Activos (PDF)
        </a>

        <a href="../../controllers/ReporteController.php?accion=libros_mas_prestados" 
           class="btn btn-info mb-2" target="_blank">
           Generar Reporte de Libros más Prestados (PDF)
        </a>

        <a href="../../controllers/ReporteController.php?accion=usuarios_activos" 
           class="btn btn-warning mb-2" target="_blank">
           Generar Reporte de Usuarios (PDF)
        </a>
    </div>
</div>

<script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
