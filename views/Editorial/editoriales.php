<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../../includes/auth.php";
require_once __DIR__ . "/../../models/Editorial.php";

$editorialObj = new Editorial($GLOBALS['db']);
$busqueda = $_GET['busqueda'] ?? '';
$editoriales = $busqueda ? $editorialObj->buscarEditoriales($busqueda) : $editorialObj->listarEditoriales();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Editoriales</title>
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
    <h3 class="text-center">Gestión de Editoriales</h3>

    <div class="d-flex justify-content-between mb-3">
        <a href="editorial_form.php" class="btn btn-primary">Nueva Editorial</a>
        <form method="GET" class="d-flex" style="max-width:300px;">
            <input type="text" name="busqueda" class="form-control me-2" placeholder="Buscar por nombre...">
            <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
        </form>
    </div>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Nombre Editorial</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($editoriales)): ?>
                <?php foreach($editoriales as $ed): ?>
                <tr>
                    <td><?php echo $ed['id_editorial']; ?></td>
                    <td><?php echo $ed['nombre_editorial']; ?></td>
                    <td>
                        <?php if($ed['estado'] === 'ACTIVO'): ?>
                            <span class="badge bg-success">ACTIVO</span>
                        <?php else: ?>
                            <span class="badge bg-danger">INACTIVO</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="editorial_form.php?id=<?php echo $ed['id_editorial']; ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="../../controllers/EditorialController.php?delete=<?php echo $ed['id_editorial']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar esta editorial?');">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4" class="text-center">No hay editoriales registradas</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>