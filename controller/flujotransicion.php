<?php
require_once("../config/conexion.php");
require_once("../models/FlujoTransicion.php");
require_once("../models/FlujoPaso.php");

$flujoTransicion = new FlujoTransicion();
$flujoPaso = new FlujoPaso();

switch ($_GET["op"]) {

    case "guardaryeditar":
        // Asegurarse que el paso destino puede ser NULL
        $paso_destino_id = !empty($_POST["paso_destino_id"]) ? $_POST["paso_destino_id"] : null;

        if (empty($_POST["transicion_id"])) {
            $flujoTransicion->insert_transicion($_POST["paso_origen_id"], $paso_destino_id, $_POST["condicion_clave"], $_POST["condicion_nombre"]);
        } else {
            $flujoTransicion->update_transicion($_POST["transicion_id"], $_POST["paso_origen_id"], $paso_destino_id, $_POST["condicion_clave"], $_POST["condicion_nombre"]);
        }
        break;

    case "listar_por_paso":
        $datos = $flujoTransicion->get_transiciones_por_paso($_POST["paso_origen_id"]);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["paso_origen"];
            $sub_array[] = $row["paso_destino"] ? $row["paso_destino"] : 'Fin del Flujo';
            $sub_array[] = $row["condicion_clave"];
            $sub_array[] = $row["condicion_nombre"];
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