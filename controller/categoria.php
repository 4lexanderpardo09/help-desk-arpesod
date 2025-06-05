<?php
require_once('../config/conexion.php');
require_once('../models/Categoria.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$categoria = new Categoria();

switch ($_GET["op"]) {
    case "combo":
        
        $datos = $categoria->get_categoria();
        if(is_array($datos) and count($datos) > 0){
            // $html = "<option></option>";
            foreach($datos as $row){
                 $html.= "<option value='".$row["cat_id"]."'>".$row["cat_nom"]."</option>";
            }
            echo $html;
        }

        break;
}
?>