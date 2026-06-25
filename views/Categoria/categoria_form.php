<?php
require_once __DIR__ . "/../../includes/auth.php";
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../../models/Categoria.php";

$database = new Database();
$db = $database->getConnection();
$categoriaObj = new Categoria($db);

$categoria = null;
if (isset($_GET['id'])) {
    $categoria = $categoriaObj->buscarCategoria($_GET['id']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $categoria ? "Editar Categoría" : "Nueva Categoría" ?></title>
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
    <h2 class="text-center mb-4"><?= $categoria ? "Editar Categoría" : "Nueva Categoría" ?></h2>
    <form action="../../controllers/CategoriaController.php" method="POST" class="border p-4 rounded shadow-sm bg-light">
        <input type="hidden" name="id_categoria" value="<?= $categoria['id_categoria'] ?? '' ?>">
        
    
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre de la Categoría</label>
            <input type="text" class="form-control" id="nombre" name="nombre_categoria" 
                   value="<?= $categoria['nombre_categoria'] ?? '' ?>" required>
        </div>
        
   
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?= $categoria['descripcion'] ?? '' ?></textarea>
        </div>

   
        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <select class="form-select" id="estado" name="estado" required>
                <option value="ACTIVO" <?= strtoupper($categoria['estado'] ?? '') === 'ACTIVO' ? 'selected' : '' ?>>ACTIVO</option>
                <option value="INACTIVO" <?= strtoupper($categoria['estado'] ?? '') === 'INACTIVO' ? 'selected' : '' ?>>INACTIVO        </option>
            </select>
        </div>
        
     
        <button type="submit" name="guardar" class="btn btn-success">Guardar</button>
        <a href="categorias.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
