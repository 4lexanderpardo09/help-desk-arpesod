<?php

require_once('../models/DateHelper.php');
require_once('../models/FlujoPaso.php');
require_once('../models/Flujo.php');
require_once('../models/Usuario.php');
require_once('../models/Ticket.php');

class TicketWorkflowService
{
    private $dateHelper;
    private $flujoPasoModel;
    private $flujoModel;
    private $usuarioModel;
    private $ticketModel;

    public function __construct()
    {
        $this->dateHelper = new DateHelper();
        $this->flujoPasoModel = new FlujoPaso();
        $this->usuarioModel = new Usuario();
        $this->ticketModel = new Ticket();
        $this->flujoModel = new Flujo();
    }

    public function approveStep($tick_id)
    {
        $ticket_data = $this->ticketModel->listar_ticket_x_id($tick_id);
        $paso_actual_id = $ticket_data['paso_actual_id'];
        $siguiente_paso = $this->flujoPasoModel->get_siguiente_paso($paso_actual_id);

        if ($siguiente_paso) {
            $this->ticketModel->update_asignacion_y_paso($tick_id, $siguiente_paso['cargo_id_asignado'], $siguiente_paso['paso_id'], $_SESSION['usu_id']);
        } else {
            $this->ticketModel->update_ticket($tick_id);
            $this->ticketModel->insert_ticket_detalle_cerrar($tick_id, $_SESSION['usu_id']);
        }
    }

    public function rejectStep($tick_id)
    {
        $asignacion_actual = $this->ticketModel->get_ultima_asignacion($tick_id);
        $asignacion_anterior = $this->ticketModel->get_penultima_asignacion($tick_id);

        if ($asignacion_anterior) {
            $this->ticketModel->update_asignacion_y_paso($tick_id, $asignacion_anterior['usu_asig'], $asignacion_anterior['paso_actual_id'], $_SESSION['usu_id']);
        }
    }

    public function ApproveFlow($tickPost, $session)
    {
        $tick_id = $tickPost['tick_id'];
        $jefe_id = $session['usu_id']; // El jefe que está aprobando

        // Obtenemos los datos del ticket para saber quién lo creó y de qué subcategoría es
        $datos_ticket = $this->ticketModel->listar_ticket_x_id($tick_id)[0];
        $usu_id_creador = $datos_ticket['usu_id'];
        $cats_id = $datos_ticket['cats_id'];

        // Obtenemos todos los datos del usuario creador
        $datos_creador = $this->usuarioModel->get_usuario_x_id($usu_id_creador);
        $creador_car_id = $datos_creador['car_id'];
        $creador_reg_id = $datos_creador['reg_id'];

        // Buscamos el flujo y su primer paso
        $flujo = $this->flujoModel->get_flujo_por_subcategoria($cats_id);
        if ($flujo) {
            $paso_inicial = $this->flujoModel->get_paso_inicial_por_flujo($flujo['flujo_id']);
            if ($paso_inicial) {
                $primer_paso_id = $paso_inicial['paso_id'];
                $primer_cargo_id = $paso_inicial['cargo_id_asignado'];

                // Buscamos al agente que cumple con el primer cargo y la regional del creador
                $primer_agente_info = $this->usuarioModel->get_usuario_por_cargo_y_regional($primer_cargo_id, $creador_reg_id);

                if ($primer_agente_info) {
                    $primer_agente_id = $primer_agente_info['usu_id'];
                    
                    // Actualizamos el ticket: se lo asignamos al primer agente y le ponemos el primer paso
                    $this->ticketModel->update_asignacion_y_paso($tick_id, $primer_agente_id, $primer_paso_id, $jefe_id);
                }
            }
        }
    }

    public function CheckStartFlow($dataPost)
    {
        $cats_id = $dataPost['cats_id'];
        $output = ['requiere_seleccion' => false, 'usuarios' => []];

        // Buscamos el flujo asociado a la subcategoría
        $flujo = $this->flujoModel->get_flujo_por_subcategoria($cats_id);
        if ($flujo) {
            // Si hay flujo, buscamos su primer paso
            $primer_paso = $this->flujoModel->get_paso_inicial_por_flujo($flujo['flujo_id']);
            if ($primer_paso && $primer_paso['requiere_seleccion_manual'] == 1) {
                // Si el primer paso requiere selección manual, preparamos la respuesta
                $output['requiere_seleccion'] = true;
                $cargo_id_necesario = $primer_paso['cargo_id_asignado'];
                
                // Buscamos a TODOS los usuarios con ese cargo
                $usuarios = $this->usuarioModel->get_usuarios_por_cargo($cargo_id_necesario);
                $output['usuarios'] = $usuarios;
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($output);
    }


}
?>