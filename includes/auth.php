<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Usuario.php";

$database = new Database();
$db = $database->getConnection();
$usuarioObj = new Usuario($db);

// Login
if (basename($_SERVER['PHP_SELF']) === "login.php" && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    $user = $usuarioObj->buscarPorUsuario($usuario);

    if ($user && $user['estado'] === 'ACTIVO') {
        if (password_verify($password, $user['password'])) {
            $_SESSION['usuario']    = $user['usuario'];
            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['id_rol']     = $user['id_rol'];
            $_SESSION['ultimo_login'] = (new DateTime())->format('d-m-Y H:i:s');

            header("Location: ../views/dashboard.php");
            exit();
        } else {
            $error = "Usuario o contraseña incorrectos.";
        }
    } else {
        $error = "El usuario no existe o está inactivo.";
    }
}

// Verificación de sesión
if (!isset($_SESSION['usuario']) || !isset($_SESSION['id_usuario'])) {
    header("Location: ../views/login.php");
    exit();
}

$usuarioActual = $usuarioObj->buscarUsuario($_SESSION['id_usuario']);

if (!$usuarioActual) {
    session_destroy();
    header("Location: ../views/login.php");
    exit();
}

// Expulsar SIEMPRE si está inactivo (incluye Admin)
if ($usuarioActual['estado'] !== 'ACTIVO') {
    session_destroy();
    header("Location: ../views/login.php");
    exit();
}

// Chequeo automático vía AJAX
if (isset($_GET['check']) && $_GET['check'] === 'estado') {
    $estado = 'INACTIVO';
    if ($usuarioActual) {
        $estado = $usuarioActual['estado'];
    }
    echo json_encode(['estado' => $estado]);
    exit();
}