<?php
require_once('../config/conexion.php');
require_once('../models/Prioridad.php');
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
}
?>