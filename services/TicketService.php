<?php

require_once('../models/Ticket.php');
require_once('../models/Usuario.php');
require_once('../models/Subcategoria.php');
require_once('../models/Documento.php');
require_once('../models/Flujo.php');
require_once('../models/FlujoPaso.php');
require_once('../models/Departamento.php');
require_once('../models/DateHelper.php');
require_once('../models/FlujoTransicion.php');
require_once('../models/Ruta.php');
require_once('../models/RutaPaso.php');
require_once('../services/TicketWorkflowService.php');
require_once('../models/repository/NovedadRepository.php');

use models\repository\TicketRepository;
use models\repository\NotificationRepository;
use models\repository\AssignmentRepository;
use models\repository\NovedadRepository;
use PDO;
use Exception;


class TicketService
{
    private $ticketModel;
    private $subcategoriaModel;
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
    private NovedadRepository $novedadRepository;
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
        $this->subcategoriaModel = new Subcategoria();


        $this->pdo = $pdo;
        $this->ticketRepository = new TicketRepository($pdo);
        $this->notificationRepository = new NotificationRepository($pdo);
        $this->assignmentRepository = new AssignmentRepository($pdo);
        $this->novedadRepository = new NovedadRepository($pdo);
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

            $cats_nom = $this->subcategoriaModel->get_nombre_subcategoria($cats_id);

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

            $this->assignmentRepository->insertAssignment($datos, $resolveResult['usu_asig_final'], $session_usu, $resolveResult['paso_actual_id_final'], ' Ticket creado');

