<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../../includes/auth.php";
require_once __DIR__ . "/../../models/Usuario.php";

$usuarioObj = new Usuario($GLOBALS['db']);
$usuario = null;
if(isset($_GET['id'])){
    $usuario = $usuarioObj->buscarUsuario($_GET['id']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $usuario ? "Editar Usuario" : "Nuevo Usuario"; ?></title>
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
    <h3><?php echo $usuario ? "Editar Usuario" : "Nuevo Usuario"; ?></h3>
    <form action="../../controllers/UsuarioController.php" method="POST">
        <?php if($usuario): ?>
            <input type="hidden" name="id_usuario" value="<?php echo $usuario['id_usuario']; ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label>Usuario</label>
            <input type="text" name="usuario" class="form-control" 
                   required value="<?php echo $usuario['usuario'] ?? ''; ?>">
        </div>

        <div class="mb-3">
            <label>Correo</label>
            <input type="email" name="correo" class="form-control" 
                   required value="<?php echo $usuario['correo'] ?? ''; ?>">
        </div>

        <div class="mb-3">
            <label>Contraseña <?php echo $usuario ? "" : ""; ?></label>
            <input type="password" name="password" class="form-control" 
                   <?php echo $usuario ? "" : "required"; ?>>
        </div>

        <div class="mb-3">
            <label>Rol</label>
            <select name="id_rol" class="form-select" required>
                <option value="">Seleccione...</option>
                <option value="1" <?php echo ($usuario && $usuario['id_rol']==1)?'selected':''; ?>>Administrador</option>
                <option value="2" <?php echo ($usuario && $usuario['id_rol']==2)?'selected':''; ?>>Bibliotecario</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Estado</label>
            <select name="estado" class="form-select" required>
                <option value="ACTIVO" <?php echo ($usuario && $usuario['estado']=='ACTIVO')?'selected':''; ?>>ACTIVO</option>
                <option value="INACTIVO" <?php echo ($usuario && $usuario['estado']=='INACTIVO')?'selected':''; ?>>INACTIVO</option>
            </select>
        </div>

        <button type="submit" name="guardar" class="btn btn-success">Guardar</button>
        <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
