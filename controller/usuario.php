<?php
require_once('../config/conexion.php');
require_once('../models/Usuario.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$usuario = new Usuario();

switch ($_GET["op"]) {

    case "guardaryeditar":
        if(empty($_POST["usu_id"])){
            $usuario->insert_usuario( $_POST["usu_nom"],$_POST["usu_ape"],$_POST["usu_correo"],$_POST["usu_pass"],$_POST["rol_id"]);
        }else{
            $usuario->update_usuario($_POST["usu_id"], $_POST["usu_nom"], $_POST["usu_ape"], $_POST["usu_correo"], $_POST["usu_pass"], $_POST["rol_id"]);
        }
    break;

    case "listar":

        $datos = $usuario->get_usuario();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row['usu_nom'];
            $sub_array[] = $row['usu_ape'];
            $sub_array[] = $row['usu_correo'];
          
            if($row['rol_id']==1){
                $sub_array[] = '<span class="label label-primary">Usuario</span>';
            }else {
                $sub_array[] = '<span class="label label-info">Soporte</span>';
            }

            $sub_array[] = '<button type="button" onClick="editar(' . $row['usu_id'] . ');" id="' . $row['usu_id'] . '" class="btn btn-inline btn-waring btn-sm ladda-button"><i class="fa fa-edit"></i></button>';
            $sub_array[] = '<button type="button" onClick="eliminar(' . $row['usu_id'] . ');" id="' . $row['usu_id'] . '" class="btn btn-inline btn-danger btn-sm ladda-button"><i class="fa fa-trash"></i></button>';

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
        $usuario->delete_usuario($_POST["usu_id"]);
    break;

    case "mostrar":

        $datos = $usuario->get_usuario_x_id($_POST['usu_id']);
        if(is_array($datos)==true and count($datos)>0){
            foreach($datos as $row){
                $output['usu_id'] = $row['usu_id'];
                $output['usu_nom'] = $row['usu_nom'];
                $output['usu_ape'] = $row['usu_ape'];
                $output['usu_correo'] = $row['usu_correo'];
                $output['usu_pass'] = $row['usu_pass'];
                $output['rol_id'] = $row['rol_id'];
            }
            echo json_encode($output);
        }
        
    break;  
    
    case "total":
        $datos = $usuario->get_usuario_total_id($_POST['usu_id']);
        if(is_array($datos)==true and count($datos)>0){
            foreach($datos as $row){
                $output['TOTAL'] = $row['TOTAL'];
            }
            echo json_encode($output);
        }
    break;  

    case "totalabierto":
        $datos = $usuario->get_usuario_totalabierto_id($_POST['usu_id']);
        if(is_array($datos)==true and count($datos)>0){
            foreach($datos as $row){
                $output['TOTAL'] = $row['TOTAL'];
            }
            echo json_encode($output);
        }
    break;

    case "totalcerrado":
        $datos = $usuario->get_usuario_totalcerrado_id($_POST['usu_id']);
        if(is_array($datos)==true and count($datos)>0){
            foreach($datos as $row){
                $output['TOTAL'] = $row['TOTAL'];
            }
            echo json_encode($output);
        }
    break;

    case "graficousuario":
        $datos = $usuario->get_total_categoria_usuario($_POST["usu_id"]);
            echo json_encode($datos);
    break; 
    
    case "usuariosxrol":
        $datos = $usuario->get_usuario_x_rol();
        if(is_array($datos) == true and count($datos) > 0){
            $html.="<option label='Seleccionar'></option>";
            foreach($datos as $row){
                $html.="<option value='".$row['usu_id']."'>".$row['usu_nom']." ".$row['usu_ape']."</option>";
            }
            echo $html;
        }
    break;    
}
