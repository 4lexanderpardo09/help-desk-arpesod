<?php
require_once('../config/conexion.php');
require_once('../models/Documento.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$documento = new Documento();

switch ($_GET["op"]) {
    case "listar":
        $datos = $documento->get_documento_x_ticket($_POST["tick_id"]);
        $data = array();

        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = '<a href="../../public/document/ticket/' . $_POST["tick_id"] . '/' . $row["doc_nom"] . '" target="_blank">' . $row["doc_nom"] . '</a>';
            $sub_array[] = '<a type="button" href="../../public/document/ticket/' . $_POST["tick_id"] . '/' . $row["doc_nom"] . '" target="_blank" class="btn btn-inline btn-primary btn-sm ladda-button"><i class="fa fa-eye"></i></a>';
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
}
