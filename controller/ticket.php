<?php

require_once('../config/conexion.php');
require_once('../services/TicketService.php');
require_once('../services/TicketWorkflowService.php');
require_once('../services/TicketLister.php');
require_once('../services/TicketDetailLister.php');
require_once('../models/repository/TicketRepository.php');
require_once('../models/repository/NotificationRepository.php');
require_once('../models/repository/AssignmentRepository.php');

$pdo = Conectar::getConexion();

$ticket = new Ticket();
$ticketService = new TicketService($pdo);
$workflowService = new TicketWorkflowService();
$lister = new TicketLister();
$detailLister = new TicketDetailLister();

switch ($_GET["op"]) {

    case "insert":
        $result = $ticketService->createTicket($_POST);
        echo json_encode($result);
    break;
    
    case "listar_x_usu":
        $result = $lister->listTicketsByUser($_POST['usu_id']);
        echo json_encode($result);
    break;
    
    case "listar_x_agente":
        $result = $lister->listTicketsByAgent($_POST['usu_asig']);
        echo json_encode($result);
    break;

    case "listar":
        $result = $lister->listAllTickets();
        echo json_encode($result);
    break;
    
    case "listar_historial_tabla_x_agente":
        $result = $lister->listTicketsRecordByAgent($_POST['usu_id']);
        echo json_encode($result);
    break;

    case "listardetalle":
        $detailLister->listTicketDetails($_POST['tick_id']);
    break;
     
    case "listarhistorial":
        $detailLister->listTicketDetailRecord($_POST['tick_id']);
    break;

    case "listar_historial_tabla":
        $result = $lister->listAllTicketsRecord();
        echo json_encode($result);
    break;

    case "mostrar":
        $result = $ticketService->showTicket($_POST['tick_id']);
    break;

    case "get_transiciones":
        require_once('../models/FlujoPaso.php');
        $flujoPaso = new FlujoPaso();
        $transiciones = $flujoPaso->get_transiciones_por_paso($_POST["paso_id"]);
        echo json_encode($transiciones);
    break;
    
    case "insertdetalle":
        var_dump($_POST);
        $result = $ticketService->createDetailTicket($_POST);
    break;

    case "update":
        $ticket->update_ticket($_POST["tick_id"]);
        $ticket->insert_ticket_detalle_cerrar($_POST["tick_id"], $_POST["usu_id"]);
        break;

    case 'cerrar_con_nota':
        $files = isset($_FILES['cierre_files']) ? $_FILES['cierre_files'] : [];
        $ticket->cerrar_ticket_con_nota($_POST["tick_id"], $_POST["usu_id"], $_POST["nota_cierre"], $files);
        echo json_encode(["success" => true]);
        break;

    case "reabrir":
        $ticket->reabrir_ticket($_POST['tick_id']);
        // $correo->ticket_cerrado($_POST['tick_id']);
        $ticket->insert_ticket_detalle_reabrir($_POST['tick_id'], $_POST['usu_id']);
    break;

    case "updateasignacion":
        $ticket->update_ticket_asignacion($_POST['tick_id'], $_POST['usu_asig'], $_POST['how_asig']);
    break;
    
    case"calendario_x_usu_asig":
        $datos=$ticket->get_calendar_x_asig($_POST['usu_asig']);
        echo json_encode($datos);
    break;

    case"calendario_x_usu":
        $datos=$ticket->get_calendar_x_usu($_POST['usu_id']);
        echo json_encode($datos);
    break;    

    case "aprobar_flujo":
        $result = $workflowService->ApproveFlow($_POST, $_SESSION);
    break;    

    case "registrar_error":
        $result = $ticketService->LogErrorTicket($_POST);
    break;

    case "verificar_inicio_flujo":
        $result = $workflowService->CheckStartFlow($_POST);
    break;

    case "aprobar_paso":
        $resultado = $ticketService->approveStep($_POST['tick_id']);
        echo json_encode($resultado);
        break;

    case "rechazar_paso":
        $resultado = $ticketService->rejectStep($_POST['tick_id']);
        echo json_encode($resultado);
        break;
}
