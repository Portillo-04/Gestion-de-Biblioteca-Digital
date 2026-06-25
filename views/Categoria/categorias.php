<?php
require_once __DIR__ . "/../../includes/auth.php";
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../../models/Categoria.php";

$database = new Database();
$db = $database->getConnection();
$categoriaObj = new Categoria($db);

// Filtro único (nombre_categoria o estado)
if(isset($_GET['criterio']) && $_GET['criterio'] != ""){
    $categorias = $categoriaObj->listarCategorias($_GET['criterio']);
} else {
    $categorias = $categoriaObj->listarCategorias();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Categorías</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <h3 class="text-center mb-4">Gestión de Categorías</h3>

  
    <div class="row mb-3">
        <div class="col-md-3">
            <a href="categoria_form.php" class="btn btn-primary">Nueva Categoría</a>
        </div>
        <div class="col-md-9 d-flex justify-content-end">
            <form method="GET" class="d-flex" style="max-width:300px; margin-left:auto;">
                <input type="text" name="criterio" 
                       class="form-control form-control-sm me-2" 
                       placeholder="Buscar...">
                <button type="submit" class="btn btn-outline-secondary btn-sm">🔍</button>
            </form>
        </div>
    </div>

    <?php if (empty($categorias)): ?>
        <div class="alert alert-warning text-center">No hay categorías registradas.</div>
    <?php else: ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($categorias as $cat): ?>
                <tr>
                    <td><?= $cat['id_categoria'] ?></td>
                    <td><?= $cat['nombre_categoria'] ?></td>
                    <td><?= $cat['descripcion'] ?></td>
                    <td>
                        <?php if (strtoupper($cat['estado']) === "ACTIVO"): ?>
                            <span class="badge bg-success">ACTIVO</span>
                        <?php else: ?>
                            <span class="badge bg-danger">INACTIVO</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="categoria_form.php?id=<?= $cat['id_categoria'] ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="../../controllers/CategoriaController.php?delete=<?= $cat['id_categoria'] ?>" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('¿Seguro que deseas eliminar esta categoría?');">
                           Eliminar
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
