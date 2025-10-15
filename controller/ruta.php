<?php
require_once("../config/conexion.php");
require_once("../models/Ruta.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$ruta = new Ruta();

switch ($_GET["op"]) {
    case "guardaryeditar":
        if (empty($_POST["ruta_id"])) {
            // Al insertar, devuelve el ID del nuevo registro
            $lastId = $ruta->insert_ruta($_POST["flujo_id"], $_POST["ruta_nombre"]);
            echo $lastId;
        } else {
            $ruta->update_ruta($_POST["ruta_id"], $_POST["ruta_nombre"]);
            // Al editar, puedes devolver un 'true' o nada
            echo "true";
        }
        break;

    case "listar":
        $datos = $ruta->get_rutas_por_flujo($_POST["flujo_id"]);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["ruta_nombre"];
            $sub_array[] = '<button type="button" onClick="editar(' . $row["ruta_id"] . ');"  id="' . $row["ruta_id"] . '" class="btn btn-inline btn-warning btn-sm ladda-button"><i class="fa fa-edit"></i></button>';
            $sub_array[] = '<button type="button" onClick="eliminar(' . $row["ruta_id"] . ');"  id="' . $row["ruta_id"] . '" class="btn btn-inline btn-danger btn-sm ladda-button"><i class="fa fa-trash"></i></button>';
            $sub_array[] = '<button type="button" onClick="gestionarPasosRuta(' . $row["ruta_id"] . ');"  id="' . $row["ruta_id"] . '" class="btn btn-inline btn-primary btn-sm ladda-button">Gestionar Pasos</button>';
            $data[] = $sub_array;
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results);
        break;

    case "mostrar":
        $datos = $ruta->get_ruta_por_id($_POST["ruta_id"]);
        if (is_array($datos) == true and count($datos) > 0) {
            echo json_encode($datos);
        }
        break;

    case "eliminar":
        $ruta->delete_ruta($_POST["ruta_id"]);
        break;

    case "listar_para_select":
        $datos = $ruta->get_rutas_por_flujo($_POST["flujo_id"]);
        $html = "";
        foreach ($datos as $row) {
            $html .= "<option value='" . $row['ruta_id'] . "'>" . $row['ruta_nombre'] . "</option>";
        }
        echo $html;
        break;
}
?>