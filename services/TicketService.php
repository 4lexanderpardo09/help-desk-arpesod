<?php

require_once('../models/Ticket.php');
require_once('../models/Usuario.php');
require_once('../models/Documento.php');
require_once('../models/Flujo.php');
require_once('../models/FlujoPaso.php');
require_once('../models/Departamento.php');
require_once('../models/DateHelper.php');
require_once('../models/FlujoTransicion.php');
require_once('../models/Ruta.php');
require_once('../models/RutaPaso.php');
require_once('../services/TicketWorkflowService.php');

use models\repository\TicketRepository;
use models\repository\NotificationRepository;
use models\repository\AssignmentRepository;
use PDO;
use Exception;


class TicketService
{
    private $ticketModel;
    private $usuarioModel;
    private $documentoModel;
    private $flujoModel;
    private $flujoPasoModel;
    private $departamentoModel;
    private $dateHelper;
    private $workflowService;
    private $flujoTransicionModel;
    private $rutaModel;
    private $rutaPasoModel;


    private TicketRepository $ticketRepository;
    private NotificationRepository $notificationRepository;
    private AssignmentRepository $assignmentRepository;
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->ticketModel = new Ticket();
        $this->usuarioModel = new Usuario();
        $this->documentoModel = new Documento();
        $this->flujoModel = new Flujo();
        $this->flujoPasoModel = new FlujoPaso();
        $this->departamentoModel = new Departamento();
        $this->dateHelper = new DateHelper();
        $this->workflowService = new TicketWorkflowService();
        $this->flujoTransicionModel = new FlujoTransicion();
        $this->rutaModel = new Ruta();
        $this->rutaPasoModel = new RutaPaso();

