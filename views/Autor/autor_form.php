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

$autor = null;
if(isset($_GET['id'])){
    $autor = $autorObj->buscarAutor($_GET['id']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $autor ? "Editar Autor" : "Nuevo Autor"; ?></title>
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
    <h3><?php echo $autor ? "Editar Autor" : "Nuevo Autor"; ?></h3>
    <form action="../../controllers/AutorController.php" method="POST">
        <?php if($autor): ?>
            <input type="hidden" name="id_autor" value="<?php echo $autor['id_autor']; ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label>Nombre</label>
           <input type="text" name="nombre_autor" class="form-control" required value="<?php echo $autor['nombre_autor'] ?? ''; ?>">
        </div>

        <div class="mb-3">
            <label>Apellido</label>
            <input type="text" name="apellido" class="form-control" required value="<?php echo $autor['apellido'] ?? ''; ?>">
        </div>

        <div class="mb-3">
            <label>Nacionalidad</label>
            <input type="text" name="nacionalidad" class="form-control" value="<?php echo $autor['nacionalidad'] ?? ''; ?>">
        </div>

        <div class="mb-3">
            <label>Fecha de Nacimiento</label>
            <input type="date" name="fecha_nacimiento" class="form-control" value="<?php echo $autor['fecha_nacimiento'] ?? ''; ?>">
        </div>

        <div class="mb-3">
            <label>Biografía</label>
            <textarea name="biografia" class="form-control"><?php echo $autor['biografia'] ?? ''; ?></textarea>
        </div>

        <div class="mb-3">
            <label>Estado</label>
            <select name="estado" class="form-select" required>
                <option value="ACTIVO" <?php echo ($autor && $autor['estado']=='ACTIVO')?'selected':''; ?>>ACTIVO</option>
                <option value="INACTIVO" <?php echo ($autor && $autor['estado']=='INACTIVO')?'selected':''; ?>>INACTIVO</option>
            </select>
        </div>

        <button type="submit" name="guardar" class="btn btn-success">Guardar</button>
        <a href="autores.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
<script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
