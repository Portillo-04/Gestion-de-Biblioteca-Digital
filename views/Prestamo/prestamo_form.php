<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../../models/Estudiante.php";
require_once __DIR__ . "/../../models/Libro.php";

$database = new Database();
$db = $database->getConnection();

$estudianteObj = new Estudiante($db);
$libroObj      = new Libro($db);

$estudiantes = $estudianteObj->listarEstudiantes();
$libros      = $libroObj->listarLibros();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Préstamo</title>
    <link href="../../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
            <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Biblioteca Digital</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <span class="nav-link">Usuario: <?php echo $_SESSION['usuario']; ?></span>
        </li>
        <!-- Botón Inicio -->
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
    <h3>Nuevo Préstamo</h3>

    <?php if(isset($_GET['error']) && $_GET['error']=='pendiente_libro'): ?>
        <div class="alert alert-danger">
            El estudiante ya tiene un libro pendiente y no puede solicitar otro.
        </div>
    <?php endif; ?>

    <form action="../../controllers/PrestamoController.php" method="POST">
        <!-- Selección de estudiante -->
        <div class="mb-3">
            <label>Estudiante</label>
            <select name="id_estudiante" id="id_estudiante" class="form-select" required>
                <option value="">Seleccione...</option>
                <?php foreach($estudiantes as $est): ?>
                    <option value="<?= $est['id_estudiante']; ?>">
                        <?= $est['carnet']." - ".$est['nombre_completo']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Si tiene préstamo pendiente -->
        <div id="alertaPrestamo" class="alert alert-danger" style="display:none;">
            Este estudiante ya tiene un libro pendiente y no puede solicitar otro.
        </div>


        <div class="mb-3" id="librosContainer" style="display:none;">
            <label>Libro</label>
            <select name="id_libro" id="id_libro" class="form-select" required>
                <option value="">Seleccione...</option>
                <?php foreach($libros as $l): ?>
                    <?php if($l['estado']=='DISPONIBLE'): ?>
                        <option value="<?= $l['id_libro']; ?>">
                            <?= $l['titulo']." (".$l['nombre_editorial'].")"; ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3" id="fechaEntregaContainer" style="display:none;">
            <label>Fecha de Entrega</label>
            <input type="text" id="fecha_entrega" name="fecha_entrega" class="form-control" readonly>
        </div>

        <button type="submit" name="guardar" class="btn btn-success">Registrar Préstamo</button>
        <a href="prestamos.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('id_estudiante').addEventListener('change', function(){
    let idEst = this.value;
    if(idEst){
        fetch('../../controllers/PrestamoController.php?checkPendiente='+idEst)
            .then(res => res.json())
            .then(data => {
                if(data.pendiente){
                    document.getElementById('alertaPrestamo').style.display = 'block';
                    document.getElementById('librosContainer').style.display = 'none';
                    document.getElementById('fechaEntregaContainer').style.display = 'none';
                    document.querySelector('button[name="guardar"]').disabled = true;
                } else {
                    document.getElementById('alertaPrestamo').style.display = 'none';
                    document.getElementById('librosContainer').style.display = 'block';
                    document.getElementById('fechaEntregaContainer').style.display = 'block';
                    document.querySelector('button[name="guardar"]').disabled = false;

                    // Calcular fecha de entrega 30 días después de habiles
                    let hoy = new Date();
                    hoy.setDate(hoy.getDate() + 30);
                    let fechaEntrega = hoy.toISOString().split('T')[0];
                    document.getElementById('fecha_entrega').value = fechaEntrega;
                }
            });
    }
});
</script>
</body>
</html>
