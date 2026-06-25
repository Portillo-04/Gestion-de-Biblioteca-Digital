<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../../models/Autor.php";

$database = new Database();
$db = $database->getConnection();
$autorObj = new Autor($db);

if(isset($_GET['criterio']) && $_GET['criterio'] != ""){
    $autores = $autorObj->buscarAutores($_GET['criterio']);
} else {
    $autores = $autorObj->listarAutores();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Autores</title>
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
    <h3 class="text-center">Gestión de Autores</h3>
    <a href="autor_form.php" class="btn btn-primary mb-3">Nuevo Autor</a>

    
    <?php if(isset($_GET['inactivo'])): ?>
        <div class="alert alert-warning">Autor marcado como INACTIVO.</div>
    <?php endif; ?>


    <form method="GET" class="d-flex mb-3 justify-content-end" style="max-width:300px; margin-left:auto;">
        <input type="text" name="criterio" class="form-control form-control-sm me-2" placeholder="Buscar...">
        <button type="submit" class="btn btn-outline-secondary btn-sm">🔍</button>
    </form>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre Completo</th>
                <th>Nacionalidad</th>
                <th>Fecha Nacimiento</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($autores)): ?>
                <?php foreach($autores as $autor): ?>
                <tr>
                    <td><?php echo $autor['id_autor']; ?></td>
                    <td><?php echo $autor['nombre_autor']." ".$autor['apellido']; ?></td>
                    <td><?php echo $autor['nacionalidad']; ?></td>
                    <td><?php echo date("d-m-Y", strtotime($autor['fecha_nacimiento'])); ?></td>
                    <td>
                        <?php if($autor['estado'] === 'ACTIVO'): ?>
                            <span class="badge bg-success">ACTIVO</span>
                        <?php else: ?>
                            <span class="badge bg-danger">INACTIVO</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="autor_form.php?id=<?php echo $autor['id_autor']; ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="../../controllers/AutorController.php?delete=<?php echo $autor['id_autor']; ?>" 
                           class="btn btn-danger btn-sm" 
                           onclick="return confirm('¿Seguro que deseas inactivar este autor?');">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No hay autores registrados</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
