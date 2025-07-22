<?php
require_once('../config/conexion.php');
require_once('../models/Flujo.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$flujo = new Flujo();

switch ($_GET["op"]) {
    case "combo":
        
        $datos = $flujo->get_flujo();
        if(is_array($datos) and count($datos) > 0){
            $html = "";
            foreach($datos as $row){
                $html.= "<option value='".$row["flujo_id"]."'>".$row["flujo_nom"]."</option>";
            }
            echo $html;
        }

        break;

    case "comboxusu":
        
        $datos = $flujo->get_flujo_x_usu($_POST['usu_id']);
        if(is_array($datos) and count($datos) > 0){
            $html = "";
            foreach($datos as $row){
                $html.= "<option value='".$row["flujo_id"]."'>".$row["flujo_nom"]."</option>";
            }
            echo $html;
        }

        break;    
        
        case "guardaryeditar":
        $flujo_id = isset($_POST["flujo_id"]) ? $_POST["flujo_id"] : null;
        $flujo_nom = $_POST["flujo_nom"];
        $cats_id = $_POST["cats_id"];
        // Un checkbox no se envía si no está marcado. Así verificamos su valor.
        $req_aprob_jefe = isset($_POST["requiere_aprobacion_jefe"]) ? 1 : 0; 

        if (empty($flujo_id)) {
            $flujo->insert_flujo($flujo_nom, $cats_id, $req_aprob_jefe);
        } else {
            $flujo->update_flujo($flujo_id, $flujo_nom, $cats_id, $req_aprob_jefe);
        }
        break;
    
        case "listar":
            $datos = $flujo->get_flujotodo();
            $data = array();
            foreach ($datos as $row) {
                $sub_array = array();
                $sub_array[] = $row["flujo_nom"];
                $sub_array[] = $row["cats_nom"];
                $sub_array[] = $row["requiere_aprobacion_jefe"] ? '<span class="label label-info">Si</span>' : '<span class="label label-default">No</span>';
                $sub_array[] = '<button type="button" onClick="editar(' . $row['flujo_id'] . ');" id="' . $row['flujo_id'] . '" class="btn btn-inline btn-waring btn-sm ladda-button"><i class="fa fa-edit"></i></button>';
                $sub_array[] = '<button type="button" onClick="eliminar(' . $row['flujo_id'] . ');" id="' . $row['flujo_id'] . '" class="btn btn-inline btn-danger btn-sm ladda-button"><i class="fa fa-trash"></i></button>';
                $sub_array[] = '<button type="button" onClick="ver(' . $row['flujo_id'] . ');" class="btn btn-inline btn-primary btn-sm ladda-button" title="Ver pasos del flujo"><i class="fa fa-eye"></i></button>';
                $data[] = $sub_array;
            }
            $result = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            echo json_encode($result);
            break;    
    
        case "eliminar":
            $flujo->delete_flujo($_POST["flujo_id"]);
    
            break;
    
        case "mostrar":
            $datos = $flujo->get_flujo_x_id($_POST['flujo_id']);
            if(is_array($datos) and count($datos) >0){
                foreach ($datos as $row) {
                    $output['flujo_id'] = $row['flujo_id'];
                    $output['flujo_nom'] = $row['flujo_nom'];
                    $output['cats_id'] = $row['cats_id'];
                    $output['cat_id'] = $row['cat_id'];
                    $output['emp_id'] = $row['emp_id'];
                    $output['dp_id'] = $row['dp_id'];
                    $output['requiere_aprobacion_jefe'] = $row['requiere_aprobacion_jefe'];
                }
                echo json_encode($output);
            }       
}
?>