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

// Parámetros adicionales para nuevos endpoints
$sla_hours = isset($_POST['hours']) ? (int)$_POST['hours'] : 48;
$aging_buckets = isset($_POST['buckets']) ? $_POST['buckets'] : null; // esperar array (JSON) o null
$limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;

switch ($_GET["op"]) {

    // -------------- EXISTENTES (sin cambios lógicos) --------------

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
        // NOTA: mantenemos la versión original por compatibilidad
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

    // -------------- NUEVOS ENDPOINTS (añadidos) --------------

    case "apply_recommended_indexes":
        // Ejecuta alter table para índices recomendados (prueba en staging primero)
        $results = $reporte->apply_recommended_indexes();
        echo json_encode($results);
        break;

    case "get_recommended_indexes_sql":
        // Devuelve script SQL para aplicar manualmente (más seguro para prod)
        $script = $reporte->get_recommended_indexes_sql_script();
        echo json_encode(['sql' => $script]);
        break;

    case "create_kpi_table":
        $res = $reporte->create_kpi_summary_table();
        echo json_encode($res);
        break;

    case "populate_kpi_summary":
        $res = $reporte->populate_kpi_summary_once();
        echo json_encode($res);
        break;

    case "create_kpi_trigger":
        $res = $reporte->create_trigger_update_summary_on_close();
        echo json_encode($res);
        break;

    case "rebuild_kpi_summary":
        $res = $reporte->rebuild_kpi_summary_full();
        echo json_encode($res);
        break;

    case "get_tiempo_agente_opt":
        // Versión optimizada del tiempo por agente
        $dp_id = ($rol_id_real == 3) ? $filtro_dp_id : ($dp_id_sesion > 0 ? $dp_id_sesion : null);
        $datos = $reporte->get_tiempo_promedio_por_agente_optimizado(null, $dp_id, $filtro_cats_id, $filtro_tick_id);
        $labels = array_map(function ($row) { return $row['usu_nom'] . ' ' . $row['usu_ape']; }, $datos);
        $data = array_map('floatval', array_column($datos, 'horas_promedio_respuesta'));
        echo json_encode(["labels" => $labels, "data" => $data]);
        break;

    case "get_tiempo_primera_respuesta":
        $usu_id = ($rol_id_real == 3 || $dp_id_sesion > 0) ? null : $usu_id_sesion;
        $dp_id = ($rol_id_real == 3) ? $filtro_dp_id : ($dp_id_sesion > 0 ? $dp_id_sesion : null);
        $res = $reporte->get_tiempo_primera_respuesta($usu_id, $dp_id, $filtro_cats_id);
        echo json_encode(['horas_promedio_primera_respuesta' => $res]);
        break;

    case "get_sla_compliance":
        $usu_id = ($rol_id_real == 3 || $dp_id_sesion > 0) ? null : $usu_id_sesion;
        $dp_id = ($rol_id_real == 3) ? $filtro_dp_id : ($dp_id_sesion > 0 ? $dp_id_sesion : null);
        $hours = isset($_POST['hours']) ? (int)$_POST['hours'] : $sla_hours;
        $res = $reporte->get_sla_compliance($hours, $usu_id, $dp_id, $filtro_cats_id);
        echo json_encode($res);
        break;

    case "get_aging_backlog":
        // buckets puede venir como JSON string array via POST['buckets']
        if (!is_null($aging_buckets) && is_string($aging_buckets)) {
            $decoded = json_decode($aging_buckets, true);
            if (is_array($decoded)) $aging_buckets = $decoded;
        }
        $res = $reporte->get_aging_backlog($aging_buckets);
        echo json_encode($res);
        break;

    case "get_reopen_rate":
        $res = $reporte->get_reopen_rate_estimate();
        echo json_encode($res);
        break;

    case "get_top_categorias_tiempo":
        $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : $limit;
        $res = $reporte->get_top_categorias_tiempo_resolucion($limit);
        echo json_encode($res);
        break;

    default:
        echo json_encode(['error' => 'Operación no definida']);
        break;
}
?>
