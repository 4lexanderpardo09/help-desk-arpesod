<?php
require_once("../config/conexion.php");
require_once("../models/Regional.php");
$regional = new Regional();

switch ($_GET["op"]) {

    case "guardaryeditar":
        if (empty($_POST["reg_id"])) {
            $regional->insert_regional($_POST["reg_nom"]);
        } else {
            $regional->update_regional($_POST["reg_id"], $_POST["reg_nom"]);
        }
        break;

    case "listar":
        $datos = $regional->get_regionales();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["reg_nom"];
            $sub_array[] = '<button type="button" onClick="editar(' . $row['reg_id'] . ');" id="' . $row['reg_id'] . '" class="btn btn-inline btn-waring btn-sm ladda-button"><i class="fa fa-edit"></i></button>';
            $sub_array[] = '<button type="button" onClick="eliminar(' . $row['reg_id'] . ');" id="' . $row['reg_id'] . '" class="btn btn-inline btn-danger btn-sm ladda-button"><i class="fa fa-trash"></i></button>';
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
        $datos = $regional->get_regional_x_id($_POST["reg_id"]);
        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output["reg_id"] = $row["reg_id"];
                $output["reg_nom"] = $row["reg_nom"];
            }
            echo json_encode($output);
        }
        break;

    case "eliminar":
        $regional->delete_regional($_POST["reg_id"]);
        break;
}
?>