<?php
require_once("../config/conexion.php");
require_once("../models/RutaPaso.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 
$rutapaso = new RutaPaso();

switch ($_GET["op"]) {
    case "guardar":
        // Aquí necesitarías lógica para no repetir el orden o el paso
        $rutapaso->insert_paso_en_ruta($_POST["ruta_id"], $_POST["paso_id"], $_POST["orden"]);
        break;

    case "listar":
        $datos = $rutapaso->get_pasos_por_ruta($_POST["ruta_id"]);
        // Devuelve los datos para que JS construya la lista de pasos
        echo json_encode($datos);
        break;

    case "eliminar":
        $rutapaso->delete_paso_de_ruta($_POST["ruta_paso_id"]);
        break;
        
    case "reordenar":
        // El frontend enviará un array de objetos: [{id: 1, orden: 1}, {id: 2, orden: 2}]
        $orden_json = $_POST['orden_data'];
        $orden_data = json_decode($orden_json, true);
        $rutapaso->update_orden_pasos($orden_data);
        echo json_encode(["status" => "success"]);
        break;
}
?>