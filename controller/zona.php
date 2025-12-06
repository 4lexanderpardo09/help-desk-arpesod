<?php
require_once("../config/conexion.php");
require_once("../models/Zona.php");
$zona = new Zona();

switch ($_GET["op"]) {
    case "combo":
        $datos = $zona->get_zonas();
        if (is_array($datos) == true and count($datos) > 0) {
            $html = "<option label='Seleccione'></option>";
            foreach ($datos as $row) {
                $html .= "<option value='" . $row['zona_id'] . "'>" . $row['zona_nom'] . "</option>";
            }
            echo $html;
        }
        break;

    case "guardaryeditar":
        if (empty($_POST["zona_id"])) {
            $zona->insert_zona($_POST["zona_nom"]);
        } else {
            $zona->update_zona($_POST["zona_id"], $_POST["zona_nom"]);
        }
        break;

    case "listar":
        $datos = $zona->get_zonas();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["zona_nom"];
            $sub_array[] = '<button type="button" onClick="editar(' . $row['zona_id'] . ');" id="' . $row['zona_id'] . '" class="btn btn-inline btn-waring btn-sm ladda-button"><i class="fa fa-edit"></i></button>';
            $sub_array[] = '<button type="button" onClick="eliminar(' . $row['zona_id'] . ');" id="' . $row['zona_id'] . '" class="btn btn-inline btn-danger btn-sm ladda-button"><i class="fa fa-trash"></i></button>';
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
        $datos = $zona->get_zona_x_id($_POST["zona_id"]);
        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output["zona_id"] = $row["zona_id"];
                $output["zona_nom"] = $row["zona_nom"];
            }
            echo json_encode($output);
        }
        break;

    case "eliminar":
        $zona->delete_zona($_POST["zona_id"]);
        break;
}