        $this->pdo = $pdo;
        $this->ticketRepository = new TicketRepository($pdo);
        $this->notificationRepository = new NotificationRepository($pdo);
        $this->assignmentRepository = new AssignmentRepository($pdo);
    }

    public function resolveAssigned($flujo, $usu_id_creador, $ticket_reg_id)
    {
        $datos_creador = $this->usuarioModel->get_usuario_x_id($usu_id_creador);
        $creador_car_id = $datos_creador['car_id'] ?? null;
        $errors = [];
        $paso_actual_id_final = null;

        if ($flujo) { {
                $paso_inicial = $this->flujoModel->get_paso_inicial_por_flujo($flujo['flujo_id']);
                $paso_actual_id_final = $paso_inicial ? $paso_inicial['paso_id'] : null;

                if (!$paso_inicial) {
                    $errors[] = "El flujo (id: {$flujo['flujo_id']}) no tiene paso inicial definido.";
                } else {
                    if (empty($usu_asig_final)) {
                        $asignado_car_id = $paso_inicial['cargo_id_asignado'] ?? null;
                        if (!$asignado_car_id) {
                            $errors[] = "El paso inicial no tiene cargo asignado.";
                        } else {
                            if (!empty($paso_inicial['es_tarea_nacional']) && $paso_inicial['es_tarea_nacional'] == 1) {
                                $nuevo_asignado_info = $this->usuarioModel->get_usuario_nacional_por_cargo($asignado_car_id);
                            } else {
                                $nuevo_asignado_info = $this->usuarioModel->get_usuario_por_cargo_y_regional($asignado_car_id, $ticket_reg_id);
                            }

                            if ($nuevo_asignado_info && !empty($nuevo_asignado_info['usu_id'])) {
                                $usu_asig_final = $nuevo_asignado_info['usu_id'];
                            } else {
                                $errors[] = "No se encontró un usuario automático para cargo_id {$asignado_car_id} (paso inicial).";
                            }
                        }
                    }
                }
            }
        } else {
            if (empty($usu_asig_final)) {
                $errors[] = "No existe flujo para la subcategoría y no se suministró un usuario asignado manualmente.";
            }
        }

        return [
            'usu_asig_final' => $usu_asig_final ?? null,
            'paso_actual_id_final' => $paso_actual_id_final,
            'errors' => $errors
        ];
    }

    public function insertDocument($datos)
    {
        $output = [];

        if ($datos >= 0) {
            $output['tick_id'] = $datos;

            if (!empty($_FILES['files']) && is_array($_FILES['files']['name'])) {
                $countFiles = count($_FILES['files']['name']);
                $ruta = '../public/document/ticket/' . $output['tick_id'] . '/';

                if (!file_exists($ruta)) {
                    mkdir($ruta, 0777, true);
                }

                for ($i = 0; $i < $countFiles; $i++) {
                    if ($_FILES['files']['error'][$i] === UPLOAD_ERR_OK) {
                        $nombreArchivo = basename($_FILES['files']['name'][$i]);
                        $tmpArchivo    = $_FILES['files']['tmp_name'][$i];
                        $destino       = $ruta . $nombreArchivo;
                        $this->documentoModel->insert_documento($output['tick_id'], $nombreArchivo);
                        move_uploaded_file($tmpArchivo, $destino);
                    }
                }

                return $output;

            }
        }
    }

    public function createTicket($postData)
    {
        $this->pdo->beginTransaction();
        try {
            $usu_id_creador = $postData['usu_id'] ?? null;
            $cats_id = $postData['cats_id'] ?? null;
            $session_usu = $_SESSION['usu_id'] ?? null;
            $emp_id = $postData['emp_id'] ?? null;
            $dp_id = $postData['dp_id'] ?? null;
            $usu_asig = $postData['usu_asig'] ?? null;
            $usu_asig_final = null;

            $datos_creador = $this->usuarioModel->get_usuario_x_id($usu_id_creador);
            $ticket_reg_id = null;
            if (!empty($datos_creador['es_nacional']) && $datos_creador['es_nacional'] == 1) {
                $ticket_reg_id = $postData['reg_id'] ?? null;
            } else {
                $ticket_reg_id = $datos_creador['reg_id'] ?? null;
            }

            $errors = [];

            $flujo = $this->flujoModel->get_flujo_por_subcategoria($cats_id);

            $resolveResult = $this->resolveAssigned($flujo, $usu_id_creador, $ticket_reg_id);

            if (!empty($usu_asig)) {
                $usu_asig_final = $usu_asig;
            } else {
                $usu_asig_final = $resolveResult['usu_asig_final'];
            }

            $errors = array_merge($errors, $resolveResult['errors']);

            if (count($errors) > 0) {
                return ["success" => false, "errors" => $errors];
            }

            $datos = $this->ticketRepository->insertTicket(
                $usu_id_creador,
                $postData['cat_id'],
                $cats_id,
                $postData['pd_id'],
                $postData['tick_titulo'],
                $postData['tick_descrip'],
                $postData['error_proceso'],
                $usu_asig_final,
                $session_usu,
                $emp_id,
                $dp_id,
                $resolveResult['paso_actual_id_final'],
                $ticket_reg_id
            );

            $this->assignmentRepository->insertAssignment($datos, $resolveResult['usu_asig_final'], $session_usu, $resolveResult['paso_actual_id_final']);

            if ($resolveResult['usu_asig_final'] && $resolveResult['usu_asig_final'] != $usu_id_creador) {
                $mensaje_notificacion = "Se le ha asignado el ticket # {$datos}.";
                $this->notificationRepository->insertNotification($resolveResult['usu_asig_final'], $mensaje_notificacion, $datos);
            }

            $output = $this->insertDocument($datos);

            $this->pdo->commit();

            return ["success" => true, "ticket" => $datos, "tick_id" => $output['tick_id'] ?? null];
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ["success" => false, "errors" => ["Exception: " . $e->getMessage()]] ;
        }
    }

    public function showTicket($tickId)
    {
        $datos = $this->ticketModel->listar_ticket_x_id($tickId);
        if (is_array($datos) == true and count($datos) > 0) {
            $row = $datos;
            $output['tick_id'] = $row['tick_id'];
            $output['usu_id'] = $row['usu_id'];
            $output['cat_id'] = $row['cat_id'];
            $output['cats_id'] = $row['cats_id'];
            $output['pd_id'] = $row['pd_id'];
            $output['usu_asig'] = $row['usu_asig'];
            $output['tick_titulo'] = $row['tick_titulo'];
            $output['tick_descrip'] = $row['tick_descrip'];
            $output['tick_estado_texto'] = $row['tick_estado'];

            if ($row['tick_estado'] == 'Abierto') {
                $output['tick_estado'] = '<span class="label label-success">Abierto</span>';
            } else {
                $output['tick_estado'] = '<span class="label label-danger">Cerrado</span>';
            }
            $output['fech_crea'] = date("d/m/Y", strtotime($row["fech_crea"]));
            $output['usu_nom'] = $row['usu_nom'];
            $output['usu_ape'] = $row['usu_ape'];
            $output['cat_nom'] = $row['cat_nom'];
            $output['cats_nom'] = $row['cats_nom'];
            $output['emp_nom'] = $row['emp_nom'];
            $output['dp_nom'] = $row['dp_nom'];
            $output['paso_actual_id'] = $row['paso_actual_id'];
            $output['paso_nombre'] = $row['paso_nombre'];

            if ($row['pd_nom'] == 'Baja') {
                $output['prioridad_usuario'] = '<span class="label label-default">Baja</span>';
            } elseif ($row['pd_nom'] == 'Media') {
                $output['pd_nom'] = '<span class="label label-warning">Media</span>';
            } else {
                $output['pd_nom'] = '<span class="label label-danger">Alta</span>';
            }

            $output["decisiones_disponibles"] = [];
            $output["siguientes_pasos_lineales"] = [];
            $output["paso_actual_info"] = []; // Valor por defecto

            if (!empty($datos["paso_actual_id"])) {
                // 1. ¿Existen transiciones (decisiones) para este paso?
                $transiciones = $this->flujoTransicionModel->get_transiciones_por_paso($datos["paso_actual_id"]);

                if (count($transiciones) > 0) {
                    // Si hay decisiones, estas son las acciones principales para el usuario.
                    $output["decisiones_disponibles"] = $transiciones;
                } else {
                    // Si NO hay decisiones, buscamos el siguiente paso lineal.
                    $siguientes_pasos = $this->flujoPasoModel->get_siguientes_pasos($datos["paso_actual_id"]);
                    if ($siguientes_pasos) {
                        $output["siguientes_pasos_lineales"] = $siguientes_pasos;
                    }
                    $paso_actual_info = $this->flujoPasoModel->get_paso_actual($row["paso_actual_id"]);
                    if ($paso_actual_info) {
                        $output["paso_actual_info"] = $paso_actual_info;
                    }
                }
            }



            $output["timeline_graph"] = ""; // Creamos una nueva variable para el string del diagrama
            if (!empty($row["paso_actual_id"])) {
                $flujo_id = $this->flujoPasoModel->get_flujo_id_from_paso($row["paso_actual_id"]);
                if ($flujo_id) {
                    $todos_los_pasos = $this->flujoPasoModel->get_pasos_por_flujo($flujo_id);
                    $paso_actual_info = $this->flujoPasoModel->get_paso_por_id($row["paso_actual_id"]);
                    $orden_actual = $paso_actual_info['paso_orden'];

                    // --- INICIA NUEVA LÓGICA PARA GENERAR EL "GUION" DE MERMAID ---
                    $mermaid_string = "graph TD;\n"; // TD = Top Down
                    $pasos_por_orden = [];
                    foreach ($todos_los_pasos as $paso) {
                        $pasos_por_orden[$paso['paso_orden']][] = $paso;
                    }

                    // Definimos los nodos y sus estilos
                    foreach ($todos_los_pasos as $paso) {
                        $estado = '';
                        if ($paso['paso_orden'] < $orden_actual) $estado = 'completed';
                        elseif ($paso['paso_orden'] == $orden_actual && $paso['paso_id'] == $row["paso_actual_id"]) $estado = 'active';
                        else $estado = 'pending';

                        // Sintaxis de Mermaid para un nodo: ID_NODO["Texto del Nodo"]
                        $mermaid_string .= "    paso{$paso['paso_id']}_[\"{$paso['paso_nombre']}\"];\n";
                        // Sintaxis para aplicar un estilo
                        $mermaid_string .= "    class paso{$paso['paso_id']}_ {$estado};\n";
                    }

                    // Definimos las conexiones (flechas)
                    ksort($pasos_por_orden); // Ordenamos los niveles
                    $ordenes = array_keys($pasos_por_orden);
                    for ($i = 0; $i < count($ordenes) - 1; $i++) {
                        $nivel_actual = $pasos_por_orden[$ordenes[$i]];
                        $siguiente_nivel = $pasos_por_orden[$ordenes[$i + 1]];
                        foreach ($nivel_actual as $paso_padre) {
                            foreach ($siguiente_nivel as $paso_hijo) {
                                // Sintaxis de conexión: ID_PADRE --> ID_HIJO
                                $mermaid_string .= "    paso{$paso_padre['paso_id']}_ --> paso{$paso_hijo['paso_id']}_;\n";
                            }
                        }
                    }

                    // Definimos los estilos CSS para los estados
                    $mermaid_string .= "classDef completed fill:#dff0d8,stroke:#3c763d,stroke-width:2px;\n";
                    $mermaid_string .= "classDef active fill:#d9edf7,stroke:#31708f,stroke-width:4px;\n";
                    $mermaid_string .= "classDef pending fill:#f5f5f5,stroke:#ccc,stroke-width:2px;\n";
                    // --- FIN DE LÓGICA ---

                    $output["timeline_graph"] = $mermaid_string;
                }
            }

            $mi_ticket = $datos;
            $estado_tiempo = '<span class="label label-defa">N/A</span>';

            if ($mi_ticket['tick_estado'] == 'Abierto' && !empty($mi_ticket['paso_actual_id'])) {
                $fecha_asignacion = $this->ticketModel->get_fecha_ultima_asignacion($mi_ticket['tick_id']);
                $paso_info = $this->flujoPasoModel->get_paso_por_id($mi_ticket['paso_actual_id']);
                $dias_habiles_permitidos = $paso_info['paso_tiempo_habil'];

                if ($fecha_asignacion && $dias_habiles_permitidos > 0) {
                    $fecha_limite = $this->dateHelper->calcularFechaLimiteHabil($fecha_asignacion, $dias_habiles_permitidos);
                    $fecha_hoy = new DateTime();

                    if ($fecha_hoy > $fecha_limite) {
                        $estado_tiempo = '<span class="label label-danger">Atrasado</span>';
                    } else {
                        $estado_tiempo = '<span class="label label-success">A Tiempo</span>';
                    }
                }
            }
            $output['estado_tiempo'] = $estado_tiempo;
            echo json_encode($output);
        }
    }

    public function createDetailTicket($tickId, $usuId, $tickdDescrip, $condicion_nombre = null)
    {
        // 1. Guardar comentario y archivos
        $datos = $this->ticketModel->insert_ticket_detalle($tickId, $usuId, $tickdDescrip);
        if (is_array($datos) && count($datos) > 0) {
            $tickd_id = $datos[0]['tickd_id'];
            if (!empty($_FILES['files']['name'][0])) {
                $countfiles = count($_FILES['files']['name']);
                $ruta = '../public/document/detalle/' . $tickd_id . '/';
                if (!file_exists($ruta)) {
                    mkdir($ruta, 0777, true);
                }
                for ($index = 0; $index < $countfiles; $index++) {
                    $doc1 = $_FILES['files']['name'][$index];
                    $tmp_name = $_FILES['files']['tmp_name'][$index];
                    $destino = $ruta . $doc1;
                    $this->documentoModel->insert_documento_detalle($tickd_id, $doc1);
                    move_uploaded_file($tmp_name, $destino);
                }
            }
        }

        $this->pdo->beginTransaction();
        try {
            $ticket = $this->ticketModel->listar_ticket_x_id($tickId);
            
            // Recogemos la decisión que el usuario tomó en el frontend
            $decision_tomada = $_POST['decision_nombre'] ?? null;
            $avanzar_lineal = isset($_POST['avanzar_lineal']) && $_POST['avanzar_lineal'] === 'true';

            $reassigned = false; // Por defecto, no se reasigna

            // SOLO avanzamos si se dio una instrucción para ello
            if ($decision_tomada || $avanzar_lineal) {
                
                if ($ticket["ruta_id"]) {
                    // CASO 1: El ticket YA ESTÁ en una ruta. Simplemente avanzamos.
                    $this->avanzar_ticket_en_ruta($ticket);
                } elseif ($decision_tomada) {
                    // CASO 2: El usuario eligió una decisión específica.
                    $this->iniciar_ruta_desde_decision($ticket, $decision_tomada);
                } elseif ($avanzar_lineal) {
                    // CASO 3: El usuario quiere avanzar en un flujo sin decisiones.
                    $this->avanzar_ticket_lineal($ticket);
                }
                $reassigned = true; // Si entramos en este bloque, significa que se avanzó/reasignó.
            }
            $this->pdo->commit();
            echo json_encode(["status" => "success", "reassigned" => $reassigned]);

        } catch (Exception $e) {
            $this->pdo->rollBack();
            // Es importante devolver el mensaje de error para depurar
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }

    }

    private function iniciar_ruta_desde_decision($ticket, $decision_tomada)
    {
        // Buscamos la transición que corresponde al paso actual y la decisión tomada
        $transicion = $this->flujoTransicionModel->get_transicion_por_decision($ticket['paso_actual_id'], $decision_tomada);

        if ($transicion && !empty($transicion['ruta_id'])) {
            // Si encontramos la transición, obtenemos la ruta_id y la iniciamos
            $this->iniciar_ruta_para_ticket($ticket, $transicion['ruta_id']);
        } else {
            // Si no se encuentra una transición válida, lanzamos un error.
            throw new Exception("La decisión '{$decision_tomada}' no es una transición válida para el paso actual.");
        }
    }

    public function avanzar_ticket_en_ruta($ticket)
    {
        $ruta_paso_orden_actual = $ticket["ruta_paso_orden"];
        $siguiente_orden = $ruta_paso_orden_actual + 1;
        $siguiente_paso_info = $this->rutaPasoModel->get_paso_por_orden($ticket["ruta_id"], $siguiente_orden);

        if ($siguiente_paso_info) {
            $this->actualizar_estado_ticket($ticket['tick_id'], $siguiente_paso_info["paso_id"], $ticket["ruta_id"], $siguiente_orden);
        } else {
            $this->cerrar_ticket($ticket['tick_id'], "Ruta completada.");
        }
    }

    public function iniciar_ruta_para_ticket($ticket, $ruta_id)
    {
        $primer_paso_info = $this->rutaPasoModel->get_paso_por_orden($ruta_id, 1);
        if ($primer_paso_info) {
            $this->actualizar_estado_ticket($ticket['tick_id'], $primer_paso_info["paso_id"], $ruta_id, 1);
        } else {
            throw new Exception("La ruta seleccionada (ID: $ruta_id) no tiene un primer paso definido.");
        }
    }

    public function avanzar_ticket_lineal($ticket)
    {
        $siguiente_paso_info = $this->flujoModel->get_siguiente_paso($ticket["paso_actual_id"]);
        if ($siguiente_paso_info) {
            $this->actualizar_estado_ticket($ticket['tick_id'], $siguiente_paso_info["paso_id"], null, null);
        } else {
            $this->cerrar_ticket($ticket['tick_id'], "Flujo principal completado.");
        }
    }

    public function actualizar_estado_ticket($ticket_id, $nuevo_paso_id, $ruta_id, $ruta_paso_orden)
    {
        $siguiente_paso = $this->flujoPasoModel->get_paso_por_id($nuevo_paso_id);
        if (!$siguiente_paso) {
            throw new Exception("No se encontró la información del siguiente paso (ID: $nuevo_paso_id).");
        }

        $siguiente_cargo_id = $siguiente_paso['cargo_id_asignado'] ?? null;
        $nuevo_asignado_info = null;

        if ($siguiente_paso['es_tarea_nacional'] == 1) {
            $nuevo_asignado_info = $this->usuarioModel->get_usuario_nacional_por_cargo($siguiente_cargo_id);
        } else {
            $regional_origen_id = $this->ticketModel->get_ticket_region($ticket_id);
            $nuevo_asignado_info = $this->usuarioModel->get_usuario_por_cargo_y_regional($siguiente_cargo_id, $regional_origen_id);
        }

        if ($nuevo_asignado_info) {
            $nuevo_usuario_asignado = $nuevo_asignado_info['usu_id'];
            $this->ticketRepository->updateTicketFlowState($ticket_id, $nuevo_usuario_asignado, $nuevo_paso_id, $ruta_id, $ruta_paso_orden);
            
            // Añadir al historial y enviar notificaciones...
            $this->assignmentRepository->insertAssignment($ticket_id, $nuevo_usuario_asignado, $_SESSION['usu_id'], $nuevo_paso_id);
            $mensaje_notificacion = "Se le ha asignado el ticket #{$ticket_id}.";
            $this->notificationRepository->insertNotification($nuevo_usuario_asignado, $mensaje_notificacion, $ticket_id);

        } else {
            throw new Exception("No se encontró un usuario para asignar al cargo ID: $siguiente_cargo_id.");
        }
    }

    private function cerrar_ticket($ticket_id, $mensaje)
    {
        $this->ticketModel->update_ticket($ticket_id);
        $this->ticketModel->insert_ticket_detalle_cerrar($ticket_id, $_SESSION['usu_id']);
    }

    public function updateTicket($tickId)
    {
        // a. Obtenemos la información de la última asignación (el paso que está por terminar)
        $asignacion_actual = $this->ticketModel->get_ultima_asignacion($tickId);

        // Solo ejecutamos la lógica de tiempo si el ticket estaba en un flujo
        if ($asignacion_actual && !empty($asignacion_actual['paso_actual_id'])) {
            $paso_actual_info = $this->flujoPasoModel->get_paso_por_id($asignacion_actual['paso_actual_id']);

            if ($paso_actual_info) {
                $estado_paso_final = 'N/A';
                $fecha_asignacion = $asignacion_actual['fech_asig'];
                $dias_habiles = $paso_actual_info['paso_tiempo_habil'];

                if ($dias_habiles > 0) {
                    // b. Calculamos si se completó a tiempo
                    $fecha_limite = $this->dateHelper->calcularFechaLimiteHabil($fecha_asignacion, $dias_habiles);
                    $fecha_hoy = new DateTime();
                    $estado_paso_final = ($fecha_hoy > $fecha_limite) ? 'Atrasado' : 'A Tiempo';
                }

                // c. Actualizamos el historial de ESE ÚLTIMO PASO con su estado final
                $this->ticketModel->update_estado_tiempo_paso($asignacion_actual['th_id'], $estado_paso_final);
            }
        }
        // --- FINALIZA LÓGICA DE MEDICIÓN ---


        // --- TU LÓGICA ORIGINAL PARA CERRAR EL TICKET (AHORA VA DESPUÉS) ---
        $this->ticketModel->update_ticket($_POST['tick_id']);
        $this->ticketModel->insert_ticket_detalle_cerrar($_POST['tick_id'], $_POST['usu_id']);
    }

    public function LogErrorTicket($dataPost)
    {
        try {
            $tick_id = $dataPost['tick_id'] ?? null;
            $answer_id = $dataPost['answer_id'] ?? null;
            $usu_id = $dataPost['usu_id'] ?? null; // analista que reporta
            $error_descrip = $dataPost['error_descrip'] ?? '';

            if (!$tick_id || !$answer_id || !$usu_id) {
                echo json_encode(["status" => "error", "msg" => "Parámetros incompletos"]);
                return;
            }

            require_once('../models/RespuestaRapida.php');
            $respuesta_rapida = new RespuestaRapida();
            $datos_respuesta = $respuesta_rapida->get_respuestarapida_x_id($answer_id);

            if (!$datos_respuesta) {
                echo json_encode(["status" => "error", "msg" => "Respuesta rápida no encontrada"]);
                return;
            }

            $nombre_respuesta = $datos_respuesta["answer_nom"] ?? 'Respuesta desconocida';
            $es_error_proceso = !empty($datos_respuesta["es_error_proceso"]);

            // Traer ticket (útil para datos del creador)
            $ticket_data = $this->ticketModel->listar_ticket_x_id($tick_id);
            if (!$ticket_data) {
                echo json_encode(["status" => "error", "msg" => "Ticket no encontrado"]);
                return;
            }

            // Buscar asignaciones
            $asignacion_anterior = $this->ticketModel->get_penultima_asignacion($tick_id);
            $primera_asignacion = $this->ticketModel->get_primera_asignacion($tick_id);

            // Determinar a quién sellar (historial) y a quién devolver (si aplica)
            $id_historial_a_sellar = null;
            if ($asignacion_anterior && !empty($asignacion_anterior['th_id'])) {
                $id_historial_a_sellar = $asignacion_anterior['th_id'];
            } elseif ($primera_asignacion && !empty($primera_asignacion['th_id'])) {
                $id_historial_a_sellar = $primera_asignacion['th_id'];
            }

            // Sellar historial si aplica
            if ($id_historial_a_sellar) {
                $this->ticketModel->update_error_code_paso($id_historial_a_sellar, $answer_id, $error_descrip);
            }

            // Preparar comentario visible
            $nombre_completo_responsable = null;
            if ($asignacion_anterior && isset($asignacion_anterior['usu_nom'], $asignacion_anterior['usu_ape'])) {
                $nombre_completo_responsable = $asignacion_anterior['usu_nom'] . ' ' . $asignacion_anterior['usu_ape'];
            } elseif (!empty($ticket_data['usu_nom']) && !empty($ticket_data['usu_ape'])) {
                $nombre_completo_responsable = $ticket_data['usu_nom'] . ' ' . $ticket_data['usu_ape'];
            }

            $comentario = "Se registró un evento: <b>{$nombre_respuesta}</b>.";
            if (!empty($error_descrip)) $comentario .= "<br><b>Descripción:</b> " . htmlspecialchars($error_descrip);
            if ($nombre_completo_responsable) $comentario .= "<br><small class='text-muted'>Error atribuido a: <b>{$nombre_completo_responsable}</b></small>";

            // Marcar ticket con el código de error (bandera visual)
            $this->ticketModel->update_error_proceso($tick_id, $answer_id);

            // Insertar detalle del ticket
            $this->ticketModel->insert_ticket_detalle($tick_id, $usu_id, $comentario);

            // Si es error de proceso, reasignar: preferir usuario anterior; si no existe -> creador
            $assigned_to = null;
            $assigned_paso = null;
            $updateResult = null;

            if ($es_error_proceso) {
                if ($asignacion_anterior && !empty($asignacion_anterior['usu_asig'])) {
                    // devolver al usuario anterior
                    $assigned_to = $asignacion_anterior['usu_asig'];
                    $assigned_paso = $asignacion_anterior['paso_id'] ?? null;
                } else {
                    // no hay anterior: devolver al creador del ticket
                    $assigned_to = $ticket_data['usu_id'];
                    // si existe paso en la primera asignacion podemos usarlo
                    $assigned_paso = $primera_asignacion['paso_id'] ?? null;
                }

                // Ejecutar la reasignación en BD (si existe función)
                if ($assigned_to !== null) {
                    $comentario_reasignacion = "Ticket devuelto por error de proceso.";
                    $mensaje_notificacion = "Se te ha devuelto el Ticket #{$tick_id} por un error en el proceso.";
                    $updateResult = $this->ticketModel->update_asignacion_y_paso(
                        $tick_id,
                        $assigned_to,
                        $assigned_paso,
                        $usu_id, // quien reasigna (analista que reporta)
                        $comentario_reasignacion,
                        $mensaje_notificacion
                    );
                }
            }

            // Traer ticket actualizado y devolver información
            $ticket_data_actualizado = $this->ticketModel->listar_ticket_x_id($tick_id);

            $response = [
                "status" => "success",
                "ticket" => $ticket_data_actualizado,
                "assigned_to" => $assigned_to,
                "assigned_paso" => $assigned_paso,
                "update_asign_result" => $updateResult
            ];

            echo json_encode($response);
            return;
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "msg" => "Exception: " . $e->getMessage()]);
            return;
        }
    }



}