            if ($resolveResult['usu_asig_final'] && $resolveResult['usu_asig_final'] != $usu_id_creador) {
                $mensaje_notificacion = "Se le ha asignado el ticket # {$datos} - {$cats_nom}.";
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
            } elseif ($row['tick_estado'] == 'Pausado') {
                $output['tick_estado'] = '<span class="label label-info">Pausado</span>';
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
            $output['novedad_abierta'] = $this->novedadRepository->getNovedadAbiertaPorTicket($tickId);

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
                $paso_actual_info = $this->flujoPasoModel->get_paso_actual($row["paso_actual_id"]);
                if ($paso_actual_info) {
                    $output["paso_actual_info"] = $paso_actual_info;
                }
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
                    if ($siguientes_pasos && $siguientes_pasos[0]['requiere_seleccion_manual'] == 1) {
                    $output['requiere_seleccion_manual'] = true;
                    $usuarios_especificos = $this->flujoPasoModel->get_usuarios_especificos($siguientes_pasos[0]['paso_id']);
                    if (count($usuarios_especificos) > 0) {
                        $output['usuarios_seleccionables'] = $this->usuarioModel->get_usuarios_por_ids($usuarios_especificos);
                    } else {
                        $output['usuarios_seleccionables'] = $this->usuarioModel->get_usuarios_por_cargo($paso_actual_info['cargo_id_asignado']);
                    }
                }
                }
            }

            $output["timeline_graph"] = "";
            if (!empty($row["paso_actual_id"])) {
                $flujo_id = $this->flujoPasoModel->get_flujo_id_from_paso($row["paso_actual_id"]);
                if ($flujo_id) {
                    $todos_los_pasos_flujo = $this->flujoPasoModel->get_pasos_por_flujo($flujo_id);
                    $paso_actual_info = $this->flujoPasoModel->get_paso_por_id($row["paso_actual_id"]);
                    $orden_actual_flujo = $paso_actual_info['paso_orden'] ?? 0;
                    $ruta_actual_id = !empty($row['ruta_id']) ? $row['ruta_id'] : null;

                    $mermaid_string = "graph TD;\n";
                    $declared_nodes = [];
                    $connections = "";

                    // 1. Declarar todos los nodos del flujo principal y de las rutas
                    foreach ($todos_los_pasos_flujo as $paso) {
                        $paso_id_unico = "flujo_{$paso['paso_id']}";
                        if (!in_array($paso_id_unico, $declared_nodes)) {
                            $mermaid_string .= "    {$paso_id_unico}[\"{$paso['paso_nombre']}\"];\n";
                            $declared_nodes[] = $paso_id_unico;
                        }

                        $transiciones = $this->flujoTransicionModel->get_transiciones_por_paso($paso["paso_id"]);
                        if (count($transiciones) > 0) {
                            foreach ($transiciones as $transicion) {
                                if (!empty($transicion['ruta_id'])) {
                                    $pasos_de_la_ruta = $this->rutaPasoModel->get_pasos_por_ruta($transicion['ruta_id']);
                                    if ($pasos_de_la_ruta) {
                                        foreach ($pasos_de_la_ruta as $paso_ruta) {
                                            $paso_ruta_id_unico = "ruta{$transicion['ruta_id']}_paso{$paso_ruta['paso_id']}";
                                            if (!in_array($paso_ruta_id_unico, $declared_nodes)) {
                                                $mermaid_string .= "    {$paso_ruta_id_unico}[\"{$paso_ruta['paso_nombre']}\"];\n";
                                                $declared_nodes[] = $paso_ruta_id_unico;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    // 2. Aplicar estilos, crear subgrafos y generar las conexiones
                    $rutas_procesadas = [];
                    for ($i = 0; $i < count($todos_los_pasos_flujo); $i++) {
                        $paso = $todos_los_pasos_flujo[$i];
                        $paso_id_unico = "flujo_{$paso['paso_id']}";
                        
                        // Aplicar estilo al paso del flujo principal
                        $estado = 'pending';
                        if ($ruta_actual_id) {
                            if ($paso['paso_orden'] <= $orden_actual_flujo) $estado = 'completed';
                        } else {
                            if ($paso['paso_orden'] < $orden_actual_flujo) $estado = 'completed';
                            if ($paso['paso_id'] == $row["paso_actual_id"]) $estado = 'active';
                        }
                        $mermaid_string .= "    class {$paso_id_unico} {$estado};\n";

                        // Generar Conexiones y Subgrafos
                        $transiciones = $this->flujoTransicionModel->get_transiciones_por_paso($paso["paso_id"]);
                        $has_valid_branches = false;

                        if (count($transiciones) > 0) {
                            foreach ($transiciones as $transicion) {
                                if (!empty($transicion['ruta_id'])) {
                                    $ruta_id = $transicion['ruta_id'];
                                    $ruta_info = $this->rutaModel->get_ruta_por_id($ruta_id);
                                    $pasos_de_la_ruta = $this->rutaPasoModel->get_pasos_por_ruta($ruta_id);

                                    if ($ruta_info && $pasos_de_la_ruta) {
                                        $has_valid_branches = true;
                                        
                                        // Conectar el flujo principal al inicio de la ruta
                                        $inicio_ruta = "ruta{$ruta_id}_paso{$pasos_de_la_ruta[0]['paso_id']}";
                                        $connections .= "    {$paso_id_unico} -- \"{$transicion['decision_nombre']}\" --> {$inicio_ruta};\n";
                                        
                                        if (!in_array($ruta_id, $rutas_procesadas)) {
                                            $mermaid_string .= "    subgraph " . $ruta_info['ruta_nombre'] . "\n";
                                            foreach ($pasos_de_la_ruta as $paso_ruta) {
                                                $paso_ruta_id_unico = "ruta{$ruta_id}_paso{$paso_ruta['paso_id']}";
                                                $mermaid_string .= "        {$paso_ruta_id_unico}\n";
                                                
                                                $estado_ruta = 'pending';
                                                if ($ruta_actual_id == $ruta_id && $paso_ruta['paso_id'] == $row["paso_actual_id"]) {
                                                    $estado_ruta = 'active';
                                                }
                                                $mermaid_string .= "        class {$paso_ruta_id_unico} {$estado_ruta};\n";
                                            }
                                            $mermaid_string .= "    end\n";
                                            
                                            // Conexiones internas de la ruta
                                            for ($j = 0; $j < count($pasos_de_la_ruta) - 1; $j++) {
                                                $origen = "ruta{$ruta_id}_paso{$pasos_de_la_ruta[$j]['paso_id']}";
                                                $destino = "ruta{$ruta_id}_paso{$pasos_de_la_ruta[$j+1]['paso_id']}";
                                                $connections .= "        {$origen} --> {$destino};\n";
                                            }
                                            $rutas_procesadas[] = $ruta_id;
                                        }
                                    }
                                }
                            }
                        }

                        // Si no tiene ramas válidas, es una conexión lineal
                        if (!$has_valid_branches && isset($todos_los_pasos_flujo[$i + 1])) {
                            $siguiente_paso_id = $todos_los_pasos_flujo[$i + 1]['paso_id'];
                            $destino_id = "flujo_{$siguiente_paso_id}";
                            $connections .= "    {$paso_id_unico} --> {$destino_id};\n";
                        }
                    }
                    
                    $mermaid_string .= $connections;

                    // 3. Definir los estilos
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

    public function createDetailTicket($postData)
    {
        
        $tickId = $postData['tick_id'] ;
        $usuId = $postData['usu_id'] ;
        $tickdDescrip = $postData['tickd_descrip'];
        $usu_asig = $postData['usu_asig'] ?? null;

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
                
                if (!empty($ticket["ruta_id"])) {
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
            }else{
                $this->avanzar_ticket_con_usuario_asignado($ticket, $usu_asig);
                $reassigned = true;
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
            $this->actualizar_estado_ticket($ticket['tick_id'], $siguiente_paso_info["paso_id"], $ticket["ruta_id"], $siguiente_orden, null);
        } else {
            $this->cerrar_ticket($ticket['tick_id'], "Ruta completada.");
        }
    }
    
    public function iniciar_ruta_para_ticket($ticket, $ruta_id)
    {
        $primer_paso_info = $this->rutaPasoModel->get_paso_por_orden($ruta_id, 1);
        if ($primer_paso_info) {
            $this->actualizar_estado_ticket($ticket['tick_id'], $primer_paso_info["paso_id"], $ruta_id, 1, null);
        } else {
            throw new Exception("La ruta seleccionada (ID: $ruta_id) no tiene un primer paso definido.");
        }
    }
    
    public function avanzar_ticket_con_usuario_asignado($ticket, $usu_asig)
    {
        $siguiente_paso_info = $this->flujoModel->get_siguiente_paso($ticket["paso_actual_id"]);
        if ($siguiente_paso_info) {
            $this->actualizar_estado_ticket($ticket['tick_id'], $siguiente_paso_info["paso_id"], null, null, $usu_asig);
        } else {
            $this->cerrar_ticket($ticket['tick_id'], "Flujo principal completado.");
        }
    }
    
    public function avanzar_ticket_lineal($ticket)
    {
        $siguiente_paso_info = $this->flujoModel->get_siguiente_paso($ticket["paso_actual_id"]);
        if ($siguiente_paso_info) {
            $this->actualizar_estado_ticket($ticket['tick_id'], $siguiente_paso_info["paso_id"], null, null, null);
        } else {
            $this->cerrar_ticket($ticket['tick_id'], "Flujo principal completado.");
        }
    }

    public function actualizar_estado_ticket($ticket_id, $nuevo_paso_id, $ruta_id, $ruta_paso_orden, $usu_asig)
    {

        $cats_nom = $this->ticketModel->listar_ticket_x_id($ticket_id)['cats_nom'];

        $siguiente_paso = $this->flujoPasoModel->get_paso_por_id($nuevo_paso_id);
        if (!$siguiente_paso) {
            throw new Exception("No se encontró la información del siguiente paso (ID: $nuevo_paso_id).");
        }

        $siguiente_cargo_id = $siguiente_paso['cargo_id_asignado'] ?? null;
        $nuevo_asignado_info = null;

        if (!empty($usu_asig) || $usu_asig != null) {

            $nuevo_asignado_info = $usu_asig;
        
        } else{
            
            if ($siguiente_paso['es_tarea_nacional'] == 1) {
                $nuevo_asignado_info = $this->usuarioModel->get_usuario_nacional_por_cargo($siguiente_cargo_id);
            } else {
                $regional_origen_id = $this->ticketModel->get_ticket_region($ticket_id);
                $nuevo_asignado_info = $this->usuarioModel->get_usuario_por_cargo_y_regional($siguiente_cargo_id, $regional_origen_id);
            }
        
        }
        if ($nuevo_asignado_info) {
            if (!empty($usu_asig) || $usu_asig != null) {
                $nuevo_usuario_asignado = $usu_asig;
            }else{
                $nuevo_usuario_asignado = $nuevo_asignado_info['usu_id'];
            }
            $this->ticketRepository->updateTicketFlowState($ticket_id, $nuevo_usuario_asignado, $nuevo_paso_id, $ruta_id, $ruta_paso_orden);
            
            // Añadir al historial y enviar notificaciones...
            $th_id = $this->assignmentRepository->insertAssignment($ticket_id, $nuevo_usuario_asignado, $_SESSION['usu_id'], $nuevo_paso_id, 'Ticket trasladado');

            // Actualizar el estado de tiempo para la última asignación
            $this->computeAndUpdateEstadoPaso($ticket_id);            
            $mensaje_notificacion = "Se le ha trasladado el ticket #{$ticket_id} - {$cats_nom}.";
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
        $tick_id = $_POST['tick_id'];
        $usu_id = $_POST['usu_id'];

        $ticket = $this->ticketModel->listar_ticket_x_id($tick_id);

        // Si el ticket tiene un flujo asignado, verificamos que esté en el último paso
        if ($ticket && !empty($ticket['paso_actual_id'])) {
            $is_last_step = false;

            // Caso 1: El ticket está en una ruta específica
            if (!empty($ticket['ruta_id'])) {
                $siguiente_orden = (int)$ticket["ruta_paso_orden"] + 1;
                $siguiente_paso_info = $this->rutaPasoModel->get_paso_por_orden($ticket["ruta_id"], $siguiente_orden);
                if (!$siguiente_paso_info) {
                    $is_last_step = true;
                }
            } 
            // Caso 2: El ticket está en el flujo principal
            else {
                $siguientes_pasos = $this->flujoPasoModel->get_siguientes_pasos($ticket["paso_actual_id"]);
                $transiciones = $this->flujoTransicionModel->get_transiciones_por_paso($ticket["paso_actual_id"]);
                
                // Un paso es final si no tiene pasos lineales siguientes NI transiciones a otras rutas
                if (empty($siguientes_pasos) && empty($transiciones)) {
                    $is_last_step = true;
                }
            }

            // Si después de las validaciones, no es el último paso, devolvemos un error
            if (!$is_last_step) {
                header('Content-Type: application/json');
                echo json_encode(["status" => "error", "message" => "El ticket no se puede cerrar porque no ha llegado al final de su flujo."]);
                return;
            }
        }

        // --- Si es el último paso o no tiene flujo, procede a cerrar ---

        $asignacion_actual = $this->ticketModel->get_ultima_asignacion($tick_id);

        if ($asignacion_actual && !empty($asignacion_actual['paso_actual_id'])) {
            $paso_actual_info = $this->flujoPasoModel->get_paso_por_id($asignacion_actual['paso_actual_id']);

            if ($paso_actual_info) {
                $estado_paso_final = 'N/A';
                $fecha_asignacion = $asignacion_actual['fech_asig'];
                $dias_habiles = $paso_actual_info['paso_tiempo_habil'];

                if ($dias_habiles > 0) {
                    $fecha_limite = $this->dateHelper->calcularFechaLimiteHabil($fecha_asignacion, $dias_habiles);
                    $fecha_hoy = new DateTime();
                    $estado_paso_final = ($fecha_hoy > $fecha_limite) ? 'Atrasado' : 'A Tiempo';
                }

                $this->ticketModel->update_estado_tiempo_paso($asignacion_actual['th_id'], $estado_paso_final);
            }
        }
        
        $this->ticketModel->update_ticket($tick_id);
        $this->ticketModel->insert_ticket_detalle_cerrar($tick_id, $usu_id);

        header('Content-Type: application/json');
        echo json_encode(["status" => "success", "message" => "Ticket cerrado correctamente."]);
    }

    /**
     * Registra un evento de error en un ticket.
     *
     * Este método maneja la lógica para cuando un analista registra un error en un ticket.
     * - Identifica si el error es un "error de proceso" que requiere que el ticket retroceda en el flujo.
     * - Determina el usuario responsable del error basándose en el paso anterior del flujo de trabajo definido.
     * - Sella el registro de historial de la asignación anterior con el código y la descripción del error.
     * - Añade un comentario público al historial del ticket detallando el error.
     * - Si es un error de proceso, reasigna el ticket al usuario responsable (ya sea el del paso anterior o el creador del ticket si estaba en el primer paso).
     *
     * @param array $dataPost Datos que normalmente provienen de $_POST. Debe contener:
     *                        - 'tick_id': ID del ticket.
     *                        - 'answer_id': ID de la respuesta rápida que define el error.
     *                        - 'usu_id': ID del usuario (analista) que está reportando el error.
     *                        - 'error_descrip' (opcional): Una descripción textual del error.
     * @throws Exception Si faltan parámetros o si no se encuentran datos esenciales (ticket, respuesta rápida, etc.).
     * @return void      Imprime una respuesta JSON con el estado de la operación y los datos actualizados del ticket.
     */
    public function LogErrorTicket($dataPost)
    {
        try {
            $this->pdo->beginTransaction();
    
            $tick_id = $dataPost['tick_id'] ?? null;
            $answer_id = $dataPost['answer_id'] ?? null;
            $usu_id_reporta = $dataPost['usu_id'] ?? null;
            $error_descrip = $dataPost['error_descrip'] ?? '';
    
            if (!$tick_id || !$answer_id || !$usu_id_reporta) {
                throw new Exception("Parámetros incompletos.");
            }
    
            require_once('../models/RespuestaRapida.php');
            $respuesta_rapida = new RespuestaRapida();
            $datos_respuesta = $respuesta_rapida->get_respuestarapida_x_id($answer_id);
            if (!$datos_respuesta) {
                throw new Exception("Respuesta rápida no encontrada.");
            }
            $nombre_respuesta = $datos_respuesta["answer_nom"] ?? 'Respuesta desconocida';
            $es_error_proceso = !empty($datos_respuesta["es_error_proceso"]);
    
            $ticket_data = $this->ticketModel->listar_ticket_x_id($tick_id);
            if (!$ticket_data) {
                throw new Exception("Ticket no encontrado.");
            }
    
            // Determinar a quién reasignar PRIMERO
            $assigned_to = null;
            $assigned_paso = null;
            $paso_actual_id = $ticket_data['paso_actual_id'];
            if ($es_error_proceso) {
                if ($paso_actual_id) {
                    $paso_anterior = $this->flujoPasoModel->get_paso_anterior($paso_actual_id);
                    if ($paso_anterior) {
                        $assigned_paso = $paso_anterior['paso_id'];
                        $assigned_to = $this->ticketModel->get_usuario_asignado_a_paso($tick_id, $assigned_paso);
                        if (!$assigned_to) {
                            throw new Exception("No se pudo determinar el usuario del paso anterior (Paso ID: $assigned_paso).");
                        }
                    } else {
                        $assigned_to = $ticket_data['usu_id']; // Creador del ticket
                        $assigned_paso = null;
                    }
                } else {
                    $assigned_to = $ticket_data['usu_id']; // Creador del ticket
                    $assigned_paso = null;
                }
            }
    
            // Obtener el nombre del responsable para el mensaje
            $nombre_completo_responsable = null;
            if ($es_error_proceso) {
                if ($assigned_to) {
                    $datos_responsable = $this->usuarioModel->get_usuario_x_id($assigned_to);
                    if ($datos_responsable) {
                        $nombre_completo_responsable = $datos_responsable['usu_nom'] . ' ' . $datos_responsable['usu_ape'];
                    }
                }
            } else {
                // Para errores que no son de proceso, el responsable es el asignado actual.
                if ($paso_actual_id) {
                    $paso_anterior = $this->flujoPasoModel->get_paso_anterior($paso_actual_id);
                    if ($paso_anterior) {
                        $assigned_paso = $paso_anterior['paso_id'];
                        $assigned_to = $this->ticketModel->get_usuario_asignado_a_paso($tick_id, $assigned_paso);
                        if (!$assigned_to) {
                            throw new Exception("No se pudo determinar el usuario del paso anterior (Paso ID: $assigned_paso).");
                        }
                    }else{
                        $assigned_to = $ticket_data['usu_id']; // Creador del ticket
                        $assigned_paso = null;
                    }   
                }else{
                    $assigned_to = $ticket_data['usu_id']; // Creador del ticket
                    $assigned_paso = null;
                }

                if ($assigned_to) {
                    $datos_responsable = $this->usuarioModel->get_usuario_x_id($assigned_to);
                    if ($datos_responsable) {
                        $nombre_completo_responsable = $datos_responsable['usu_nom'] . ' ' . $datos_responsable['usu_ape'];
                    }
                }
            }
    
            // Sellar el historial
            $asignacion_a_sellar = $this->ticketModel->get_ultima_asignacion($tick_id);
            if ($asignacion_a_sellar) {
                $this->ticketModel->update_error_code_paso($asignacion_a_sellar['th_id'], $answer_id, $error_descrip);
            }
    
            // Preparar y guardar comentario
            $comentario = "Se registró un evento: <b>{$nombre_respuesta}</b>.";
            if (!empty($error_descrip)) $comentario .= "<br><b>Descripción:</b> " . htmlspecialchars($error_descrip);
            if ($nombre_completo_responsable) $comentario .= "<br><small class='text-muted'>Error atribuido a: <b>{$nombre_completo_responsable}</b></small>";
            
            $this->ticketModel->update_error_proceso($tick_id, $answer_id);
            $this->ticketModel->insert_ticket_detalle($tick_id, $usu_id_reporta, $comentario);
    
            // Realizar la reasignación si es un error de proceso
            if ($es_error_proceso && $assigned_to) {
                if($assigned_to == $ticket_data['usu_id'] && $assigned_paso === null){
                    $this->ticketModel->update_ticket($tick_id);
                    $this->ticketModel->insert_ticket_detalle_cerrar($tick_id, $usu_id_reporta);
                    $this->assignmentRepository->insertAssignment($tick_id, $assigned_to, $usu_id_reporta, null, 'Ticket cerrado por error de proceso en el primer paso.');
                    $this->notificationRepository->insertNotification($assigned_to, "El Ticket #{$tick_id} ha sido cerrado debido a un error en el proceso.", $tick_id);
                }else{
                    $comentario_reasignacion = "Ticket devuelto por error de proceso.";
                    $mensaje_notificacion = "Se te ha devuelto el Ticket #{$tick_id} por un error en el proceso.";
                    $this->ticketModel->update_asignacion_y_paso(
                        $tick_id,
                        $assigned_to,
                        $assigned_paso,
                        $usu_id_reporta,
                        $comentario_reasignacion,
                        $mensaje_notificacion
                );
                }
            }
    
            $this->pdo->commit();
    
            $ticket_data_actualizado = $this->ticketModel->listar_ticket_x_id($tick_id);
            echo json_encode([
                "status" => "success",
                "ticket" => $ticket_data_actualizado,
                "assigned_to" => $assigned_to,
                "assigned_paso" => $assigned_paso
            ]);
    
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            echo json_encode(["status" => "error", "msg" => "Exception: " . $e->getMessage()]);
        }
    }

    public function approveStep($tickId)
    {
        $this->pdo->beginTransaction();
        try {
            $ticket = $this->ticketModel->listar_ticket_x_id($tickId);
            if (!$ticket) {
                throw new Exception("Ticket no encontrado.");
            }

            $usuId = $_SESSION['usu_id'];
            $this->ticketModel->insert_ticket_detalle($tickId, $usuId, "Aprobó el paso actual.");

            if (!empty($ticket["ruta_id"])) {
                // Si está en una ruta, avanza en la ruta.
                $this->avanzar_ticket_en_ruta($ticket);
            } else {
                // Si está en el flujo principal, avanza de forma lineal.
                $this->avanzar_ticket_lineal($ticket);
            }
            
            $this->pdo->commit();
            return ["status" => "success", "message" => "Paso aprobado. El ticket ha avanzado."];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            // Devolvemos el mensaje de la excepción para que el frontend lo muestre.
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function rejectStep($tickId)
    {
        $this->pdo->beginTransaction();
        try {
            $ticket = $this->ticketModel->listar_ticket_x_id($tickId);
            if (!$ticket) {
                throw new Exception("Ticket no encontrado.");
            }

            // Buscamos la penúltima asignación para saber a quién devolverle el ticket
            $asignacion_anterior = $this->ticketModel->get_penultima_asignacion($tickId);
            if (!$asignacion_anterior || empty($asignacion_anterior['usu_asig'])) {
                throw new Exception("No se encontró un paso anterior al cual devolver el ticket.");
            }

            $usuId_actual = $_SESSION['usu_id'];
            $usuId_devolver = $asignacion_anterior['usu_asig'];
            $pasoId_devolver = $asignacion_anterior['paso_id'];

            // Comentario para el historial
            $comentario_rechazo = "Se rechazó el paso actual. El ticket ha sido devuelto.";
            // Mensaje para la notificación
            $mensaje_notificacion = "Se te ha devuelto el Ticket #{$tickId} por un rechazo en el flujo.";

            // Usamos la función existente para reasignar
            $this->ticketModel->update_asignacion_y_paso(
                $tickId,
                $usuId_devolver,
                $pasoId_devolver,
                $usuId_actual,
                $comentario_rechazo,
                $mensaje_notificacion
            );

            $this->pdo->commit();
            return ["status" => "success", "message" => "Paso rechazado. El ticket ha sido devuelto."];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function computeAndUpdateEstadoPaso(int $ticketId): string
    {
        // 1) Obtener la última asignación (th_ticket_asignacion)
        $asignacion_actual = $this->ticketModel->get_ultima_asignacion($ticketId);
        if (!$asignacion_actual || empty($asignacion_actual['th_id'])) {
            return 'N/A';
        }

        $th_id = $asignacion_actual['th_id'];
        $fech_asig = $asignacion_actual['fech_asig'] ?? null;
        $paso_id = $asignacion_actual['paso_id'] ?? null;

        // 2) Obtener info del paso
        $estado = 'N/A';
        if ($paso_id) {
            $paso_info = $this->flujoPasoModel->get_paso_por_id($paso_id);
            $dias_habiles_permitidos = (int)($paso_info['paso_tiempo_habil'] ?? 0);

            if ($fech_asig && $dias_habiles_permitidos > 0) {
                // calcular fecha límite usando el DateHelper existente
                $fecha_limite = $this->dateHelper->calcularFechaLimiteHabil($fech_asig, $dias_habiles_permitidos);
                $fecha_hoy = new \DateTime();

                $estado = ($fecha_hoy > $fecha_limite) ? 'Atrasado' : 'A Tiempo';
            } else {
                // Si no hay tiempo habil definido, dejamos 'N/A'
                $estado = 'N/A';
            }
        }

        // 3) Actualizar en la tabla de historial (método ya usado en tu código)
        try {
            $this->ticketModel->update_estado_tiempo_paso($th_id, $estado);
        } catch (\Exception $e) {
            // No romper el flujo si la actualización falla — loguear si tienes logger
            // error_log("computeAndUpdateEstadoPaso error: " . $e->getMessage());
        }

        return $estado;
    }

    public function crearNovedad(array $data): array
    {
        $this->pdo->beginTransaction();
        try {
            $tick_id = (int)$data['tick_id'];
            $usu_crea_novedad = (int)$data['usu_id'];
            $usu_asig_novedad = (int)$data['usu_asig_novedad'];
            $descripcion_novedad = $data['descripcion_novedad'];

            $ticket = $this->ticketModel->listar_ticket_x_id($tick_id);
            if (!$ticket) {
                throw new Exception("Ticket no encontrado.");
            }

            $paso_id_pausado = $ticket['paso_actual_id'];

            $this->novedadRepository->crearNovedad($tick_id, $paso_id_pausado, $usu_asig_novedad, $usu_crea_novedad, $descripcion_novedad);

            $this->ticketRepository->updateTicketStatus($tick_id, 'Pausado');

            $comentario = "Se ha creado una novedad: " . htmlspecialchars($descripcion_novedad);
            $this->ticketModel->insert_ticket_detalle($tick_id, $usu_crea_novedad, $comentario);

            // Registrar la asignación de la novedad en el historial principal
            $comentario_asignacion = "Novedad asignada: " . htmlspecialchars($descripcion_novedad);
            $this->assignmentRepository->insertAssignment($tick_id, $usu_asig_novedad, $usu_crea_novedad, $paso_id_pausado, $comentario_asignacion);

            $mensaje_notificacion = "Se te ha asignado una novedad para el ticket #{$tick_id}.";
            $this->notificationRepository->insertNotification($usu_asig_novedad, $mensaje_notificacion, $tick_id);

            $this->pdo->commit();
            return ["status" => "success", "message" => "Novedad creada y ticket pausado."];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function resolverNovedad(array $data): array
    {
        $this->pdo->beginTransaction();
        try {
            $tick_id = (int)$data['tick_id'];
            $usu_resuelve_novedad = (int)$data['usu_id'];

            $novedad = $this->novedadRepository->getNovedadAbiertaPorTicket($tick_id);
            if (!$novedad) {
                throw new Exception("No se encontró una novedad abierta para este ticket.");
            }

            $this->novedadRepository->resolverNovedad($novedad['novedad_id']);

            $this->ticketRepository->updateTicketStatus($tick_id, 'Abierto');

            $fecha_inicio = new DateTime($novedad['fecha_inicio']);
            $fecha_fin = new DateTime();
            $duracion = $fecha_inicio->diff($fecha_fin);
            $duracion_formateada = $duracion->format('%d días, %h horas, %i minutos');

            $comentario = "Se ha resuelto la novedad: " . htmlspecialchars($novedad['descripcion_novedad']) . ".<br><b>Tiempo de resolución de la novedad:</b> " . $duracion_formateada;
            $this->ticketModel->insert_ticket_detalle($tick_id, $usu_resuelve_novedad, $comentario);

            // Notificar al agente que tenía el paso pausado
            $ticket = $this->ticketModel->listar_ticket_x_id($tick_id);
            $usu_asig_original = $ticket['usu_asig'];

            // Registrar el retorno del ticket en el historial de asignaciones
            $comentario_asignacion = "Novedad resuelta. El ticket vuelve al agente original.";
            $this->assignmentRepository->insertAssignment($tick_id, $usu_asig_original, $usu_resuelve_novedad, $novedad['paso_id_pausado'], $comentario_asignacion);

            $mensaje_notificacion = "La novedad del ticket #{$tick_id} ha sido resuelta. Puedes continuar con tu trabajo.";
            $this->notificationRepository->insertNotification($usu_asig_original, $mensaje_notificacion, $tick_id);

            $this->pdo->commit();
            return ["status" => "success", "message" => "Novedad resuelta y ticket reactivado."];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }



}
