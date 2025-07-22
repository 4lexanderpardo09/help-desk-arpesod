<?php
require_once('../config/conexion.php');
require_once('../models/FlujoPaso.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$flujopaso = new FlujoPaso();

switch ($_GET["op"]) {
    case "combo":
        
        $datos = $flujopaso->get_flujopaso();
        if(is_array($datos) and count($datos) > 0){
            $html = "";
            foreach($datos as $row){
                $html.= "<option value='".$row["paso_id"]."'>".$row["flujopaso_nom"]."</option>";
            }
            echo $html;
        }

        break; 
        
        case "guardaryeditar":

            if(empty($_POST['paso_id'])){
                $flujopaso->insert_paso($_POST['flujo_id'],$_POST['paso_orden'],$_POST['paso_nombre'],$_POST['cargo_id_asignado']);
            }else{
                $flujopaso->update_paso($_POST['paso_id'],$_POST['flujo_id'],$_POST['paso_orden'],$_POST['paso_nombre'],$_POST['cargo_id_asignado']);
            }  
    
            break; 
    
        case "listar":
            $datos = $flujopaso->get_pasos_por_flujo($_POST['flujo_id']);
            $data = array();
            foreach ($datos as $row) {
                $sub_array = array();
                $sub_array[] = $row["paso_orden"];
                $sub_array[] = $row["paso_nombre"];
                $sub_array[] = $row["car_nom"];
                $sub_array[] = '<button type="button" onClick="editar(' . $row['paso_id'] . ');" id="' . $row['paso_id'] . '" class="btn btn-inline btn-waring btn-sm ladda-button"><i class="fa fa-edit"></i></button>';
                $sub_array[] = '<button type="button" onClick="eliminar(' . $row['paso_id'] . ');" id="' . $row['paso_id'] . '" class="btn btn-inline btn-danger btn-sm ladda-button"><i class="fa fa-trash"></i></button>';
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
            $flujopaso->delete_paso($_POST["paso_id"]);
    
            break;
    
        case "mostrar":
            $datos = $flujopaso->get_paso_x_id($_POST['paso_id']);
            if(is_array($datos) and count($datos) >0){
                foreach ($datos as $row) {
                    $output['paso_id'] = $row['paso_id'];
                    $output['paso_nombre'] = $row['paso_nombre'];
                    $output['paso_orden'] = $row['paso_orden'];
                    $output['usu_id'] = $row['usu_id'];
                }
                echo json_encode($output);
            }       
}
?>