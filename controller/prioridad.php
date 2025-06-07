<?php
require_once('../config/conexion.php');
require_once('../models/Prioridad.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$prioridad = new Prioridad();

switch ($_GET["op"]) {

    case "combo":
        $datos = $prioridad->get_prioridad();
        if(is_array($datos) and count($datos) > 0){
            $html = "";
            foreach($datos as $row){
                $html.= "<option value='".$row["pd_id"]."'>".$row["pd_nom"]."</option>";
            }
            echo $html;
        }

        break;
    
    case "guardaryeditar":

        if(empty($_POST['pd_id'])){
            $prioridad->insert_prioridad($_POST['pd_nom']);
        }else{
            $prioridad->update_prioridad($_POST['pd_id'],$_POST['pd_nom']);
        }  

        break; 

    case "listar":
        $datos = $prioridad->get_prioridad();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["pd_nom"];
            $sub_array[] = '<button type="button" onClick="editar(' . $row['pd_id'] . ');" id="' . $row['pd_id'] . '" class="btn btn-inline btn-waring btn-sm ladda-button"><i class="fa fa-edit"></i></button>';
            $sub_array[] = '<button type="button" onClick="eliminar(' . $row['pd_id'] . ');" id="' . $row['pd_id'] . '" class="btn btn-inline btn-danger btn-sm ladda-button"><i class="fa fa-trash"></i></button>';
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
        $prioridad->delete_prioridad($_POST["pd_id"]);

        break;

    case "mostrar":
        $datos = $prioridad->get_prioridad_x_id($_POST['pd_id']);
        if(is_array($datos) and count($datos) >0){
            foreach ($datos as $row) {
                $output['pd_id'] = $row['pd_id'];
                $output['pd_nom'] = $row['pd_nom'];
            }
            echo json_encode($output);
        }
}
?>