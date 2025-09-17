<?php

require_once('../models/Ticket.php');
require_once('../models/Usuario.php');
require_once('../models/Documento.php');
require_once('../models/Flujo.php');
require_once('../models/FlujoPaso.php');
require_once('../models/Departamento.php');
require_once('../models/DateHelper.php');

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

        if ($flujo) {
            if ($flujo['requiere_aprobacion_jefe'] == 1) {
                require_once('../models/Organigrama.php');
                $organigrama = new Organigrama();

                $jefe_cargo_id = $organigrama->get_jefe_cargo_id($creador_car_id);

                if (!$jefe_cargo_id) {
                    $errors[] = "No existe definición de cargo de jefe para el cargo del creador (cargo_id: {$creador_car_id}).";
                } else {
                    $jefe_info = $this->usuarioModel->get_usuario_nacional_por_cargo($jefe_cargo_id);

                    if (!$jefe_info) {
                        $jefe_info = $this->usuarioModel->get_usuario_por_cargo_y_regional($jefe_cargo_id, $ticket_reg_id);
                    }
                    if (!$jefe_info) {
                        $jefe_info = $this->usuarioModel->get_usuario_por_cargo_y_departamento($jefe_cargo_id, $dp_id);
                    }

                    if ($jefe_info && !empty($jefe_info['usu_id'])) {
                        $usu_asig_final = $jefe_info['usu_id'];
                    } else {
                        $errors[] = "No se encontró un jefe asignable para el cargo (cargo_id: {$jefe_cargo_id}).";
                    }
                }
            } else {
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
                $resolveResult['usu_asig_final'],
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

            $output["siguientes_pasos"] = []; // Valor por defecto
            if (!empty($row["paso_actual_id"])) {
                $siguientes_pasos = $this->flujoPasoModel->get_siguientes_pasos($row["paso_actual_id"]);
                if ($siguientes_pasos) {
                    $output["siguientes_pasos"] = $siguientes_pasos;
                }
            }

            $output["paso_actual_info"] = null; // Valor por defecto
            if (!empty($row["paso_actual_id"])) {
                $paso_actual_info = $this->flujoPasoModel->get_paso_actual($row["paso_actual_id"]);
                if ($paso_actual_info) {
                    $output["paso_actual_info"] = $paso_actual_info;
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

    public function createDetailTicket($tickId, $usuId, $tickdDescrip)
    {
        // 1. Guardar comentario y archivos (tu código existente no cambia)
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

        // 2. Verificar si se debe avanzar en el flujo
        if (isset($_POST["siguiente_paso_id"]) && !empty($_POST["siguiente_paso_id"])) {
            $tick_id = $_POST["tick_id"];
            $estado_paso_actual = 'N/A';

            // Medir el tiempo del paso que está por terminar (tu lógica es correcta y no cambia)
            $asignacion_actual = $this->ticketModel->get_ultima_asignacion($tick_id);
            if ($asignacion_actual) {
                $paso_actual_info = $this->flujoPasoModel->get_paso_por_id($asignacion_actual['paso_actual_id']);
                if ($paso_actual_info) {
                    $fecha_asignacion = $asignacion_actual['fech_asig'];
                    $dias_habiles = $paso_actual_info['paso_tiempo_habil'];

                    if ($dias_habiles > 0) {
                        // b. Calculamos si se completó a tiempo
                        $fecha_limite = $this->dateHelper->calcularFechaLimiteHabil($fecha_asignacion, $dias_habiles);
                        $fecha_hoy = new DateTime();
                        $estado_paso_actual = ($fecha_hoy > $fecha_limite) ? 'Atrasado' : 'A Tiempo';
                    }
                }
            }

            // --- INICIA LÓGICA DE ASIGNACIÓN AVANZADA ---
            $nuevo_asignado_info = null;
            $siguiente_paso_id = $_POST["siguiente_paso_id"];
            $datos_siguiente_paso = $this->flujoPasoModel->get_paso_por_id($siguiente_paso_id);

            if ($datos_siguiente_paso) {
                $siguiente_cargo_id = $datos_siguiente_paso["cargo_id_asignado"];

                // 1. VERIFICAR SI HAY SELECCIÓN MANUAL
                if (isset($_POST['nuevo_asignado_id']) && !empty($_POST['nuevo_asignado_id'])) {
                    $nuevo_asignado_info = $this->usuarioModel->get_usuario_x_id($_POST['nuevo_asignado_id']);
                } else {
                    // 2. SI NO HAY SELECCIÓN MANUAL, APLICAR LÓGICA AUTOMÁTICA
                    if ($datos_siguiente_paso['es_tarea_nacional'] == 1) {
                        // a. Si la tarea es nacional, buscar especialista nacional
                        $nuevo_asignado_info = $this->usuarioModel->get_usuario_nacional_por_cargo($siguiente_cargo_id);
                    } else {
                        // b. Si la tarea es regional, buscar por región
                        $regional_origen_id = $this->ticketModel->get_ticket_region($tick_id);
                        $nuevo_asignado_info = $this->usuarioModel->get_usuario_por_cargo_y_regional($siguiente_cargo_id, $regional_origen_id);
                    }
                }
            }
            // --- FINALIZA LÓGICA DE ASIGNACIÓN AVANZADA ---

            // Si se encontró un usuario, se procede (esta parte no cambia)
            if ($nuevo_asignado_info) {
                $nuevo_usuario_asignado = $nuevo_asignado_info["usu_id"];
                $quien_asigno_id = $_SESSION["usu_id"];

                $this->ticketModel->update_asignacion_y_paso($tick_id, $nuevo_usuario_asignado, $siguiente_paso_id, $quien_asigno_id);

                if ($asignacion_actual) {
                    $this->ticketModel->update_estado_tiempo_paso($asignacion_actual['th_id'], $estado_paso_actual);
                }
            }
        }

        $reassigned = isset($nuevo_asignado_info) && $nuevo_asignado_info;
        echo json_encode(["status" => "success", "reassigned" => $reassigned]);
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
        $tick_id = $dataPost['tick_id'];
        $answer_id = $dataPost['answer_id'];
        $usu_id = $dataPost['usu_id']; // ID del usuario que reporta el error (el analista)
        $error_descrip = $dataPost['error_descrip']; // Nueva descripción del error

        // Buscamos el nombre de la respuesta rápida
        require_once('../models/RespuestaRapida.php');
        $respuesta_rapida = new RespuestaRapida();
        $datos_respuesta = $respuesta_rapida->get_respuestarapida_x_id($answer_id);
        $nombre_respuesta = $datos_respuesta["answer_nom"];
        $es_error_proceso = $datos_respuesta["es_error_proceso"];

        // --- LÓGICA UNIFICADA Y CORREGIDA ---

        // 1. Buscamos el registro de la asignación ANTERIOR para saber a quién atribuirle el error.
        $asignacion_anterior = $this->ticketModel->get_penultima_asignacion($tick_id);

        $nombre_completo_responsable = null;
        $id_historial_a_sellar = null;

        if ($asignacion_anterior) {
            $nombre_completo_responsable = $asignacion_anterior['usu_nom'] . ' ' . $asignacion_anterior['usu_ape'];
            $id_historial_a_sellar = $asignacion_anterior['th_id'];
        } else {
            // Si no hay asignación penúltima (es un flujo de un solo paso), atribuimos al creador.
            $primera_asignacion = $this->ticketModel->get_primera_asignacion($tick_id);
            if ($primera_asignacion) {
                // El responsable es el creador del ticket
                $ticket_data = $this->ticketModel->listar_ticket_x_id($tick_id);
                $nombre_completo_responsable = $ticket_data['usu_nom'] . ' ' . $ticket_data['usu_ape'];
                $id_historial_a_sellar = $primera_asignacion['th_id'];
            }
        }

        if ($id_historial_a_sellar) {
            // 2. "Sellamos" ese registro de historial con el código del error y la descripción.
            $this->ticketModel->update_error_code_paso($id_historial_a_sellar, $answer_id, $error_descrip);
        }

        // 3. Construimos el comentario para el historial visible.
        $comentario = "Se registró un evento: <b>" . $nombre_respuesta . "</b>.";
        if (!empty($error_descrip)) {
            $comentario .= "<br><b>Descripción:</b> " . htmlspecialchars($error_descrip);
        }
        if ($nombre_completo_responsable) {
            $comentario .= "<br><small class='text-muted'>Error atribuido a: <b>" . $nombre_completo_responsable . "</b></small>";
        }

        // 4. Marcamos el ticket con el código de error en la tabla principal (para la alerta visual).
        $this->ticketModel->update_error_proceso($tick_id, $answer_id);

        // 5. Insertamos el nuevo comentario detallado en el historial.
        $this->ticketModel->insert_ticket_detalle($tick_id, $usu_id, $comentario);

        // 6. Si es error de proceso, reasignamos al usuario anterior y retrocedemos el paso.
        if ($es_error_proceso && $asignacion_anterior) {
            $usuario_anterior_id = $asignacion_anterior['usu_asig'];
            $paso_anterior_id = $asignacion_anterior['paso_id'];
            $quien_asigno_id = $usu_id;

            $comentario_reasignacion = "Ticket devuelto por error de proceso.";
            $mensaje_notificacion = "Se te ha devuelto el Ticket #" . $tick_id . " por un error en el proceso.";

            $this->ticketModel->update_asignacion_y_paso($tick_id, $usuario_anterior_id, $paso_anterior_id, $quien_asigno_id, $comentario_reasignacion, $mensaje_notificacion);
        }

        echo json_encode(["status" => "success"]);
    }
}
