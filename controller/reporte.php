<?php
require_once('../config/conexion.php');
require_once('../models/Reporte.php');
require_once('../models/DateHelper.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$reporte = new Reporte();
header('Content-Type: application/json');

// --- 1. RECOLECCIÓN DE TODOS LOS FILTROS ---
// Datos de la sesión para la lógica de roles
$usu_id_sesion = isset($_SESSION['usu_id']) ? $_SESSION['usu_id'] : null;
$rol_id_real = isset($_SESSION['rol_id_real']) ? $_SESSION['rol_id_real'] : null;
$dp_id_sesion = isset($_SESSION['dp_id']) ? $_SESSION['dp_id'] : null;

// Filtros que vienen del dashboard (pueden estar vacíos)
$filtro_dp_id = isset($_POST['dp_id']) && !empty($_POST['dp_id']) ? $_POST['dp_id'] : null;
$filtro_cats_id = isset($_POST['cats_id']) && !empty($_POST['cats_id']) ? $_POST['cats_id'] : null;
$filtro_tick_id = isset($_POST['tick_id']) && !empty($_POST['tick_id']) ? $_POST['tick_id'] : null;


switch ($_GET["op"]) {

    case "get_kpis":
        $usu_id = ($rol_id_real == 3 || $dp_id_sesion > 0) ? null : $usu_id_sesion;
        $dp_id = ($rol_id_real == 3) ? $filtro_dp_id : ($dp_id_sesion > 0 ? $dp_id_sesion : null);

        $datos_estado = $reporte->get_tickets_por_estado($usu_id, $dp_id, $filtro_cats_id, $filtro_tick_id);
        $tiempoPromedio = $reporte->get_tiempo_promedio_resolucion($usu_id, $dp_id, $filtro_cats_id, $filtro_tick_id);
        
        $output = ["totalAbiertos" => 0, "totalCerrados" => 0];
        if (is_array($datos_estado)) {
            foreach ($datos_estado as $row) {
                if ($row['estado'] == 'Tickets Abiertos') $output["totalAbiertos"] = (int)$row['total'];
                if ($row['estado'] == 'Tickets Cerrados') $output["totalCerrados"] = (int)$row['total'];
            }
        }
        $output["tiempoPromedio"] = $tiempoPromedio;
        echo json_encode($output);
        break;

    case "get_tickets_por_mes":
        $usu_id = ($rol_id_real == 3 || $dp_id_sesion > 0) ? null : $usu_id_sesion;
        $dp_id = ($rol_id_real == 3) ? $filtro_dp_id : ($dp_id_sesion > 0 ? $dp_id_sesion : null);
        $datos_mes = $reporte->get_tickets_creados_por_mes($usu_id, $dp_id, $filtro_cats_id, $filtro_tick_id);
        
        $labels = array_column($datos_mes, 'mes');
        $data = array_map('intval', array_column($datos_mes, 'total_tickets'));
        echo json_encode(["labels" => $labels, "data" => $data]);
        break;

    case "get_carga_por_agente":
        $usu_id = ($rol_id_real == 3 || $dp_id_sesion > 0) ? null : $usu_id_sesion;
        $dp_id = ($rol_id_real == 3) ? $filtro_dp_id : ($dp_id_sesion > 0 ? $dp_id_sesion : null);
        $datos_agente = $reporte->get_carga_por_agente($usu_id, $dp_id, $filtro_cats_id, $filtro_tick_id);

        $labels = array_column($datos_agente, 'agente_de_soporte');
        $data = array_map('intval', array_column($datos_agente, 'tickets_abiertos'));
        echo json_encode(["labels" => $labels, "data" => $data]);
        break;

    case "get_top_categorias":
        $usu_id = ($rol_id_real == 3 || $dp_id_sesion > 0) ? null : $usu_id_sesion;
        $dp_id = ($rol_id_real == 3) ? $filtro_dp_id : ($dp_id_sesion > 0 ? $dp_id_sesion : null);
        $datos = $reporte->get_tickets_por_categoria($usu_id, $dp_id, $filtro_cats_id, $filtro_tick_id);
        echo json_encode($datos);
        break;

    case "get_top_usuarios":
        $usu_id = ($rol_id_real == 3 || $dp_id_sesion > 0) ? null : $usu_id_sesion;
        $dp_id = ($rol_id_real == 3) ? $filtro_dp_id : ($dp_id_sesion > 0 ? $dp_id_sesion : null);
        $datos = $reporte->get_usuarios_top_creadores($usu_id, $dp_id, $filtro_cats_id, $filtro_tick_id);
        echo json_encode($datos);
        break;
        
    case "get_tiempo_agente":
        $dp_id = ($rol_id_real == 3) ? $filtro_dp_id : ($dp_id_sesion > 0 ? $dp_id_sesion : null);
        $datos = $reporte->get_tiempo_promedio_por_agente(null, $dp_id, $filtro_cats_id, $filtro_tick_id);
        
        $labels = array_map(function ($row) { return $row['usu_nom'] . ' ' . $row['usu_ape']; }, $datos);
        $data = array_map('floatval', array_column($datos, 'horas_promedio_respuesta'));
        echo json_encode(["labels" => $labels, "data" => $data]);
        break;

    case "get_rendimiento_paso":
        $dp_id = ($rol_id_real == 3) ? $filtro_dp_id : ($dp_id_sesion > 0 ? $dp_id_sesion : null);
        $datos = $reporte->get_rendimiento_por_paso(null, $dp_id, $filtro_cats_id, $filtro_tick_id);
        echo json_encode($datos);
        break;

    case "get_errores_tipo":
        $dp_id = ($rol_id_real == 3) ? $filtro_dp_id : ($dp_id_sesion > 0 ? $dp_id_sesion : null);
        $datos = $reporte->get_conteo_errores_por_tipo(null, $dp_id, $filtro_cats_id, $filtro_tick_id);

        $labels = array_column($datos, 'tipo_de_error');
        $data = array_map('intval', array_column($datos, 'total_tickets'));
        echo json_encode(["labels" => $labels, "data" => $data]);
        break;
        
    case "get_errores_agente":
        $dp_id = ($rol_id_real == 3) ? $filtro_dp_id : ($dp_id_sesion > 0 ? $dp_id_sesion : null);
        $datos = $reporte->get_errores_atribuidos_por_agente(null, $dp_id, $filtro_cats_id, $filtro_tick_id);
        echo json_encode($datos);
        break;

    case "get_filtros_departamento":
        $datos = $reporte->get_departamentos_para_filtro();
        echo json_encode($datos);
        break;
        
    case "get_filtros_subcategoria":
        $datos = $reporte->get_subcategorias_para_filtro();
        echo json_encode($datos);
        break;

    case "get_tickets_resueltos":
        $usu_id = ($rol_id_real == 3 || $dp_id_sesion > 0) ? null : $usu_id_sesion;
        $dp_id = ($rol_id_real == 3) ? $filtro_dp_id : ($dp_id_sesion > 0 ? $dp_id_sesion : null);
        $datos = $reporte->get_tickets_resueltos_por_agente($usu_id, $dp_id, $filtro_cats_id, $filtro_tick_id);
        echo json_encode($datos);
        break;
}
?>