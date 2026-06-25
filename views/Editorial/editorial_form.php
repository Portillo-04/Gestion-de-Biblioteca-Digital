<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../../includes/auth.php";
require_once __DIR__ . "/../../models/Editorial.php";

$editorialObj = new Editorial($GLOBALS['db']);
$editorial = null;
if(isset($_GET['id'])){
    $editorial = $editorialObj->buscarEditorial($_GET['id']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $editorial ? "Editar Editorial" : "Nueva Editorial"; ?></title>
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
    <h3><?php echo $editorial ? "Editar Editorial" : "Nueva Editorial"; ?></h3>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form action="../../controllers/EditorialController.php" method="POST">
        <?php if($editorial): ?>
            <input type="hidden" name="id_editorial" value="<?php echo $editorial['id_editorial']; ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label>Nombre Editorial</label>
            <input type="text" name="nombre_editorial" class="form-control" required value="<?php echo $editorial['nombre_editorial'] ?? ''; ?>">
        </div>

        <div class="mb-3">
            <label>Estado</label>
            <select name="estado" class="form-select">
                <option value="ACTIVO" <?php echo ($editorial && $editorial['estado']=='ACTIVO')?'selected':''; ?>>ACTIVO</option>
                <option value="INACTIVO" <?php echo ($editorial && $editorial['estado']=='INACTIVO')?'selected':''; ?>>INACTIVO</option>
            </select>
        </div>

        <button type="submit" name="guardar" class="btn btn-success">Guardar</button>
        <a href="editoriales.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
<script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
