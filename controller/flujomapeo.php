<?php
require_once("../config/conexion.php");
require_once("../models/FlujoMapeo.php"); // Asegúrate que el nombre del modelo sea el correcto
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$flujo_mapeo = new FlujoMapeo();

switch ($_GET["op"]) {

    case "listar":
        // CAMBIADO: Se usa la nueva función get_reglas_mapeo()
        $datos = $flujo_mapeo->get_reglas_mapeo();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["cats_nom"] ?? "N/A";
            // CAMBIADO: Se usan las nuevas columnas con los nombres concatenados
            $sub_array[] = $row["creadores"] ?? "Sin Asignar";
            $sub_array[] = $row["asignados"] ?? "Sin Asignar";
            $sub_array[] = '<button type="button" onClick="editar(' . $row['regla_id'] . ');" class="btn btn-inline btn-warning btn-sm"><i class="fa fa-edit"></i></button>';
            $sub_array[] = '<button type="button" onClick="eliminar(' . $row['regla_id'] . ');" class="btn btn-inline btn-danger btn-sm"><i class="fa fa-trash"></i></button>';
            $data[] = $sub_array;
        }
        $results = ["sEcho" => 1, "iTotalRecords" => count($data), "iTotalDisplayRecords" => count($data), "aaData" => $data];
        echo json_encode($results);
        break;

    case "guardaryeditar":
        // NUEVA LÓGICA: Ahora se manejan arrays para los cargos
        $regla_id = isset($_POST['regla_id']) && !empty($_POST['regla_id']) ? $_POST['regla_id'] : null;
        $cats_id = $_POST['cats_id'];
        // Se asegura de que los arrays existan, incluso si el formulario los envía vacíos
        $creador_car_ids = isset($_POST['creador_car_ids']) ? $_POST['creador_car_ids'] : [];
        $asignado_car_ids = isset($_POST['asignado_car_ids']) ? $_POST['asignado_car_ids'] : [];

        if (is_null($regla_id)) {
            // Llama a la nueva función de inserción con arrays
            $flujo_mapeo->insert_flujo_mapeo($cats_id, $creador_car_ids, $asignado_car_ids);
        } else {
            // Llama a la nueva función de actualización con arrays
            $flujo_mapeo->update_flujo_mapeo($regla_id, $cats_id, $creador_car_ids, $asignado_car_ids);
        }
        break;

    case "mostrar":
        // CAMBIADO: La nueva función del modelo devuelve un array estructurado
        $datos = $flujo_mapeo->get_regla_mapeo_por_id($_POST['regla_id']);
        if ($datos) {
            // Se envía el JSON completo, el JavaScript se encargará de leerlo
            echo json_encode($datos);
        }
        break;

    case "eliminar":
        // SIN CAMBIOS: La lógica de eliminación sigue siendo la misma
        $flujo_mapeo->delete_regla_mapeo($_POST["regla_id"]);
        break;
        
    case "combo_cargos":
        require_once("../models/Cargo.php");
        $cargo = new Cargo();
        $datos = $cargo->get_cargos(); // Asume que tienes esta función
        $html = "";
        if(is_array($datos) && count($datos)>0){
            foreach($datos as $row){
                if($row['est'] == 1) $html.= "<option value='".$row['car_id']."'>".$row['car_nom']."</option>";
            }
        }
        echo $html;
        break;
}
?>