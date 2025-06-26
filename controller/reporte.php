<?php
require_once('../config/conexion.php');
require_once('../models/Reporte.php'); // Asegúrate que el nombre de tu modelo es correcto

$reporte = new Reporte();
header('Content-Type: application/json'); // Ponemos la cabecera aquí para todos los casos

switch ($_GET["op"]) {

    // Operación para las tarjetas KPI
    case "get_kpis":
        $output = array();
        $totalAbiertos = 0;
        $totalCerrados = 0;
        $datos_estado = $reporte->get_tickets_por_estado();
        foreach ($datos_estado as $row) {
            if ($row['estado'] == 'Tickets Abiertos') $totalAbiertos = (int)$row['total'];
            if ($row['estado'] == 'Tickets Cerrados') $totalCerrados = (int)$row['total'];
        }
        $output["totalAbiertos"] = $totalAbiertos;
        $output["totalCerrados"] = $totalCerrados;
        $output["tiempoPromedio"] = $reporte->get_tiempo_promedio_resolucion();
        echo json_encode($output);
        break;

    // Operación para el gráfico de Tickets por Mes
    case "get_tickets_por_mes":
        $datos_mes = $reporte->get_tickets_creados_por_mes();
        $labels = array();
        $data = array();
        foreach ($datos_mes as $row) {
            $labels[] = $row['mes'];
            $data[] = (int)$row['total_tickets'];
        }
        echo json_encode(["labels" => $labels, "data" => $data]);
        break;

    // Operación para el gráfico de Carga por Agente
    case "get_carga_por_agente":
        $datos_agente = $reporte->get_carga_por_agente();
        $labels = array();
        $data = array();
        foreach ($datos_agente as $row) {
            $labels[] = $row['agente_de_soporte'];
            $data[] = (int)$row['tickets_abiertos'];
        }
        echo json_encode(["labels" => $labels, "data" => $data]);
        break;

    // Operación para la tabla de Top Categorías
    case "get_top_categorias":
        $datos = $reporte->get_tickets_por_categoria();
        echo json_encode($datos);
        break;

    // Operación para la tabla de Top Usuarios
    case "get_top_usuarios":
        $datos = $reporte->get_usuarios_top_creadores();
        echo json_encode($datos);
        break;


    
    // Operación para las tarjetas KPI x agente
    case "get_kpis_x_agente":
        $output = array();
        $totalAbiertos = 0;
        $totalCerrados = 0;
        $datos_estado = $reporte->get_tickets_por_estado_x_agente($_POST['usu_asig']);
        foreach ($datos_estado as $row) {
            if ($row['estado'] == 'Tickets Abiertos') $totalAbiertos = (int)$row['total'];
            if ($row['estado'] == 'Tickets Cerrados') $totalCerrados = (int)$row['total'];
        }
        $output["totalAbiertos"] = $totalAbiertos;
        $output["totalCerrados"] = $totalCerrados;
        $output["tiempoPromedio"] = $reporte->get_tiempo_promedio_resolucion_x_agente($_POST['usu_asig']);
        echo json_encode($output);
        break;

    // Operación para el gráfico de Tickets por Mes
    case "get_tickets_por_mes_x_agente":
        $datos_mes = $reporte->get_tickets_creados_por_mes_x_agente($_POST['usu_asig']);
        $labels = array();
        $data = array();
        foreach ($datos_mes as $row) {
            $labels[] = $row['mes'];
            $data[] = (int)$row['total_tickets'];
        }
        echo json_encode(["labels" => $labels, "data" => $data]);
        break;

    // Operación para la tabla de Top Categorías
    case "get_top_categorias_x_agente":
        $datos = $reporte->get_tickets_por_categoria_x_agente($_POST['usu_asig']);
        echo json_encode($datos);
        break;

    // Operación para la tabla de Top Usuarios
    case "get_top_usuarios_x_agente":
        $datos = $reporte->get_usuarios_top_creadores_x_agente($_POST['usu_asig']);
        echo json_encode($datos);
        break;    
}
?>