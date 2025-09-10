<?php
require_once('../config/conexion.php');
require_once('../models/Departamento.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$departamento = new Departamento();

switch ($_GET["op"]) {
    case "combo":
        
        $datos = $departamento->get_departamento();
        if(is_array($datos) and count($datos) > 0){
            $html = "";
            foreach($datos as $row){
                 $html.= "<option value='".$row["dp_id"]."'>".$row["dp_nom"]."</option>";
            }
            echo $html;
        }

        break;

     
        case "guardaryeditar":

            if(empty($_POST['dp_id'])){
                $departamento->insert_departamento($_POST['dp_nom']);
            }else{
                $departamento->update_departamento($_POST['dp_id'],$_POST['dp_nom']);
            }  
    
            break; 
    
        case "listar":
            $datos = $departamento->get_departamento();
            $data = array();
            foreach ($datos as $row) {
                $sub_array = array();
                $sub_array[] = $row["dp_nom"];
                $sub_array[] = '<button type="button" onClick="editar(' . $row['dp_id'] . ');" id="' . $row['dp_id'] . '" class="btn btn-inline btn-waring btn-sm ladda-button"><i class="fa fa-edit"></i></button>';
                $sub_array[] = '<button type="button" onClick="eliminar(' . $row['dp_id'] . ');" id="' . $row['dp_id'] . '" class="btn btn-inline btn-danger btn-sm ladda-button"><i class="fa fa-trash"></i></button>';
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
            $departamento->delete_departamento($_POST["dp_id"]);
    
            break;
    
        case "mostrar":
            $datos = $departamento->get_departamento_x_id($_POST['dp_id']);
                $output['dp_id'] = $datos['dp_id'];
                $output['dp_nom'] = $datos['dp_nom'];
            echo json_encode($output);  
        break;
}
?>