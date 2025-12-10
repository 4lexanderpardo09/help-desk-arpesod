<?php
require_once("../config/conexion.php");
require_once("../models/Perfil.php");
$perfil = new Perfil();

switch ($_GET["op"]) {

    case "guardaryeditar":
        if (empty($_POST["per_id"])) {
            $perfil->insert_perfil($_POST["per_nom"]);
        } else {
            $perfil->update_perfil($_POST["per_id"], $_POST["per_nom"]);
        }
        break;

    case "mostrar":
        $datos = $perfil->get_perfil_x_id($_POST["per_id"]);
        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output["per_id"] = $row["per_id"];
                $output["per_nom"] = $row["per_nom"];
            }
            echo json_encode($output);
        }
        break;

    case "eliminar":
        $perfil->delete_perfil($_POST["per_id"]);
        break;

    case "listar":
        $datos = $perfil->get_perfiles();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["per_nom"];
            $sub_array[] = '<button type="button" onClick="editar(' . $row["per_id"] . ');"  id="' . $row["per_id"] . '" class="btn btn-inline btn-warning btn-sm ladda-button"><i class="fa fa-edit"></i></button>';
            $sub_array[] = '<button type="button" onClick="eliminar(' . $row["per_id"] . ');"  id="' . $row["per_id"] . '" class="btn btn-inline btn-danger btn-sm ladda-button"><i class="fa fa-trash"></i></button>';
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

    case "combo":
        $datos = $perfil->get_perfiles();
        if (is_array($datos) and count($datos) > 0) {
            $html = "";
            foreach ($datos as $row) {
                $html .= "<option value='" . $row["per_id"] . "'>" . $row["per_nom"] . "</option>";
            }
            echo $html;
        }
        break;

    case "combo_select2":
        $datos = $perfil->get_perfiles();
        $data = array();
        if (is_array($datos) and count($datos) > 0) {
            foreach ($datos as $row) {
                // Filtrar por término de búsqueda si existe
                $text = $row['per_nom'];
                if (isset($_GET['q']) && !empty($_GET['q'])) {
                    if (stripos($text, $_GET['q']) === false) {
                        continue;
                    }
                }
                $data[] = array("id" => $row['per_id'], "text" => $row['per_nom']);
            }
        }
        echo json_encode($data);
        break;
}
