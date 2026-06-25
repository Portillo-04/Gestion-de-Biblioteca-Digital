<?php
session_start();
require_once "../config/db.php";

$database = new Database();
$db = $database->getConnection();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);

    // Buscar usuario en la BD
    $query = "SELECT id_usuario, usuario, password, id_rol 
              FROM usuarios 
              WHERE usuario=:usuario AND estado='ACTIVO' LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":usuario", $usuario, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);


   if($user && password_verify($password, $user['password'])){
    $_SESSION['id_usuario'] = (int)$user['id_usuario'];
    $_SESSION['usuario'] = $user['usuario'];
    $_SESSION['rol'] = (int)$user['id_rol'];

    header("Location: ../views/dashboard.php");
    exit();
} else {
    header("Location: ../views/login.php?error=1");
    exit();

}   
}
?>