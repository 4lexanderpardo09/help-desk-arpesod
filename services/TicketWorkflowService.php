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


    public function BossTicketApproval($tickPost, $session)
    {
        try {
            // Validar input
            $tick_id = isset($tickPost['tick_id']) ? intval($tickPost['tick_id']) : null;
            $nuevo_asignado_id = isset($tickPost['nuevo_asignado_id']) ? intval($tickPost['nuevo_asignado_id']) : null;
            $jefe_id = $session['usu_id'] ?? null;

            if (!$jefe_id) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'No autenticado.']);
                return;
            }
            if (!$tick_id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Parámetro tick_id requerido.']);
                return;
            }

            // 1. Obtener ticket
            $ticket_data = $this->ticketModel->listar_ticket_x_id($tick_id);
            if (!$ticket_data) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Ticket no encontrado.']);
                return;
            }

            $cats_id = $ticket_data['cats_id'];
            $regional_id_creador = $this->ticketModel->get_ticket_region($tick_id);

            // 2. Estado del paso de aprobación
            $estado_paso_aprobacion = 'N/A';
            $asignacion_actual = $this->ticketModel->get_ultima_asignacion($tick_id);
            if ($asignacion_actual) {
                $dias_habiles_aprobacion = 2;
                $fecha_asignacion = $asignacion_actual['fech_asig'];
                $fecha_limite = DateHelper::calcularFechaLimiteHabil($fecha_asignacion, $dias_habiles_aprobacion);
                $estado_paso_aprobacion = (new DateTime() > $fecha_limite) ? 'Atrasado' : 'A Tiempo';
            }

            // 3. Flujo y primer paso
            $flujo_data = $this->flujoModel->get_flujo_por_subcategoria($cats_id);
            if (!$flujo_data) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'No hay flujo para esta subcategoría.']);
                return;
            }
            $primer_paso = $this->flujoModel->get_paso_inicial_por_flujo($flujo_data['flujo_id']);
            if (!$primer_paso) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'El flujo no tiene paso inicial.']);
                return;
            }

            $paso_id_siguiente = $primer_paso['paso_id'];
            $cargo_siguiente_paso = $primer_paso['cargo_id_asignado'];

            // 4. Determinar nuevo asignado
            $nuevo_asignado_info = null;
            $datos_siguiente_paso = $this->flujoPasoModel->get_paso_por_id($paso_id_siguiente);

            if ($nuevo_asignado_id) {
                $nuevo_asignado_info = $this->usuarioModel->get_usuario_x_id($nuevo_asignado_id);
            } else {
                if (!empty($datos_siguiente_paso['es_tarea_nacional']) && $datos_siguiente_paso['es_tarea_nacional'] == 1) {
                    $nuevo_asignado_info = $this->usuarioModel->get_usuario_nacional_por_cargo($cargo_siguiente_paso);
                } else {
                    $nuevo_asignado_info = $this->usuarioModel->get_usuario_por_cargo_y_regional($cargo_siguiente_paso, $regional_id_creador);
                }
            }

            if (!$nuevo_asignado_info || empty($nuevo_asignado_info['usu_id'])) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'No se encontró un agente disponible para el siguiente paso.']);
                return;
            }

            // 5. Realizar cambios en BD (usar transacción si procede)
            // Si tus modelos no gestionan transacciones, añade begin/commit en tu DB layer.
            $nuevo_usuario_asignado = $nuevo_asignado_info['usu_id'];

            // Ejecutar actualización
            $this->ticketModel->update_asignacion_y_paso($tick_id, $nuevo_usuario_asignado, $paso_id_siguiente, $jefe_id);

            if ($asignacion_actual) {
                $this->ticketModel->update_estado_tiempo_paso($asignacion_actual['th_id'], $estado_paso_aprobacion);
            }

            $this->ticketModel->insert_ticket_detalle($tick_id, $jefe_id, "Ticket aprobado por jefe. El flujo de trabajo ha comenzado.");

            echo json_encode([
                'success' => true,
                'message' => 'Aprobación completada exitosamente.',
                'tick_id' => $tick_id,
                'nuevo_asignado' => $nuevo_usuario_asignado,
                'paso' => $paso_id_siguiente
            ]);
        } catch (\Throwable $ex) {
            // Log del error real para debugging del servidor
            error_log("BossTicketApproval error: " . $ex->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error interno al aprobar el ticket.']);
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