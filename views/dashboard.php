<?php
session_start();
if(!isset($_SESSION['usuario']) || !isset($_SESSION['id_usuario'])){
    header("Location: ../views/login.php");
    exit();
}
require_once "../includes/auth.php";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Biblioteca Digital</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: url('../assets/bootstrap/img/dashboard.png') no-repeat center center fixed;
             background-size: cover;">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Biblioteca Digital</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <span class="nav-link">Usuario: <?php echo $_SESSION['usuario']; ?></span>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../logout.php">Cerrar Sesión</a>
        </li>
      </ul>
    </div>
  </div>
</nav>


<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="row justify-content-center w-100">

        <?php if($_SESSION['rol'] == 1): ?>
        <div class="col-md-4 mb-3">
            <div class="card shadow" style="background-color: rgba(255,255,255,0.9);">
                <div class="card-body text-center">
                    <h5 class="card-title">Gestión de Usuarios</h5>
                    <a href="./Usuario/usuarios.php" class="btn btn-primary">Entrar</a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="col-md-4 mb-3">
            <div class="card shadow" style="background-color: rgba(255,255,255,0.9);">
                <div class="card-body text-center">
                    <h5 class="card-title">Gestión de Libros</h5>
                    <a href="./Libro/libros.php" class="btn btn-primary">Entrar</a>
                </div>
            </div>
        </div>


        <div class="col-md-4 mb-3">
            <div class="card shadow" style="background-color: rgba(255,255,255,0.9);">
                <div class="card-body text-center">
                    <h5 class="card-title">Gestión de Autores</h5>
                    <a href="./Autor/autores.php" class="btn btn-primary">Entrar</a>
                </div>
            </div>
        </div>


        <div class="col-md-4 mb-3">
            <div class="card shadow" style="background-color: rgba(255,255,255,0.9);">
                <div class="card-body text-center">
                    <h5 class="card-title">Gestión de Categorías</h5>
                    <a href="./Categoria/categorias.php" class="btn btn-primary">Entrar</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow" style="background-color: rgba(255,255,255,0.9);">
                <div class="card-body text-center">
                    <h5 class="card-title">Gestión de Estudiantes</h5>
                    <a href="./Estudiante/estudiantes.php" class="btn btn-primary">Entrar</a>
                </div>
            </div>
        </div>

         <div class="col-md-4 mb-3">
            <div class="card shadow" style="background-color: rgba(255,255,255,0.9);">
                <div class="card-body text-center">
                    <h5 class="card-title">Gestion de Editoriales</h5>
                    <a href="./Editorial/editoriales.php" class="btn btn-primary">Entrar</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow" style="background-color: rgba(255,255,255,0.9);">
                <div class="card-body text-center">
                    <h5 class="card-title">Préstamos y Devoluciones</h5>
                    <a href="./Prestamo/prestamos.php" class="btn btn-info">Entrar</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow" style="background-color: rgba(255,255,255,0.9);">
                <div class="card-body text-center">
                    <h5 class="card-title">Reportes</h5>
                    <a href="./Reporte/reportes.php" class="btn btn-dark">Entrar</a>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="bg-white text-dark text-center py-1 fixed-bottom border-top">
    <p class="mb-0">Biblioteca Digital &copy; 2026</p>
</footer>

<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
setInterval(() => {
    fetch('../includes/auth.php?check=estado')
        .then(res => res.json())
        .then(data => {
            if (data.estado !== 'ACTIVO') {
                window.location.href = '../views/login.php';
            }
        });
}, 10000);
</script>

</body>
</html>
