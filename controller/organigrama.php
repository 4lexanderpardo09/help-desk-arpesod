<?php
require_once('../config/conexion.php');
require_once('../models/Organigrama.php');
require_once('../models/Cargo.php');

$organigrama = new Organigrama();
$cargo = new Cargo();

switch ($_GET["op"]) {
    case "datos_grafico":
        $datos = $organigrama->get_organigrama_data();
        
        if (empty($datos)) {
            echo json_encode([]);
            break;
        }

        $subordinados = array_column($datos, 'from_node');
        $jefes = array_column($datos, 'to_node');
        
        $roots = array_diff(array_unique($jefes), array_unique($subordinados));
        
        $formatted_data = [];
        foreach ($datos as $row) {
            $formatted_data[] = [$row['from_node'], $row['to_node'], ''];
        }

        foreach ($roots as $root) {
            $formatted_data[] = [$root, '', ''];
        }
        
        echo json_encode($formatted_data);
        break;

    case "listar":
        $datos = $organigrama->get_organigrama_list();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["cargo_subordinado"];
            $sub_array[] = $row["cargo_jefe"];
            $sub_array[] = '<button type="button" onClick="eliminar(' . $row["org_id"] . ');"  id="' . $row["org_id"] . '" class="btn btn-outline-danger btn-icon"><div><i class="fa fa-trash"></i></div></button>';
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

    case "guardar":
        $car_id = $_POST["car_id"];
        $jefe_car_id = $_POST["jefe_car_id"];
        $organigrama->insert_jerarquia($car_id, $jefe_car_id);
        break;

    case "eliminar":
        $org_id = $_POST["org_id"];
        $organigrama->delete_jerarquia($org_id);
        break;
    
    case "combo_cargos":
        $datos = $cargo->get_cargos();
        $html = "";
        if(is_array($datos)==true and count($datos)>0){
            $html.="<option label='Seleccionar'></option>";
            foreach($datos as $row)
            {
                $html.= "<option value='".$row['car_id']."'>".$row['car_nom']."</option>";
            }
            echo $html;
        }
        break;
}
?>