<?php
require_once('../config/conexion.php');
require_once('../models/FlujoPaso.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$flujopaso = new FlujoPaso();

switch ($_GET["op"]) {
    case "combo":
        $datos = $flujopaso->get_flujopaso();
        if (is_array($datos) and count($datos) > 0) {
            $html = "";
            foreach ($datos as $row) {
                // CORREGIDO: La columna se llama 'paso_nombre', no 'flujopaso_nom'
                $html .= "<option value='" . $row["paso_id"] . "'>" . $row["paso_nombre"] . "</option>";
            }
            echo $html;
        }
        break;

    case "combo_por_flujo":
        if (isset($_POST["flujo_id"])) {
            // Necesitas una función en tu modelo FlujoPaso que obtenga los pasos por flujo_id
            $datos = $flujopaso->get_pasos_por_flujo($_POST["flujo_id"]);
            $html = "<option value='' selected disabled>-- Seleccione un Paso --</option>";
            if (is_array($datos) && count($datos) > 0) {
                foreach ($datos as $row) {
                    // El valor es el ID del paso, el texto es el nombre del paso
                    $html .= "<option value='" . $row['paso_id'] . "'>" . $row['paso_nombre'] . "</option>";
                }
            }
            echo $html;
        }
        break;

    case "guardaryeditar":
        $requiere_seleccion_manual = isset($_POST['requiere_seleccion_manual']) ? 1 : 0;
        $es_tarea_nacional = isset($_POST['es_tarea_nacional']) ? 1 : 0;
        $es_aprobacion = isset($_POST['es_aprobacion']) ? 1 : 0;
        $permite_cerrar = isset($_POST['permite_cerrar']) ? 1 : 0;

        $paso_nom_adjunto = '';
        if (isset($_FILES['paso_nom_adjunto']) && $_FILES['paso_nom_adjunto']['name'] != '') {
            $file_name = $_FILES['paso_nom_adjunto']['name'];
            $extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $new_file_name = uniqid('paso_', true) . '.' . $extension;
            $destination_path = '../public/document/paso/' . $new_file_name;
            if (move_uploaded_file($_FILES['paso_nom_adjunto']['tmp_name'], $destination_path)) {
                $paso_nom_adjunto = $new_file_name;
            } else {
                // Handle file upload error if necessary
                $paso_nom_adjunto = '';
            }
        } else {
            $paso_nom_adjunto = isset($_POST['current_paso_nom_adjunto']) ? $_POST['current_paso_nom_adjunto'] : '';
        }

        if (empty($_POST['paso_id'])) {
            $paso_id = $flujopaso->insert_paso(
                $_POST['flujo_id'],
                $_POST['paso_orden'],
                $_POST['paso_nombre'],
                $_POST['cargo_id_asignado'],
                $_POST['paso_tiempo_habil'],
                $_POST['paso_descripcion'],
                $requiere_seleccion_manual,
                $es_tarea_nacional,
                $es_aprobacion,
                $paso_nom_adjunto,
                $permite_cerrar
            );
        } else {
            $paso_id = $_POST['paso_id'];
            $flujopaso->update_paso(
                $paso_id,
                $_POST['paso_orden'],
                $_POST['paso_nombre'],
                $_POST['cargo_id_asignado'],
                $_POST['paso_tiempo_habil'],
                $_POST['paso_descripcion'],
                $requiere_seleccion_manual,
                $es_tarea_nacional,
                $es_aprobacion,
                $paso_nom_adjunto,
                $permite_cerrar
            );
        }

        if ($requiere_seleccion_manual && isset($_POST['usuarios_especificos']) && is_array($_POST['usuarios_especificos'])) {
            $flujopaso->set_usuarios_especificos($paso_id, $_POST['usuarios_especificos']);
        } else {
            // Si no se requiere selección manual o no se envían usuarios, se limpia la tabla
            $flujopaso->set_usuarios_especificos($paso_id, []);
        }

        break;

    case "listar":
        $datos = $flujopaso->get_pasos_por_flujo($_POST['flujo_id']);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["paso_orden"];
            $sub_array[] = $row["paso_nombre"];
            $sub_array[] = $row["car_nom"];
            $sub_array[] = ($row["requiere_seleccion_manual"] == 1) ? '<span class="label label-info">Sí</span>' : '<span class="label label-default">No</span>';
            $sub_array[] = ($row["es_tarea_nacional"] == 1) ? '<span class="label label-info">Sí</span>' : '<span class="label label-default">No</span>';
            $sub_array[] = ($row["es_aprobacion"] == 1) ? '<span class="label label-info">Sí</span>' : '<span class="label label-default">No</span>';
            $sub_array[] = '<button type="button" onClick="editar(' . $row['paso_id'] . ');" class="btn btn-inline btn-warning btn-sm"><i class="fa fa-edit"></i></button>';
            $sub_array[] = '<button type="button" onClick="eliminar(' . $row['paso_id'] . ');" class="btn btn-inline btn-danger btn-sm"><i class="fa fa-trash"></i></button>';
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
        $flujopaso->delete_paso($_POST["paso_id"]);
        break;

    case "get_siguientes_pasos":
        $datos = $flujopaso->get_siguientes_pasos($_POST['paso_actual_id']);
        echo json_encode($datos);
        break;

    case "get_usuarios_por_paso":
        $paso_id = $_POST['paso_id'];
        // Obtenemos los datos del paso para saber qué cargo se necesita
        $paso_data = $flujopaso->get_paso_por_id($paso_id);
        if ($paso_data) {
            $cargo_id_necesario = $paso_data['cargo_id_asignado'];
            // Buscamos a TODOS los usuarios con ese cargo
            $usuarios = $usuario->get_usuarios_por_cargo($cargo_id_necesario);
            
            $html = "<option value=''>Seleccione un Agente</option>";
            if (is_array($usuarios) && count($usuarios) > 0) {
                foreach ($usuarios as $row) {
                    // Mostramos el nombre y su regional para poder diferenciarlos
                    $html .= "<option value='" . $row["usu_id"] . "'>" . $row["usu_nom"] . " " . $row["usu_ape"] . "</option>";
                }
            }
            echo $html;
        }
        break;

    case "mostrar":
        $datos = $flujopaso->get_paso_por_id($_POST['paso_id']);
        if ($datos) {
            $output = $datos;
            $output['requiere_seleccion_manual'] = $datos['requiere_seleccion_manual'];
            $output['es_aprobacion'] = $datos['es_aprobacion'];
            $output['permite_cerrar'] = $datos['permite_cerrar'];
            $output['paso_nom_adjunto'] = isset($datos['paso_nom_adjunto']) ? $datos['paso_nom_adjunto'] : null;
            echo json_encode($output);
        }
}
