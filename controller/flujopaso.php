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

    case "guardaryeditar":
        // AÑADIDO: Se maneja el valor del checkbox para la selección manual
        $requiere_seleccion_manual = isset($_POST['requiere_seleccion_manual']) ? 1 : 0;
        $es_tarea_nacional = isset($_POST['es_tarea_nacional']) ? 1 : 0;
        $es_aprobacion = isset($_POST['es_aprobacion']) ? 1 : 0;

        if (empty($_POST['paso_id'])) {
            // CAMBIADO: Se pasa el nuevo parámetro a la función de inserción
            $flujopaso->insert_paso(
                $_POST['flujo_id'],
                $_POST['paso_orden'],
                $_POST['paso_nombre'],
                $_POST['cargo_id_asignado'],
                $_POST['paso_tiempo_habil'],
                $_POST['paso_descripcion'],
                $requiere_seleccion_manual,
                $es_tarea_nacional,
                $es_aprobacion
            );
        } else {
            // CAMBIADO: Se pasa el nuevo parámetro a la función de actualización y se corrigen los parámetros
            $flujopaso->update_paso(
                $_POST['paso_id'],
                $_POST['paso_orden'],
                $_POST['paso_nombre'],
                $_POST['cargo_id_asignado'],
                $_POST['paso_tiempo_habil'],
                $_POST['paso_descripcion'],
                $requiere_seleccion_manual,
                $es_tarea_nacional,
                $es_aprobacion
            );
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
        // CAMBIADO: Se usa la función get_paso_por_id que es más directa
        $datos = $flujopaso->get_paso_por_id($_POST['paso_id']);
        if ($datos) {
            // No es necesario un bucle, ya que fetch() devuelve un solo resultado
            $output = $datos;
            // AÑADIDO: Aseguramos que el estado del checkbox también se envíe
            $output['requiere_seleccion_manual'] = $datos['requiere_seleccion_manual'];
            $output['es_aprobacion'] = $datos['es_aprobacion'];
            echo json_encode($output);
        }
}
