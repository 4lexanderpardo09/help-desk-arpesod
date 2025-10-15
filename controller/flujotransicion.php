<?php
require_once("../config/conexion.php");
require_once("../models/FlujoTransicion.php");
require_once("../models/FlujoPaso.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$flujoTransicion = new FlujoTransicion();
$flujoPaso = new FlujoPaso();

switch ($_GET["op"]) {

    case "guardaryeditar":
        // Usamos los nombres de campo del formulario del modal (ej: 'paso_origen_id_modal')
        $paso_origen = $_POST["paso_origen_id_modal"];
        $ruta_id = $_POST["ruta_id_modal"];
        $condicion_clave = $_POST["condicion_clave_modal"];
        $condicion_nombre = $_POST["condicion_nombre_modal"];
        
        if (empty($_POST["transicion_id"])) {
            $flujoTransicion->insert_transicion($paso_origen, $ruta_id, $condicion_clave, $condicion_nombre);
        } else {
            $flujoTransicion->update_transicion($_POST["transicion_id"], $paso_origen, $ruta_id, $condicion_clave, $condicion_nombre);
        }
        break;

    case "listar_por_paso":
        $datos = $flujoTransicion->get_transiciones_por_paso($_POST["paso_origen_id"]);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["ruta_nombre"] ? $row["ruta_nombre"] : 'Ruta no definida';
            $sub_array[] = $row["condicion_nombre"];
            $sub_array[] = $row["condicion_clave"];
            $sub_array[] = '<button type="button" onClick="editarTransicion(' . $row["transicion_id"] . ');" class="btn btn-inline btn-warning btn-sm"><i class="fa fa-edit"></i></button>';
            $sub_array[] = '<button type="button" onClick="eliminarTransicion(' . $row["transicion_id"] . ',' . $row["paso_origen_id"] . ',\'' . rawurlencode($row["paso_origen"]) . '\');" class="btn btn-inline btn-danger btn-sm"><i class="fa fa-trash"></i></button>';
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
        $datos = $flujoTransicion->get_transicion_por_id($_POST["transicion_id"]);
        if (is_array($datos) == true and count($datos) > 0) {
            echo json_encode($datos);
        }
        break;

    case "eliminar":
        $flujoTransicion->delete_transicion($_POST["transicion_id"]);
        break;

    case "combo_pasos":
        $datos = $flujoPaso->get_pasos_por_flujo($_POST["flujo_id"]);
        if (is_array($datos) == true and count($datos) > 0) {
            $html = '';
            foreach ($datos as $row) {
                $html .= '<option value="' . $row['paso_id'] . '">' . $row['paso_nombre'] . '</option>';
            }
            echo $html;
        }
        break;
}
?>