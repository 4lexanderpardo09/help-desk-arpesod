<?php
require_once("../config/conexion.php");
session_start();

$conectar = new Conectar();

if (isset($_SESSION["usu_id"])) {
    // Si hay sesión → redirige a Home
    header("Location: " . $conectar->ruta() . "view/Home/");
} else {
    // Si no hay sesión → redirige a Login
    header("Location: " . $conectar->ruta() . "index.php");
}
exit();
?>