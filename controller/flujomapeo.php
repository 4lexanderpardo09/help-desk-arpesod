<?php
require_once("../config/conexion.php");
require_once("../models/FlujoMapeo.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$flujo_mapeo = new FlujoMapeo();

switch ($_GET["op"]) {

    case "listar":
        $datos = $flujo_mapeo->get_flujo_mapeos();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["cats_nom"] ?? "N/A";
            $sub_array[] = $row["creador_cargo"] ?? "N/A";
            $sub_array[] = $row["asignado_cargo"] ?? "N/A";
            $sub_array[] = '<button type="button" onClick="editar(' . $row['map_id'] . ');" id="' . $row['map_id'] . '" class="btn btn-inline btn-waring btn-sm ladda-button"><i class="fa fa-edit"></i></button>';
            $sub_array[] = '<button type="button" onClick="eliminar(' . $row['map_id'] . ');" id="' . $row['map_id'] . '" class="btn btn-inline btn-danger btn-sm ladda-button"><i class="fa fa-trash"></i></button>';
            $data[] = $sub_array;
        }
        $results = ["sEcho" => 1, "iTotalRecords" => count($data), "iTotalDisplayRecords" => count($data), "aaData" => $data];
        echo json_encode($results);
        break;

    case "guardaryeditar":
        $map_id = isset($_POST["map_id"]) && !empty($_POST["map_id"]) ? $_POST["map_id"] : null;
        if (is_null($map_id)) {
            $flujo_mapeo->insert_flujo_mapeo($_POST["cats_id"], $_POST["creador_car_id"], $_POST["asignado_car_id"]);
        } else {
            $flujo_mapeo->update_flujo_mapeo($map_id, $_POST["cats_id"], $_POST["creador_car_id"], $_POST["asignado_car_id"]);
        }
        break;

    case "mostrar":
        $datos = $flujo_mapeo->get_flujo_mapeo_por_id($_POST["map_id"]);
        echo json_encode($datos);
        break;

    case "eliminar":
        $flujo_mapeo->delete_flujo_mapeo($_POST["map_id"]);
        break;
        
}
?>