<?php

require_once('../config/conexion.php');
require_once('../services/TicketService.php');
require_once('../services/TicketWorkflowService.php');
require_once('../services/TicketLister.php');
require_once('../services/TicketDetailLister.php');

$ticket = new Ticket();
$ticketService = new TicketService();
$workflowService = new TicketWorkflowService();
$lister = new TicketLister();
$detailLister = new TicketDetailLister();



switch ($_GET["op"]) {

    case "insert":
        $result = $ticketService->createTicket($_POST, $_FILES);
        echo json_encode($result);
    break;
    
    case "aprobar_ticket_jefe":
        $result = $workflowService->BossTicketApproval($_POST, $_SESSION);   
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
    
    case "insertdetalle":
        $result = $ticketService->createDetailTicket($_POST["tick_id"], $_POST["usu_id"], $_POST['tickd_descrip']);
    break;

    case "update":
        $result = $ticketService->updateTicket($_POST['tick_id']);
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
}
