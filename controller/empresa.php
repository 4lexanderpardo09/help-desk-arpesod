<?php
require_once('../config/conexion.php');
require_once('../models/Empresa.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$empresa = new Empresa();

switch ($_GET["op"]) {
    case "combo":
        
        $datos = $empresa->get_empresa();
        if(is_array($datos) and count($datos) > 0){
            $html = "";
            foreach($datos as $row){
                $html.= "<option value='".$row["emp_id"]."'>".$row["emp_nom"]."</option>";
            }
            echo $html;
        }

        break;

    case "comboxusu":
        
        $datos = $empresa->get_empresa_x_usu($_POST['usu_id']);
        if(is_array($datos) and count($datos) > 0){
            $html = "";
            foreach($datos as $row){
                $html.= "<option value='".$row["emp_id"]."'>".$row["emp_nom"]."</option>";
            }
            echo $html;
        }

        break;    
        
        case "guardaryeditar":

            if(empty($_POST['emp_id'])){
                $empresa->insert_empresa($_POST['emp_nom']);
            }else{
                $empresa->update_empresa($_POST['emp_id'],$_POST['emp_nom']);
            }  
    
            break; 
    
        case "listar":
            $datos = $empresa->get_empresatodo();
            $data = array();
            foreach ($datos as $row) {
                $sub_array = array();
                $sub_array[] = $row["emp_nom"];
                $sub_array[] = '<button type="button" onClick="editar(' . $row['emp_id'] . ');" id="' . $row['emp_id'] . '" class="btn btn-inline btn-waring btn-sm ladda-button"><i class="fa fa-edit"></i></button>';
                $sub_array[] = '<button type="button" onClick="eliminar(' . $row['emp_id'] . ');" id="' . $row['emp_id'] . '" class="btn btn-inline btn-danger btn-sm ladda-button"><i class="fa fa-trash"></i></button>';
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
            $empresa->delete_empresa($_POST["emp_id"]);
    
            break;
    
        case "mostrar":
            $datos = $empresa->get_empresa_x_id($_POST['emp_id']);
            if(is_array($datos) and count($datos) >0){
                foreach ($datos as $row) {
                    $output['emp_id'] = $row['emp_id'];
                    $output['emp_nom'] = $row['emp_nom'];
                }
                echo json_encode($output);
            }       
}
?>