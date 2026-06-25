<?php
session_start();


$_SESSION = array();

// Destruir la sesión
session_destroy();

// Redirigir al login
header("Location: views/login.php");
exit();
?>
