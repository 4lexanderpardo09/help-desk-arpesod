<?php
require_once("../config/conexion.php");
require_once("../models/ReglaAprobacion.php");
$regla_aprobacion = new ReglaAprobacion();

switch ($_GET["op"]) {

    case "listar":
        $datos = $regla_aprobacion->get_reglas_aprobacion();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["creador_cargo"] ?? "N/A";
            $sub_array[] = $row["aprobador_nombre"] ?? "N/A";
            $sub_array[] = '<button type="button" onClick="editar(' . $row['regla_id'] . ');" id="' . $row['regla_id'] . '" class="btn btn-inline btn-waring btn-sm ladda-button"><i class="fa fa-edit"></i></button>';
            $sub_array[] = '<button type="button" onClick="eliminar(' . $row['regla_id'] . ');" id="' . $row['regla_id'] . '" class="btn btn-inline btn-danger btn-sm ladda-button"><i class="fa fa-trash"></i></button>';
            $data[] = $sub_array;
        }
        $results = ["sEcho" => 1, "iTotalRecords" => count($data), "iTotalDisplayRecords" => count($data), "aaData" => $data];
        echo json_encode($results);
        break;

    case "guardaryeditar":
        $regla_id = isset($_POST["regla_id"]) && !empty($_POST["regla_id"]) ? $_POST["regla_id"] : null;
        if (is_null($regla_id)) {
            $regla_aprobacion->insert_regla_aprobacion($_POST["creador_car_id"], $_POST["aprobador_usu_id"]);
        } else {
            $regla_aprobacion->update_regla_aprobacion($regla_id, $_POST["creador_car_id"], $_POST["aprobador_usu_id"]);
        }
        break;

    case "mostrar":
        $datos = $regla_aprobacion->get_regla_aprobacion_por_id($_POST["regla_id"]);
        echo json_encode($datos);
        break;

    case "eliminar":
        $regla_aprobacion->delete_regla_aprobacion($_POST["regla_id"]);
        break;
}
?>