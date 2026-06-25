<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../../models/Libro.php";
require_once __DIR__ . "/../../models/Autor.php";
require_once __DIR__ . "/../../models/Editorial.php";
require_once __DIR__ . "/../../models/Categoria.php";

$database = new Database();
$db = $database->getConnection();

$libroObj     = new Libro($db);
$autorObj     = new Autor($db);
$editorialObj = new Editorial($db);
$categoriaObj = new Categoria($db);

// Listar datos
$autores     = $autorObj->listarAutores();
$editoriales = $editorialObj->listarEditoriales();
$categorias  = $categoriaObj->listarCategorias();

$libro = null;
$libroAutores = [];

if(isset($_GET['id'])){
    $libro = $libroObj->buscarLibro($_GET['id']);
    $libroAutores = $libroObj->obtenerAutoresLibro($_GET['id']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $libro ? 'Editar Libro' : 'Nuevo Libro'; ?></title>
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
    <h3><?= $libro ? 'Editar Libro' : 'Nuevo Libro'; ?></h3>

    <?php if(isset($_GET['error']) && $_GET['error'] == 'codigo'): ?>
        <div class="alert alert-danger">El código ya existe, ingrese uno diferente.</div>
    <?php endif; ?>

    <form action="../../controllers/LibroController.php" method="POST">
        <?php if($libro): ?>
            <input type="hidden" name="id_libro" value="<?= $libro['id_libro']; ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label>Código</label>
            <input type="text" name="codigo_libro" class="form-control" 
                   value="<?= $libro['codigo_libro'] ?? ''; ?>" required>
        </div>

        <div class="mb-3">
            <label>Título</label>
            <input type="text" name="titulo" class="form-control" 
                   value="<?= $libro['titulo'] ?? ''; ?>" required>
        </div>

        <div class="mb-3">
            <label>Editorial</label>
            <select name="editorial" class="form-select" required>
                <option value="">Seleccione...</option>
                <?php foreach($editoriales as $ed): ?>
                    <option value="<?= $ed['id_editorial']; ?>"
                        <?= ($libro && $libro['id_editorial'] == $ed['id_editorial']) ? 'selected' : ''; ?>>
                        <?= $ed['nombre_editorial']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Categoría</label>
            <select name="categoria" class="form-select" required>
                <option value="">Seleccione...</option>
                <?php foreach($categorias as $cat): ?>
                    <option value="<?= $cat['id_categoria']; ?>"
                        <?= ($libro && $libro['id_categoria'] == $cat['id_categoria']) ? 'selected' : ''; ?>>
                        <?= $cat['nombre_categoria']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Stock</label>
            <input type="number" name="stock" class="form-control" min="1"
                   value="<?= $libro['stock'] ?? 1; ?>" required>
        </div>

  
        <div class="mb-3">
            <label>Autor 1</label>
            <select name="autor1" class="form-select" required>
                <option value="">Seleccione...</option>
                <?php foreach($autores as $a): ?>
                    <?php 
                        $selected = false;
                        if(!empty($libroAutores)){
                            if($libroAutores[0]['id_autor'] == $a['id_autor']){
                                $selected = true;
                            }
                        }
                    ?>
                    <option value="<?= $a['id_autor']; ?>" <?= $selected ? 'selected' : ''; ?>>
                        <?= $a['nombre_autor']." ".$a['apellido']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>


        <div class="mb-3">
            <label>Autor 2 </label>
            <select name="autor2" class="form-select">
                <option value="">Seleccione...</option>
                <?php foreach($autores as $a): ?>
                    <?php 
                        $selected = false;
                        if(!empty($libroAutores) && isset($libroAutores[1])){
                            if($libroAutores[1]['id_autor'] == $a['id_autor']){
                                $selected = true;
                            }
                        }
                    ?>
                    <option value="<?= $a['id_autor']; ?>" <?= $selected ? 'selected' : ''; ?>>
                        <?= $a['nombre_autor']." ".$a['apellido']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>


        <div class="mb-3">
            <label>Estado</label>
            <select name="estado" class="form-select" required>
                <option value="DISPONIBLE" <?= ($libro && $libro['estado'] == 'DISPONIBLE') ? 'selected' : ''; ?>>Disponible</option>
                <option value="PRESTADO" <?= ($libro && $libro['estado'] == 'PRESTADO') ? 'selected' : ''; ?>>Prestado</option>
                <option value="INACTIVO" <?= ($libro && $libro['estado'] == 'INACTIVO') ? 'selected' : ''; ?>>Inactivo</option>
            </select>
        </div>

        <button type="submit" name="<?= $libro ? 'editar' : 'guardar'; ?>" class="btn btn-success">
            <?= $libro ? 'Actualizar' : 'Guardar'; ?>
        </button>
        <a href="libros.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
<script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
