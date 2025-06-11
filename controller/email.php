<?php 
    require_once('../config/conexion.php');
    require_once('../models/Email.php');
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $correo = new Email(); 

    switch ($_GET["op"]) {
        case "ticket_abierto":
            $correo->ticket_abierto($_POST['tick_id']);
        break;

        case 'ticket_asignado':
           $correo->ticket_asignado($_POST["tick_id"]);
           break;
    }
    

?>