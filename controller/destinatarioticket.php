<?php
require_once('../config/conexion.php');
require_once('../models/DestinatarioTicket.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$destinatarioticket = new DestinatarioTicket();

switch ($_GET["op"]) {

    case "combo":
        
        $datos = $destinatarioticket->get_destinatarioticket($_POST['answer_id'],$_POST['dp_id'],$_POST['cats_id']);
        if(is_array($datos) and count($datos) > 0){
            $html = "";
            foreach($datos as $row){
                 $html.= "<option value='".$row["dest_id"]."'>".$row["nombre_usuario"]."</option>";
            }
            echo $html;
        }

        break;
     
        case "guardaryeditar":

            if(empty($_POST['dest_id'])){
                $destinatarioticket->insert_destinatarioticket($_POST['answer_id'],$_POST['usu_id'],$_POST['dp_id'],$_POST['cats_id']);
            }else{
                $destinatarioticket->update_destinatarioticket($_POST['dest_id'],$_POST['answer_id'],$_POST['usu_id'],$_POST['dp_id'],$_POST['cats_id']);
            }  
    
            break; 
    
        case "listar":
            $datos = $destinatarioticket->get_destinatariotickettodo();
            $data = array();
            foreach ($datos as $row) {
                $sub_array = array();
                $sub_array[] = $row["nombre_usuario"];
                $sub_array[] = $row["dp_nom"];
                $sub_array[] = $row["cats_nom"];
                $sub_array[] = $row["answer_nom"];
                $sub_array[] = '<button type="button" onClick="editar(' . $row['dest_id'] . ');" id="' . $row['dest_id'] . '" class="btn btn-inline btn-waring btn-sm ladda-button"><i class="fa fa-edit"></i></button>';
                $sub_array[] = '<button type="button" onClick="eliminar(' . $row['dest_id'] . ');" id="' . $row['dest_id'] . '" class="btn btn-inline btn-danger btn-sm ladda-button"><i class="fa fa-trash"></i></button>';
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
            $destinatarioticket->delete_destinatarioticket($_POST["dest_id"]);
    
            break;
    
        case "mostrar":
            $datos = $destinatarioticket->get_destinatarioticket_x_id($_POST['dest_id']);
            if(is_array($datos) and count($datos) >0){
                foreach ($datos as $row) {
                    $output['dest_id'] = $row['dest_id'];
                    $output['answer_id'] = $row['answer_id'];
                    $output['usu_id'] = $row['usu_id'];
                    $output['dp_id'] = $row['dp_id'];
                    $output['cat_id'] = $row['cat_id'];
                    $output['cats_id'] = $row['cats_id'];
                    $output['nombre_usuario'] = $row['nombre_usuario'];
                }
                echo json_encode($output);
            }    
}
?>