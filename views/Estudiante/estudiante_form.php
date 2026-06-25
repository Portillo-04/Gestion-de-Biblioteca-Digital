<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../../includes/auth.php";
require_once __DIR__ . "/../../models/Estudiante.php";

$estudianteObj = new Estudiante($GLOBALS['db']);
$estudiante = null;
if(isset($_GET['id'])){
    $estudiante = $estudianteObj->buscarEstudiante($_GET['id']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $estudiante ? "Editar Estudiante" : "Nuevo Estudiante"; ?></title>
    <link href="../../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
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
    <h3><?php echo $estudiante ? "Editar Estudiante" : "Nuevo Estudiante"; ?></h3>

    <!-- Muestra error si existe -->
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']); 
            ?>
        </div>
    <?php endif; ?>

    <form action="../../controllers/EstudianteController.php" method="POST">
        <?php if($estudiante): ?>
            <input type="hidden" name="id_estudiante" value="<?php echo $estudiante['id_estudiante']; ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label>Carnet</label>
            <input type="text" name="carnet" class="form-control" required value="<?php echo $estudiante['carnet'] ?? ''; ?>">
        </div>

        <div class="mb-3">
            <label>Nombre Completo</label>
            <input type="text" name="nombre_completo" class="form-control" required value="<?php echo $estudiante['nombre_completo'] ?? ''; ?>">
        </div>

        <div class="mb-3">
            <label for="carrera" class="form-label">Carrera</label>
            <select name="carrera" class="form-select" required>
                <option value="">Seleccione...</option>
                <option value="Técnico En Ingeniería En Desarrollo De Software" <?php echo ($estudiante && $estudiante['carrera']=='Técnico En Ingeniería En Desarrollo De Software')?'selected':''; ?>>Técnico En Ingeniería En Desarrollo De Software</option>
                <option value="Técnico En Hostelería Y Turismo" <?php echo ($estudiante && $estudiante['carrera']=='Técnico En Hostelería Y Turismo')?'selected':''; ?>>Técnico En Hostelería Y Turismo</option>
                <option value="Técnico En Gastronomía" <?php echo ($estudiante && $estudiante['carrera']=='Técnico En Gastronomía')?'selected':''; ?>>Técnico En Gastronomía</option>
                <option value="Ingeniería En Logística Y Aduanas" <?php echo ($estudiante && $estudiante['carrera']=='Ingeniería En Logística Y Aduanas')?'selected':''; ?>>Ingeniería En Logística Y Aduanas</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Teléfono</label>
            <input type="text" name="telefono" class="form-control" value="<?php echo $estudiante['telefono'] ?? ''; ?>">
        </div>

        <div class="mb-3">
            <label>Correo</label>
            <input type="email" name="correo" class="form-control" required value="<?php echo $estudiante['correo'] ?? ''; ?>">
        </div>

        <div class="mb-3">
            <label>Estado</label>
            <select name="estado" class="form-select">
                <option value="ACTIVO" <?php echo ($estudiante && $estudiante['estado']=='ACTIVO')?'selected':''; ?>>ACTIVO</option>
                <option value="INACTIVO" <?php echo ($estudiante && $estudiante['estado']=='INACTIVO')?'selected':''; ?>>INACTIVO</option>
            </select>
        </div>

        <button type="submit" name="guardar" class="btn btn-success">Guardar</button>
        <a href="estudiantes.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
