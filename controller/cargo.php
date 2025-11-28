<?php
require_once("../config/conexion.php");
require_once("../models/Cargo.php");
$cargo = new Cargo();

switch ($_GET["op"]) {

    case "combo":

        $datos = $cargo->get_cargos();
        if (is_array($datos) and count($datos) > 0) {
            $html = "";
            foreach ($datos as $row) {
                $html .= "<option value='" . $row["car_id"] . "'>" . $row["car_nom"] . "</option>";
            }
            echo $html;
        }

        break;

    case "combo_select2":
        $datos = $cargo->get_cargos();
        $data = array();

        // Agregar opción especial "Jefe Inmediato"
        $term = isset($_GET['q']) ? $_GET['q'] : '';
        if (empty($term) || stripos('Jefe Inmediato', $term) !== false) {
            $data[] = array("id" => "JEFE_INMEDIATO", "text" => "Jefe Inmediato");
        }

        if (is_array($datos) and count($datos) > 0) {
            foreach ($datos as $row) {
                // Filtrar por término de búsqueda si existe
                if (!empty($term)) {
                    if (stripos($row['car_nom'], $term) === false) {
                        continue;
                    }
                }
                $data[] = array("id" => $row['car_id'], "text" => $row['car_nom']);
            }
        }
        echo json_encode($data);
        break;

    case "listar":
        $datos = $cargo->get_cargos();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["car_nom"];

            $sub_array[] = '<button type="button" onClick="editar(' . $row['car_id'] . ');" id="' . $row['car_id'] . '" class="btn btn-inline btn-waring btn-sm ladda-button"><i class="fa fa-edit"></i></button>';
            $sub_array[] = '<button type="button" onClick="eliminar(' . $row['car_id'] . ');" id="' . $row['car_id'] . '" class="btn btn-inline btn-danger btn-sm ladda-button"><i class="fa fa-trash"></i></button>';
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

    case "guardaryeditar":
        $car_id = isset($_POST["car_id"]) ? $_POST["car_id"] : null;
        $car_nom = $_POST["car_nom"];

        if (empty($car_id)) {
            $cargo->insert_cargo($car_nom);
        } else {
            $cargo->update_cargo($car_id, $car_nom);
        }
        break;

    case "mostrar":
        $datos = $cargo->get_cargo_por_id($_POST["car_id"]);
        if (isset($datos[0])) {
            echo json_encode($datos[0]);
        }
        break;

    case "eliminar":
        $cargo->delete_cargo($_POST["car_id"]);
        break;
}
