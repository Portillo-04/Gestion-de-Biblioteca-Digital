<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Biblioteca Digital</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-image: url('../assets/bootstrap/img/login.png');
             background-size: cover; 
             background-position: center; 
             background-repeat: no-repeat;">

<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header text-center bg-primary text-white">
                <h4>Iniciar Sesión</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="../controllers/LoginController.php">
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<footer class="bg-white text-dark text-center py-1 fixed-bottom border-top">
    <p class="mb-0">Biblioteca Digital &copy; 2026</p>
</footer>

<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
