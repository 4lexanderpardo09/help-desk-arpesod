<?php
require_once('../config/conexion.php');
require_once('../models/RespuestaRapida.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$respuestarapida = new RespuestaRapida();

switch ($_GET["op"]) {

    case "combo":
        
        $datos = $respuestarapida->get_respuestarapida();
        if(is_array($datos) and count($datos) > 0){
            $html = "";
            foreach($datos as $row){
                 $html.= "<option value='".$row["answer_id"]."'>".$row["answer_nom"]."</option>";
            }
            echo $html;
        }

        break;

     
        case "guardaryeditar":

            if(empty($_POST['answer_id'])){
                $respuestarapida->insert_respuestarapida($_POST['answer_nom']);
            }else{
                $respuestarapida->update_respuestarapida($_POST['answer_id'],$_POST['answer_nom']);
            }  
    
            break; 
    
        case "listar":
            $datos = $respuestarapida->get_respuestarapida();
            $data = array();
            foreach ($datos as $row) {
                $sub_array = array();
                $sub_array[] = $row["answer_nom"];
                $sub_array[] = '<button type="button" onClick="editar(' . $row['answer_id'] . ');" id="' . $row['answer_id'] . '" class="btn btn-inline btn-waring btn-sm ladda-button"><i class="fa fa-edit"></i></button>';
                $sub_array[] = '<button type="button" onClick="eliminar(' . $row['answer_id'] . ');" id="' . $row['answer_id'] . '" class="btn btn-inline btn-danger btn-sm ladda-button"><i class="fa fa-trash"></i></button>';
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
            $respuestarapida->delete_respuestarapida($_POST["answer_id"]);
    
            break;
    
        case "mostrar":
            $datos = $respuestarapida->get_respuestarapida_x_id($_POST['answer_id']);
            if(is_array($datos) and count($datos) >0){
                foreach ($datos as $row) {
                    $output['answer_id'] = $row['answer_id'];
                    $output['answer_nom'] = $row['answer_nom'];
                }
                echo json_encode($output);
            }    
}
?>