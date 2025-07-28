<?php
require_once('../config/conexion.php');
require_once('../models/Categoria.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$categoria = new Categoria();

switch ($_GET["op"]) {

    case "listar":
        // CAMBIADO: Se usa la nueva función get_categorias() del modelo
        $datos = $categoria->get_categorias();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["cat_nom"];
            // CAMBIADO: Se usan las nuevas columnas con los nombres concatenados
            $sub_array[] = $row["empresas"] ?? 'Sin asignar';
            $sub_array[] = $row["departamentos"] ?? 'Sin asignar';
            $sub_array[] = '<button type="button" onClick="editar(' . $row['cat_id'] . ');" id="' . $row['cat_id'] . '" class="btn btn-inline btn-waring btn-sm ladda-button"><i class="fa fa-edit"></i></button>';
            $sub_array[] = '<button type="button" onClick="eliminar(' . $row['cat_id'] . ');" id="' . $row['cat_id'] . '" class="btn btn-inline btn-danger btn-sm ladda-button"><i class="fa fa-trash"></i></button>';
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

    case "guardaryeditar": 
        // NUEVA LÓGICA: Ahora se manejan arrays para empresas y departamentos
        $cat_id = isset($_POST['cat_id']) && !empty($_POST['cat_id']) ? $_POST['cat_id'] : null;
        $cat_nom = $_POST['cat_nom'];
        // Se asegura de que los arrays existan, incluso si vienen vacíos
        $emp_ids = isset($_POST['emp_ids']) ? $_POST['emp_ids'] : [];
        $dp_ids = isset($_POST['dp_ids']) ? $_POST['dp_ids'] : [];


        if (is_null($cat_id)) {
            // Llama a la nueva función de inserción con arrays
            $categoria->insert_categoria($cat_nom, $emp_ids, $dp_ids);
        } else {
            // Llama a la nueva función de actualización con arrays
            $categoria->update_categoria($cat_id, $cat_nom, $emp_ids, $dp_ids);
        }
        break;

    case "mostrar":
        // CAMBIADO: La nueva función del modelo devuelve un array estructurado
        $datos = $categoria->get_categoria_x_id($_POST['cat_id']);
        if ($datos) {
            // Se envía el JSON completo, el JavaScript se encargará de leerlo
            echo json_encode($datos);
        }
        break;

    case "eliminar":
        // SIN CAMBIOS: La lógica de eliminación sigue siendo la misma
        $categoria->delete_categoria($_POST["cat_id"]);
        break;

    case "combo":
        // CAMBIADO: Se usa la nueva función para buscar en las tablas de unión
        $datos = $categoria->get_categoria_por_empresa_y_dpto($_POST['emp_id'], $_POST['dp_id']);
        $html = "";
        if (is_array($datos) && count($datos) > 0) {
            foreach ($datos as $row) {
                $html .= "<option value='" . $row["cat_id"] . "'>" . $row["cat_nom"] . "</option>";
            }
        }
        echo $html;
        break;

        case "combocat":
    // CAMBIADO: Se usa la nueva función para buscar en las tablas de unión
    $datos = $categoria->get_categorias();
    $html = "";
    if (is_array($datos) && count($datos) > 0) {
        foreach ($datos as $row) {
            $html .= "<option value='" . $row["cat_id"] . "'>" . $row["cat_nom"] . "</option>";
        }
    }
    echo $html;
    break;

        
}
?>