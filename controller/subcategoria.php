<?php
require_once('../config/conexion.php');
require_once('../models/Subcategoria.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$subcategoria = new Subcategoria();

switch ($_GET["op"]) {
    case "combo":
        
        $datos = $subcategoria->get_subcategoria($_POST['cat_id']);
        if(is_array($datos) and count($datos) > 0){
            $html = "";
            foreach($datos as $row){
                $html.= "<option value='".$row["cats_id"]."'>".$row["cats_nom"]."</option>";
            }
            echo $html;
        }

        break;
}
?>