<?php
    require_once("../config/conexion.php");
    require_once("../models/Mensaje.php");
    $mensaje = new Mensaje();

    switch($_GET["op"]){

        case "listar_conversaciones":
            $datos = $mensaje->get_conversaciones($_SESSION["usu_id"]);
            $data = array();
            foreach ($datos as $row) {
                $sub_array = array();
                $nombre_para = "";
                $para_usu_id = 0;

                if ($row["de_usu_id"] == $_SESSION["usu_id"]) {
                    $nombre_para = $row["para_usu_nom"] . " " . $row["para_usu_ape"];
                    $para_usu_id = $row["para_usu_id"];
                } else {
                    $nombre_para = $row["de_usu_nom"] . " " . $row["de_usu_ape"];
                    $para_usu_id = $row["de_usu_id"];
                }

                $sub_array[] = '<button type="button" onClick="ver_mensajes(' . $row["conv_id"] . ',' . $para_usu_id . ',' . "'" . htmlspecialchars($nombre_para, ENT_QUOTES) . "'" . ');" id="' . $row["conv_id"] . '" class="btn btn-inline btn-primary btn-sm ladda-button"><i class="fa fa-eye"></i></button>';
                $sub_array[] = $nombre_para;
                
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

        case "listar_mensajes":
            $datos=$mensaje->get_mensajes($_POST["conv_id"]);
            foreach($datos as $key => $row){
                $datos[$key]["de_usu_nom"] = $mensaje->get_usuario_nombre($row["de_usu_id"]);
            }
            echo json_encode($datos);
        break;

        case "enviar_mensaje":
            $mensaje->insert_mensaje($_POST["conv_id"], $_SESSION["usu_id"], $_POST["mensaje"]);
        break;

        case "crear_conversacion":
            $de_usu_id = $_SESSION["usu_id"];
            $para_usu_id = $_POST["para_usu_id"];
            
            // Verificar si ya existe una conversación
            $conversacion_existente = $mensaje->get_conversacion_por_usuarios($de_usu_id, $para_usu_id);
            
            if ($conversacion_existente) {
                // Si existe, devolver el ID de la conversación existente
                echo json_encode(array('conv_id' => $conversacion_existente['conv_id']));
            } else {
                // Si no existe, crear una nueva conversación
                $conv_id = $mensaje->create_conversacion($de_usu_id, $para_usu_id);
                echo json_encode(array('conv_id' => $conv_id));
            }
        break;

        case "get_total_mensajes_no_leidos":
            $total = $mensaje->get_total_mensajes_no_leidos($_SESSION["usu_id"]);
            echo json_encode(array('total' => $total));
        break;
    }
?>