<?php
session_start();
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Usuario.php";

$database = new Database();
$db = $database->getConnection();
$usuarioObj = new Usuario($db);

// Guardar o actualizar usuario
if (isset($_POST['guardar'])) {
    $data = [
        'id_usuario' => $_POST['id_usuario'] ?? null,
        'usuario'    => $_POST['usuario'],
        'correo'     => $_POST['correo'],
        'estado'     => $_POST['estado'],
        'id_rol'     => $_POST['id_rol']
    ];

    // Manejo de contraseña
    if (!empty($_POST['password'])) {
        // Nueva contraseña → encriptar
        $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    } else {
        // Cambio de contraseña si no mantiene la anterior
        if (!empty($data['id_usuario'])) {
            $usuarioExistente = $usuarioObj->buscarUsuario($data['id_usuario']);
            $data['password'] = $usuarioExistente['password'];
        }
    }

    $resultado = $usuarioObj->guardarUsuario($data);

    if ($resultado === true) {
        header("Location: ../views/Usuario/usuarios.php");
        exit();
    } else {
        echo "<script>alert('$resultado'); window.location.href='../views/Usuario/usuario_form.php';</script>";
        exit();
    }
}

// Inactivar
if (isset($_GET['delete'])) {
    $usuario = $usuarioObj->buscarUsuario($_GET['delete']);

    if ($usuario) {
        // Bloquear eliminación del admin principal
        if ($usuario['id_usuario'] == 1 && $usuario['id_rol'] == 1) {
            echo "<script>alert('No puedes eliminar al Admin'); window.location.href='../views/Usuario/usuarios.php';</script>";
            exit();
        }

        // Permitir eliminar a otros Administradores y Bibliotecarios
        $usuarioObj->eliminarUsuario($_GET['delete']);
    }

    header("Location: ../views/Usuario/usuarios.php");
    exit();
}
?>
