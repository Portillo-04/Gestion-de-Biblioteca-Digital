<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../../models/Libro.php";

$database = new Database();
$db = $database->getConnection();
$libroObj = new Libro($db);


$termino = $_GET['buscar'] ?? '';

if($termino){
    $libros = $libroObj->buscarGeneral($termino);
} else {
    $libros = $libroObj->listarLibros();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Libros</title>
    <link href="../../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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
    <h3 class="text-center">Gestión de Libros</h3>
    <a href="libro_form.php" class="btn btn-primary mb-3">Nuevo Libro</a>


    <form method="GET" class="d-flex mb-3" style="max-width:300px; margin-left:auto;">
        <input type="text" name="buscar" class="form-control form-control-sm me-2" 
               placeholder="Buscar...">
        <button type="submit" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-search"></i>
        </button>
    </form>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Código</th>
                <th>Título</th>
                <th>Editorial</th>
                <th>Categoría</th>
                <th>Stock</th>
                <th>Prestados</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($libros)): ?>
                <?php foreach($libros as $libro): ?>
                <tr>
                    <td><?= $libro['id_libro']; ?></td>
                    <td><?= $libro['codigo_libro']; ?></td>
                    <td><?= $libro['titulo']; ?></td>
                    <td><?= $libro['nombre_editorial']; ?></td>
                    <td><?= $libro['nombre_categoria']; ?></td> 
                    <td><?= $libro['stock']; ?></td>
                    <td><?= $libro['prestados']; ?></td>
                    <td>
                        <?php if($libro['estado'] == 'DISPONIBLE'): ?>
                            <span class="badge bg-success">DISPONIBLE</span>
                        <?php elseif($libro['estado'] == 'PRESTADO'): ?>
                            <span class="badge bg-warning">PRESTADO</span>
                        <?php elseif($libro['estado'] == 'INACTIVO'): ?>
                            <span class="badge bg-secondary">INACTIVO</span>
                        <?php else: ?>
                            <span class="badge bg-danger">DESCONOCIDO</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="libro_form.php?id=<?= $libro['id_libro']; ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="../../controllers/LibroController.php?eliminar=<?= $libro['id_libro']; ?>" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('¿Inactivar este libro?');">Inactivar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="9" class="text-center">No hay libros registrados</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
