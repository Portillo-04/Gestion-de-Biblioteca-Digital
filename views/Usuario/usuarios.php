<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../../includes/auth.php";
require_once __DIR__ . "/../../models/Usuario.php";

$usuarioObj = new Usuario($GLOBALS['db']);

if(isset($_GET['criterio']) && $_GET['criterio'] != ""){
    $usuarios = $usuarioObj->buscarUsuarios($_GET['criterio']);
} else {
    $usuarios = $usuarioObj->listarUsuarios();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
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
    <h3 class="text-center">Gestión de Usuarios</h3>

    <!-- Botón + lupa de filtración -->
    <div class="d-flex justify-content-between mb-3">
        <a href="usuario_form.php" class="btn btn-primary btn-sm">Nuevo Usuario</a>
        <form method="GET" class="d-flex" style="max-width:300px; margin-left:auto;">
            <input type="text" name="criterio" class="form-control form-control-sm me-2" placeholder="Buscar...">
            <button type="submit" class="btn btn-outline-secondary btn-sm">🔍</button>
        </form>
    </div>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Correo</th>
                <th>Estado</th>
                <th>Fecha Creación</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($usuarios)): ?>
                <?php foreach($usuarios as $u): ?>
                <tr>
                    <td><?php echo $u['id_usuario']; ?></td>
                    <td><?php echo $u['usuario']; ?></td>
                    <td><?php echo $u['correo']; ?></td>
                    <td>
                        <?php if(strtoupper($u['estado']) === 'ACTIVO'): ?>
                            <span class="badge bg-success">ACTIVO</span>
                        <?php else: ?>
                            <span class="badge bg-danger">INACTIVO</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php 
                            $fecha = new DateTime($u['fecha_creacion']);
                            echo $fecha->format('d-m-Y H:i:s');
                        ?>
                    </td>
                    <td><?php echo $u['id_rol']; ?></td>
                    <td>
                        <a href="usuario_form.php?id=<?php echo $u['id_usuario']; ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="../../controllers/UsuarioController.php?delete=<?php echo $u['id_usuario']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este usuario?');">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No hay usuarios registrados</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
