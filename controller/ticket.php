<?php
require_once('../config/conexion.php');
require_once('../models/Ticket.php');
$ticket = new Ticket();

require_once('../models/Usuario.php');
$usuario = new Usuario();

require_once('../models/Documento.php');
$documento = new Documento();

require_once('../models/Flujo.php');
$flujoModel = new Flujo();

require_once('../models/FlujoPaso.php');
$flujoPasoModel = new FlujoPaso();

require_once('../models/Departamento.php'); // Asegúrate de que exista
$departamento = new Departamento();

require_once('../models/DateHelper.php');
$dateHelper = new DateHelper();


switch ($_GET["op"]) {

    case "insert":

        $usu_id_creador = $_POST['usu_id'];
        $cats_id = $_POST['cats_id'];
        $session_usu = $_SESSION['usu_id'];
        $emp_id = $_POST['emp_id'];
        $dp_id = $_POST['dp_id'];

        $datos_creador = $usuario->get_usuario_x_id($usu_id_creador);
        $creador_car_id = $datos_creador['car_id'];
        $creador_reg_id = $datos_creador['reg_id'];

        $usu_asig_final = $_POST['usu_asig'] ?? null;
        $paso_actual_id_final = null;

        // --- 2. VERIFICAR SI HAY UN FLUJO Y SI REQUIERE APROBACIÓN ---
        $flujo = $flujoModel->get_flujo_por_subcategoria($cats_id);
        
        if ($flujo) {
            if ($flujo['requiere_aprobacion_jefe'] == 1) {
                // --- 2A. EL FLUJO REQUIERE APROBACIÓN DE JEFE ---
                $paso_actual_id_final = null; // Se pone en NULL porque está pendiente de aprobación
                
                require_once('../models/Organigrama.php');
                $organigrama = new Organigrama();
                $jefe_id = null;

                // 1. Buscar el cargo del jefe en el organigrama
                $jefe_cargo_id = $organigrama->get_jefe_cargo_id($creador_car_id);

                if ($jefe_cargo_id) {
                    // 2. Buscar al usuario que es el jefe, priorizando usuarios nacionales.
                    $jefe_info = null;
                    // a. Primero, buscar un jefe que sea "nacional" con ese cargo.
                    $jefe_info = $usuario->get_usuario_nacional_por_cargo($jefe_cargo_id);
                    // b. Si no se encuentra un jefe nacional, buscar uno en la misma regional del creador.
                    if (!$jefe_info) {
                        $jefe_info = $usuario->get_usuario_por_cargo_y_regional($jefe_cargo_id, $creador_reg_id);
                    }
                    // c. Como fallback, buscar en el mismo departamento si no se encontró por regional.
                    if (!$jefe_info) {
                        $jefe_info = $usuario->get_usuario_por_cargo_y_departamento($jefe_cargo_id, $dp_id);
                    }

                    if ($jefe_info) {
                        $jefe_id = $jefe_info['usu_id'];
                    }
                }
                
                if ($jefe_id) { 
                    $usu_asig_final = $jefe_id; 
                }

            } else {
                // --- 2B. EL FLUJO ES NORMAL (SIN APROBACIÓN PREVIA) ---
                $paso_inicial = $flujoModel->get_paso_inicial_por_flujo($flujo['flujo_id']);
                $paso_actual_id_final = $paso_inicial ? $paso_inicial['paso_id'] : null;

                if (empty($usu_asig_final) && $paso_inicial) {
                    // Asignación automática para el primer paso si no se asignó manualmente
                    $asignado_car_id = $paso_inicial['cargo_id_asignado'];
                    
                    if ($asignado_car_id) {
                        $nuevo_asignado_info = null;
                        
                        if (isset($paso_inicial['es_tarea_nacional']) && $paso_inicial['es_tarea_nacional'] == 1) {
                            $nuevo_asignado_info = $usuario->get_usuario_nacional_por_cargo($asignado_car_id);
                        } else {
                            $nuevo_asignado_info = $usuario->get_usuario_por_cargo_y_regional($asignado_car_id, $creador_reg_id);
                        }

                        if ($nuevo_asignado_info) {
                            $usu_asig_final = $nuevo_asignado_info['usu_id'];
                        }
                    }
                }
            }
        }

        // --- 3. CREAR EL TICKET ---
        $datos = $ticket->insert_ticket(
            $usu_id_creador, $_POST['cat_id'], $cats_id, $_POST['pd_id'], 
            $_POST['tick_titulo'], $_POST['tick_descrip'], $_POST['error_proceso'],
            $usu_asig_final, $paso_actual_id_final, $session_usu, $emp_id, $dp_id
        );

        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output['tick_id'] = $row['tick_id'];

                if (empty($_FILES['files'])) {
                } else {
                    $countfiles = is_countable($_FILES['files']['name'] ?? null) ? count($_FILES['files']['name']) : 0;
                    $ruta = '../public/document/ticket/' . $output['tick_id'] . '/';
                    $files_arr = array();

                    if (!file_exists($ruta)) {
                        mkdir($ruta, 0777, true);
                    }

                    for ($index = 0; $index < $countfiles; $index++) {
                        $doc1 = $_FILES['files']['name'][$index];
                        $tmp_name = $_FILES['files']['tmp_name'][$index];
                        $destino = $ruta . $doc1;

                        $documento->insert_documento($output['tick_id'], $doc1);

                        move_uploaded_file($tmp_name, $destino);
                    }
                }
            }
        }
        echo json_encode($datos);

        break;

    case "listar_x_usu":

        $datos = $ticket->listar_ticket_x_usuario($_POST['usu_id']);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row['tick_id'];
            $sub_array[] = $row['cat_nom'];
            $sub_array[] = $row['cats_nom'];
            $sub_array[] = $row['tick_titulo'];

            if ($row['tick_estado'] == 'Abierto') {
                $sub_array[] = '<span class="label label-success">Abierto</span>';
            } else {
                $sub_array[] = '<a onClick="cambiarEstado(' . $row['tick_id'] . ')" ><span class="label label-danger">Cerrado</span></a>';
            }

            if ($row['prioridad_usuario'] == 'Baja') {
                $sub_array[] = '<span class="label label-default">Baja</span>';
            } elseif ($row['prioridad_usuario'] == 'Media') {
                $sub_array[] = '<span class="label label-warning">Media</span>';
            } else {
                $sub_array[] = '<span class="label label-danger">Alta</span>';
            }

            if ($row['prioridad_defecto'] == 'Baja') {
                $sub_array[] = '<span class="label label-default">Baja</span>';
            } elseif ($row['prioridad_defecto'] == 'Media') {
                $sub_array[] = '<span class="label label-warning">Media</span>';
            } else {
                $sub_array[] = '<span class="label label-danger">Alta</span>';
            }


            $sub_array[] = date("d/m/Y H:i:s", strtotime($row["fech_crea"]));

            if ($row['usu_asig'] == null) {
                $sub_array[] = '<span class="label label-danger">Sin asignar</span>';
            } else {
                $datos = $usuario->get_usuario_x_id($row['usu_asig']);
                    $sub_array[] = '<span class="label label-success">' . $datos['usu_nom'] . ' ' . $datos['usu_ape'] . '</span>';
            }

            $sub_array[] = '<button type="button" onClick="ver(' . $row['tick_id'] . ');" id="' . $row['tick_id'] . '" class="btn btn-inline btn-primary btn-sm ladda-button"><i class="fa fa-eye"></i></button>';
            $data[] = $sub_array;
        }
        $result = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($result);
        break;
    
    case "listar_x_agente":

            $datos = $ticket->listar_ticket_x_agente($_POST['usu_asig']);
            $data = array();
            foreach ($datos as $row) {
                $sub_array = array();
                $sub_array[] = $row['tick_id'];
                $sub_array[] = $row['cat_nom'];
                $sub_array[] = $row['cats_nom'];
                $sub_array[] = $row['tick_titulo'];
    
                if ($row['tick_estado'] == 'Abierto') {
                    $sub_array[] = '<span class="label label-success">Abierto</span>';
                } else {
                    $sub_array[] = '<a onClick="cambiarEstado(' . $row['tick_id'] . ')" ><span class="label label-danger">Cerrado</span></a>';
                }
    
                if ($row['prioridad_usuario'] == 'Baja') {
                $sub_array[] = '<span class="label label-default">Baja</span>';
                } elseif ($row['prioridad_usuario'] == 'Media') {
                    $sub_array[] = '<span class="label label-warning">Media</span>';
                } else {
                    $sub_array[] = '<span class="label label-danger">Alta</span>';
                }

                if ($row['prioridad_defecto'] == 'Baja') {
                    $sub_array[] = '<span class="label label-default">Baja</span>';
                } elseif ($row['prioridad_defecto'] == 'Media') {
                    $sub_array[] = '<span class="label label-warning">Media</span>';
                } else {
                    $sub_array[] = '<span class="label label-danger">Alta</span>';
                }
    
                $sub_array[] = date("d/m/Y H:i:s", strtotime($row["fech_crea"]));
    
                if ($row['usu_id'] == null) {
                    $sub_array[] = '<span class="label label-danger">Sin asignar</span>';
                } else {
                    $datos = $usuario->get_usuario_x_id($row['usu_id']);
                        $sub_array[] = '<span class="label label-primary">' . $datos['usu_nom'] . ' ' . $datos['usu_ape'] . '</span>';
                }
    
    
                $sub_array[] = '<button type="button" onClick="ver(' . $row['tick_id'] . ');" id="' . $row['tick_id'] . '" class="btn btn-inline btn-primary btn-sm ladda-button"><i class="fa fa-eye"></i></button>';

                $data[] = $sub_array;
            }    

        $result = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($result);
        break;

    
    case "aprobar_ticket_jefe":
    // Incluimos los helpers y modelos necesarios
    require_once('../models/DateHelper.php');
    require_once('../models/FlujoPaso.php');
    require_once('../models/Usuario.php');
    $flujoPasoModel = new FlujoPaso();
    $usuario = new Usuario();

    // 1. OBTENER DATOS INICIALES (tu lógica es correcta)
    $tick_id = $_POST['tick_id'];
    $jefe_id = $_SESSION['usu_id'];
    $ticket_data = $ticket->listar_ticket_x_id($tick_id);
    if (!$ticket_data) {
        http_response_code(404);
        echo "Error: El ticket especificado no fue encontrado.";
        exit();
    }
    $cats_id = $ticket_data['cats_id'];
    $regional_id_creador = $ticket->get_ticket_region($tick_id);

    // 2. CALCULAR ESTADO DEL PASO DE APROBACIÓN (tu lógica es correcta)
    $estado_paso_aprobacion = 'N/A';
    $asignacion_actual = $ticket->get_ultima_asignacion($tick_id);
    if ($asignacion_actual) {
        $dias_habiles_aprobacion = 2; // Tiempo límite para que el jefe apruebe
        $fecha_asignacion = $asignacion_actual['fech_asig'];
        $fecha_limite = DateHelper::calcularFechaLimiteHabil($fecha_asignacion, $dias_habiles_aprobacion);
        $fecha_hoy = new DateTime();
        $estado_paso_aprobacion = ($fecha_hoy > $fecha_limite) ? 'Atrasado' : 'A Tiempo';
    }
    
    // 3. ENCONTRAR EL FLUJO Y EL PRIMER PASO (tu lógica es correcta)
    $flujo_data = $flujoModel->get_flujo_por_subcategoria($cats_id);
    if (!$flujo_data) {
        http_response_code(500);
        echo "Error: No se encontró un flujo de trabajo para este tipo de ticket.";
        exit();
    }

    $primer_paso = $flujoModel->get_paso_inicial_por_flujo($flujo_data['flujo_id']);
   if (!$primer_paso) {
        http_response_code(500);
        echo "Error: El flujo de trabajo no tiene un primer paso configurado.";
        exit();
    }
    
    $paso_id_siguiente = $primer_paso['paso_id'];
    $cargo_siguiente_paso = $primer_paso['cargo_id_asignado'];

    // --- 4. LÓGICA DE ASIGNACIÓN AVANZADA (VERSIÓN FINAL) ---
    $nuevo_asignado_info = null;
    $datos_siguiente_paso = $flujoPasoModel->get_paso_por_id($paso_id_siguiente);

    // 1. VERIFICAR SI HAY SELECCIÓN MANUAL (aunque en este flujo no se espera, es una salvaguarda)
    if (isset($_POST['nuevo_asignado_id']) && !empty($_POST['nuevo_asignado_id'])) {
        $nuevo_asignado_info = $usuario->get_usuario_x_id($_POST['nuevo_asignado_id']);
    } else {
        // 2. SI NO HAY SELECCIÓN MANUAL, APLICAR LÓGICA AUTOMÁTICA
        // a. Primero, revisamos si la TAREA en sí es nacional.
        if (isset($datos_siguiente_paso['es_tarea_nacional']) && $datos_siguiente_paso['es_tarea_nacional'] == 1) {
            // Si la tarea es nacional, buscamos al especialista nacional.
            $nuevo_asignado_info = $usuario->get_usuario_nacional_por_cargo($cargo_siguiente_paso);
        } else {
            // b. Si NO, la tarea es regional y usamos la lógica de siempre.
            $nuevo_asignado_info = $usuario->get_usuario_por_cargo_y_regional($cargo_siguiente_paso, $regional_id_creador);
        }
    }
    // --- FIN DE LÓGICA DE ASIGNACIÓN ---

    // 5. EJECUTAR LA APROBACIÓN (tu lógica es correcta)
    if ($nuevo_asignado_info) {
        // a) Reasignamos el ticket al siguiente paso.
        $ticket->update_asignacion_y_paso($tick_id, $nuevo_asignado_info['usu_id'], $paso_id_siguiente, $jefe_id);
        
        // b) Actualizamos el historial del paso de aprobación del jefe.
        if ($asignacion_actual) {
            $ticket->update_estado_tiempo_paso($asignacion_actual['th_id'], $estado_paso_aprobacion);
        }

        // c) Insertamos un comentario de sistema.
        $ticket->insert_ticket_detalle($tick_id, $jefe_id, "Ticket aprobado por jefe. El flujo de trabajo ha comenzado.");

        echo "Aprobación completada exitosamente.";
    } else {
        http_response_code(500);
        echo "Error: No se encontró un agente disponible para el siguiente paso del flujo.";
    }
    break;
    

    case "listar":

        $datos = $ticket->listar_ticket();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row['tick_id'];
            $sub_array[] = $row['cat_nom'];
            $sub_array[] = $row['cats_nom'];
            $sub_array[] = $row['tick_titulo'];

            if ($row['tick_estado'] == 'Abierto') {
                $sub_array[] = '<span class="label label-success">Abierto</span>';
            } else {
                $sub_array[] = '<a onClick="cambiarEstado(' . $row['tick_id'] . ')" ><span class="label label-danger">Cerrado</span></a>';
            }

            if ($row['prioridad_usuario'] == 'Baja') {
                $sub_array[] = '<span class="label label-default">Baja</span>';
            } elseif ($row['prioridad_usuario'] == 'Media') {
                $sub_array[] = '<span class="label label-warning">Media</span>';
            } else {
                $sub_array[] = '<span class="label label-danger">Alta</span>';
            }

            if ($row['prioridad_defecto'] == 'Baja') {
                $sub_array[] = '<span class="label label-default">Baja</span>';
            } elseif ($row['prioridad_defecto'] == 'Media') {
                $sub_array[] = '<span class="label label-warning">Media</span>';
            } else {
                $sub_array[] = '<span class="label label-danger">Alta</span>';
            }

            

            $sub_array[] = date("d/m/Y H:i:s", strtotime($row["fech_crea"]));

            if ($row['usu_asig'] == null) {
                $sub_array[] = '<a><span class="label label-danger">Sin asignar</span></a>';
            } else {
                $datos = $usuario->get_usuario_x_id($row['usu_asig']);
                    $sub_array[] = '<a onClick="asignar(' . $row['tick_id'] . ')" ><span class="label label-success">' . $datos['usu_nom'] . ' ' . $datos['usu_ape'] . '</span></a> ';
            }
            if ($row['usu_id'] == null) {
                $sub_array[] = '<a><span class="label label-danger">Sin asignar</span></a>';
            } else {
                $datos = $usuario->get_usuario_x_id($row['usu_id']);
                    $sub_array[] = '<a onClick="asignar(' . $row['tick_id'] . ')" ><span class="label label-success">' . $datos['usu_nom'] . ' ' . $datos['usu_ape'] . '</span></a> ';
            }
            $sub_array[] = '<button type="button" onClick="ver(' . $row['tick_id'] . ');" id="' . $row['tick_id'] . '" class="btn btn-inline btn-primary btn-sm ladda-button"><i class="fa fa-eye"></i></button>';
            $data[] = $sub_array;
        }

        $result = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($result);
        break;

    case "listardetalle":

        $datos = $ticket->listar_ticketdetalle_x_ticket($_POST['tick_id']);
            ?>
                    <?php
                    foreach ($datos as $row) {
                    ?>
                        <article class="activity-line-item box-typical">
                            <div class="activity-line-date">
                                <?php echo date("d/m/Y", strtotime($row['fech_crea'])) ?>
                            </div>
                            <header class="activity-line-item-header">
                                <div class="activity-line-item-user">
                                    <div class="activity-line-item-user-photo">
                                        <a href="#">
                                            <img src="../../public/img/user-<?php echo $row['rol_id'] ?>.png" alt="">
                                        </a>
                                    </div>
                                    <div class="activity-line-item-user-name"><?php echo $row['usu_nom'] . ' ' . $row['usu_ape'] ?></< /div>
                                        <div class="activity-line-item-user-status">
                                            <?php
                                            if ($row['rol_id'] == 1) {
                                                echo 'Usuario';
                                            } else {
                                                echo 'Soporte';
                                            }
                                            ?>
                                        </div>
                                    </div>
                            </header>
                            <div class="activity-line-action-list">
                                <section class="activity-line-action">
                                    <div class="time"><?php echo date("h:i A", strtotime($row['fech_crea'])) ?></div>
                                    <div class="cont">
                                        <div class="cont-in summernote-content" style="margin-bottom: 8px;">
                                            <p><?php echo $row['tickd_descrip'] ?></p>
                                            <ul class="meta">
                                            </ul>
                                        </div>
                                    <?php
                                    if ($row['det_nom'] != null) {
                                    ?>
                                        <?php if ($row['det_nom'] != null) { ?>
                                            <div class="documentos-attachment p-3 border rounded bg-light">
                                                <p class="mb-3 text-secondary" style="margin-bottom: 0;">
                                                    <i class="fa fa-paperclip"></i> Documento adjunto
                                                </p>
                                                <div class="d-flex justify-content-between align-items-center p-2 bg-white border rounded">
                                                    <a href="../../public/document/detalle/<?php echo $row['tickd_id']; ?>/<?php echo $row['det_nom']; ?>" target="_blank" class="text-decoration-none fw-semibold text-dark">
                                                        <i class="fa fa-file-text-o me-2"></i> <?php echo $row['det_nom']; ?>
                                                    </a>
                                                    <a href="../../public/document/detalle/<?php echo $row['tickd_id']; ?>/<?php echo $row['det_nom']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fa fa-eye"></i> Ver
                                                    </a>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    <?php
                                    }
                                    ?>
                                    </div>
                                </section>
                            </div>
                        </article>
                    <?php
                    }
                    ?>
            <?php
        break;
     
    case "listarhistorial":
        $datos = $ticket->listar_historial_completo($_POST['tick_id']);
        ?>
            <?php
            foreach ($datos as $row) {
                $actor_name = $row['usu_nom'] . ' ' . $row['usu_ape'];
                $rol_text = '';

                if ($row['tipo'] == 'comentario') {
                    $rol_text = ($row['rol_id'] == 1) ? 'Usuario' : 'Soporte';
                } elseif ($row['tipo'] == 'asignacion') {
                    $rol_text = ($row['usu_nom'] == 'Sistema') ? 'Sistema' : (($row['rol_id'] == 1) ? 'Usuario' : 'Soporte');
                } elseif ($row['tipo'] == 'cierre') {
                    $rol_text = 'Sistema';
                }
            ?>
                <article class="activity-line-item box-typical">
                    <div class="activity-line-date">
                        <?php echo date("d/m/Y", strtotime($row['fecha_evento'])) ?>
                    </div>
                    <header class="activity-line-item-header">
                        <div class="activity-line-item-user">
                            <div class="activity-line-item-user-photo">
                                <a href="#">
                                <img src="../../public/img/user-<?php echo $row['rol_id'] ?>.png" alt="">
                                </a>
                            </div>
                            <div class="activity-line-item-user-name"><?php echo $actor_name; ?></div>
                            <div class="activity-line-item-user-status"><?php echo $rol_text; ?></div>
                        </div>
                    </header>
                    <div class="activity-line-action-list">
                        <section class="activity-line-action">
                            <div class="time"><?php echo date("h:i A", strtotime($row['fecha_evento'])) ?></div>
                            <div class="cont">
                                <div class="cont-in summernote-content" style="margin-bottom: 8px;">
                                    <?php if ($row['tipo'] == 'asignacion') : ?>
                                        <p>
                                            <strong>Reasignación de Ticket:</strong><br>
                                            Ticket asignado a <b><?php echo $row['nom_receptor'] . ' ' . $row['ape_receptor']; ?></b>.
                                            <br>
                                            <i><?php echo $row['descripcion']; ?></i>
                                            <?php if (!empty($row['error_descrip'])) : ?>
                                                <br><strong>Descripción del Error:</strong> <?php echo htmlspecialchars($row['error_descrip']); ?>
                                            <?php endif; ?>
                                        </p>
                                        
                                        <?php
                                        if (!empty($row['estado_tiempo_paso']) && $row['estado_tiempo_paso'] != 'N/A') {
                                            $clase_css = ($row['estado_tiempo_paso'] == 'Atrasado') ? 'label-danger' : 'label-success';
                                        ?>
                                            <small class="text-muted">
                                                Estado del paso anterior: <span class="label <?php echo $clase_css; ?>"><?php echo $row['estado_tiempo_paso']; ?></span>
                                            </small>
                                        <?php
                                        }
                                        ?>

                                    <?php elseif ($row['tipo'] == 'cierre') : ?>
                                        <p style="color:red;"><strong><?php echo $row['descripcion']; ?></strong></p>
                                    <?php else: // Es un comentario ?>
                                        <p><?php echo $row['descripcion']; ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php
                            // Muestra adjuntos solo para comentarios
                            if ($row['tipo'] == 'comentario' && $row['det_nom'] != null) {
                            ?>
                                <?php
                            }
                            ?>
                            </div>
                        </section>
                    </div>
                </article>
            <?php
            }
            ?>
    <?php
    break;

    case "listar_historial_tabla_x_agente":
        $datos = $ticket->listar_tickets_involucrados_por_usuario($_POST['usu_id']);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row['tick_id'];
            $sub_array[] = $row['cats_nom'];
            $sub_array[] = $row['tick_titulo'];

            if ($row['tick_estado'] == 'Abierto') {
                $sub_array[] = '<span class="label label-success">Abierto</span>';
            } else {
                $sub_array[] = '<span class="label label-danger">Cerrado</span>';
            }

            $sub_array[] = date("d/m/Y H:i:s", strtotime($row["fech_crea"]));

            if ($row['usu_nom'] === null) {
                $sub_array[] = '<span class="label label-default">Sin Asignar</span>';
            } else {
                $sub_array[] = $row['usu_nom'] . ' ' . $row['usu_ape'];
            }
            
            $sub_array[] = '<button type="button" onClick="ver(' . $row['tick_id'] . ');" class="btn btn-inline btn-primary btn-sm ladda-button" title="Ver Historial Detallado"><i class="fa fa-eye"></i></button>';
            
            $data[] = $sub_array;
        }

        $result = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($result);
    break;
        
    case "listar_historial_tabla":

    $datos = $ticket->listar_tickets_con_historial();
    $data = array();
    foreach ($datos as $row) {
        $sub_array = array();
        $sub_array[] = $row['tick_id'];
        $sub_array[] = $row['cats_nom'];
        $sub_array[] = $row['tick_titulo'];

        if ($row['tick_estado'] == 'Abierto') {
            $sub_array[] = '<span class="label label-success">Abierto</span>';
        } else {
            $sub_array[] = '<span class="label label-danger">Cerrado</span>';
        }

        $sub_array[] = date("d/m/Y H:i:s", strtotime($row["fech_crea"]));

        if ($row['usu_nom'] === null) {
            $sub_array[] = '<span class="label label-default">Sin Asignar</span>';
        } else {
            $sub_array[] = $row['usu_nom'] . ' ' . $row['usu_ape'];
        }
        
        $sub_array[] = '<button type="button" onClick="ver(' . $row['tick_id'] . ');" class="btn btn-inline btn-primary btn-sm ladda-button" title="Ver Historial Detallado"><i class="fa fa-eye"></i></button>';
        
        $data[] = $sub_array;
    }

    $result = array(
        "sEcho" => 1,
        "iTotalRecords" => count($data),
        "iTotalDisplayRecords" => count($data),
        "aaData" => $data
    );
    echo json_encode($result);
    break;




    case "mostrar":
        $datos = $ticket->listar_ticket_x_id($_POST['tick_id']);
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
                $siguientes_pasos = $flujoPasoModel->get_siguientes_pasos($row["paso_actual_id"]);
                if ($siguientes_pasos) {
                    $output["siguientes_pasos"] = $siguientes_pasos;
                }
            }

            $output["paso_actual_info"] = null; // Valor por defecto
            if (!empty($row["paso_actual_id"])) {
                $paso_actual_info = $flujoPasoModel->get_paso_actual($row["paso_actual_id"]);
                if ($paso_actual_info) {
                    $output["paso_actual_info"] = $paso_actual_info;
                }
            }

            $output["timeline_steps"] = []; // Array vacío por defecto
            if (!empty($row["paso_actual_id"])) {
                $flujo_id = $flujoPasoModel->get_flujo_id_from_paso($row["paso_actual_id"]);
                if ($flujo_id) {
                    $todos_los_pasos = $flujoPasoModel->get_pasos_por_flujo($flujo_id);
                    $paso_actual_info = $flujoPasoModel->get_paso_por_id($row["paso_actual_id"]);
                    $orden_actual = $paso_actual_info['paso_orden'];

                    foreach ($todos_los_pasos as $paso) {
                        if ($row['tick_estado'] == 'Cerrado') {
                            $paso['estado'] = 'Completado';
                        } else {
                            if ($paso['paso_orden'] < $orden_actual) {
                                $paso['estado'] = 'Completado';
                            } elseif ($paso['paso_orden'] == $orden_actual) {
                                $paso['estado'] = 'Actual';
                            } else {
                                $paso['estado'] = 'Pendiente';
                            }
                        }
                        $output["timeline_steps"][] = $paso;
                    }
                }
            }

            $mi_ticket = $datos;
            $estado_tiempo = '<span class="label label-defa">N/A</span>';

            if ($mi_ticket['tick_estado'] == 'Abierto' && !empty($mi_ticket['paso_actual_id'])) {
                $fecha_asignacion = $ticket->get_fecha_ultima_asignacion($mi_ticket['tick_id']);
                $paso_info = $flujoPasoModel->get_paso_por_id($mi_ticket['paso_actual_id']);
                $dias_habiles_permitidos = $paso_info['paso_tiempo_habil'];

                if ($fecha_asignacion && $dias_habiles_permitidos > 0) {
                    $fecha_limite = $dateHelper->calcularFechaLimiteHabil($fecha_asignacion, $dias_habiles_permitidos);
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
        break;
    
    case "insertdetalle":
    // 1. Guardar comentario y archivos (tu código existente no cambia)
    $datos = $ticket->insert_ticket_detalle($_POST["tick_id"], $_POST["usu_id"], $_POST['tickd_descrip']);
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
                $documento->insert_documento_detalle($tickd_id, $doc1);
                move_uploaded_file($tmp_name, $destino);
            }
        }
    }

    // 2. Verificar si se debe avanzar en el flujo
    if (isset($_POST["siguiente_paso_id"]) && !empty($_POST["siguiente_paso_id"])) {
        
        // Incluimos los modelos necesarios
        require_once("../models/DateHelper.php");
        require_once("../models/FlujoPaso.php");
        require_once("../models/Usuario.php");
        $flujoPasoModel = new FlujoPaso();
        $usuario = new Usuario();

        $tick_id = $_POST["tick_id"];
        $estado_paso_actual = 'N/A';
        
        // Medir el tiempo del paso que está por terminar (tu lógica es correcta y no cambia)
        $asignacion_actual = $ticket->get_ultima_asignacion($tick_id);
        if ($asignacion_actual) {
            $paso_actual_info = $flujoPasoModel->get_paso_por_id($asignacion_actual['paso_actual_id']);
            if ($paso_actual_info) {
                $fecha_asignacion = $asignacion_actual['fech_asig'];
                $dias_habiles = $paso_actual_info['paso_tiempo_habil'];

                if ($dias_habiles > 0) {
                    // b. Calculamos si se completó a tiempo
                    $fecha_limite = $dateHelper->calcularFechaLimiteHabil($fecha_asignacion, $dias_habiles);
                    $fecha_hoy = new DateTime();
                    $estado_paso_actual = ($fecha_hoy > $fecha_limite) ? 'Atrasado' : 'A Tiempo';
                }
            }
        }

        // --- INICIA LÓGICA DE ASIGNACIÓN AVANZADA ---
        $nuevo_asignado_info = null;
        $siguiente_paso_id = $_POST["siguiente_paso_id"];
        $datos_siguiente_paso = $flujoPasoModel->get_paso_por_id($siguiente_paso_id);

        if ($datos_siguiente_paso) {
            $siguiente_cargo_id = $datos_siguiente_paso["cargo_id_asignado"];

            // 1. VERIFICAR SI HAY SELECCIÓN MANUAL
            if (isset($_POST['nuevo_asignado_id']) && !empty($_POST['nuevo_asignado_id'])) {
                $nuevo_asignado_info = $usuario->get_usuario_x_id($_POST['nuevo_asignado_id']);
            } else {
                // 2. SI NO HAY SELECCIÓN MANUAL, APLICAR LÓGICA AUTOMÁTICA
                if ($datos_siguiente_paso['es_tarea_nacional'] == 1) {
                    // a. Si la tarea es nacional, buscar especialista nacional
                    $nuevo_asignado_info = $usuario->get_usuario_nacional_por_cargo($siguiente_cargo_id);
                } else {
                    // b. Si la tarea es regional, buscar por región
                    $regional_origen_id = $ticket->get_ticket_region($tick_id);
                    $nuevo_asignado_info = $usuario->get_usuario_por_cargo_y_regional($siguiente_cargo_id, $regional_origen_id);
                }
            }
        }
        // --- FINALIZA LÓGICA DE ASIGNACIÓN AVANZADA ---

        // Si se encontró un usuario, se procede (esta parte no cambia)
        if ($nuevo_asignado_info) {
            $nuevo_usuario_asignado = $nuevo_asignado_info["usu_id"];
            $quien_asigno_id = $_SESSION["usu_id"];
            
            $ticket->update_asignacion_y_paso($tick_id, $nuevo_usuario_asignado, $siguiente_paso_id, $quien_asigno_id);
            
            if ($asignacion_actual) {
                $ticket->update_estado_tiempo_paso($asignacion_actual['th_id'], $estado_paso_actual);
            }
        }
    }
    
    $reassigned = isset($nuevo_asignado_info) && $nuevo_asignado_info;
    echo json_encode(["status" => "success", "reassigned" => $reassigned]);
    break;

    case "get_usuarios_por_paso":
    $paso_id = $_POST['paso_id'];
    // Obtenemos los datos del paso para saber qué cargo se necesita
    $paso_data = $flujoPasoModel->get_paso_por_id($paso_id);
    if ($paso_data) {
        $cargo_id_necesario = $paso_data['cargo_id_asignado'];
        // Buscamos a TODOS los usuarios con ese cargo
        $usuarios = $usuario->get_usuarios_por_cargo($cargo_id_necesario);
        
        $html = "<option value=''>Seleccione un Agente</option>";
        if (is_array($usuarios) && count($usuarios) > 0) {
            foreach ($usuarios as $row) {
                // Mostramos el nombre y su regional para poder diferenciarlos
                $html .= "<option value='" . $row["usu_id"] . "'>" . $row["usu_nom"] . " " . $row["usu_ape"] . "</option>";
            }
        }
        echo $html;
    }
    break;

    case "update":
    // --- INICIA LÓGICA DE MEDICIÓN DE TIEMPO DEL ÚLTIMO PASO ---
    require_once('../models/DateHelper.php');
    $dateHelper = new DateHelper();
    require_once('../models/FlujoPaso.php');
    $flujoPasoModel = new FlujoPaso();

    $tick_id = $_POST['tick_id'];
    $usu_id = $_POST['usu_id'];

    // a. Obtenemos la información de la última asignación (el paso que está por terminar)
    $asignacion_actual = $ticket->get_ultima_asignacion($tick_id);
    
    // Solo ejecutamos la lógica de tiempo si el ticket estaba en un flujo
    if ($asignacion_actual && !empty($asignacion_actual['paso_actual_id'])) {
        $paso_actual_info = $flujoPasoModel->get_paso_por_id($asignacion_actual['paso_actual_id']);
        
        if ($paso_actual_info) {
            $estado_paso_final = 'N/A';
            $fecha_asignacion = $asignacion_actual['fech_asig'];
            $dias_habiles = $paso_actual_info['paso_tiempo_habil'];

            if ($dias_habiles > 0) {
                // b. Calculamos si se completó a tiempo
                $fecha_limite = $dateHelper->calcularFechaLimiteHabil($fecha_asignacion, $dias_habiles);
                $fecha_hoy = new DateTime();
                $estado_paso_final = ($fecha_hoy > $fecha_limite) ? 'Atrasado' : 'A Tiempo';
            }

            // c. Actualizamos el historial de ESE ÚLTIMO PASO con su estado final
            $ticket->update_estado_tiempo_paso($asignacion_actual['th_id'], $estado_paso_final);
        }
    }
    // --- FINALIZA LÓGICA DE MEDICIÓN ---


    // --- TU LÓGICA ORIGINAL PARA CERRAR EL TICKET (AHORA VA DESPUÉS) ---
    $ticket->update_ticket($_POST['tick_id']);
    $ticket->insert_ticket_detalle_cerrar($_POST['tick_id'], $_POST['usu_id']);

    break;

    case "reabrir":
        $ticket->reabrir_ticket($_POST['tick_id']);
        // $correo->ticket_cerrado($_POST['tick_id']);
        $ticket->insert_ticket_detalle_reabrir($_POST['tick_id'], $_POST['usu_id']);
        break;

    case "updateasignacion":
        $ticket->update_ticket_asignacion($_POST['tick_id'], $_POST['usu_asig'], $_POST['how_asig']);
        break;

    case "total":
        $datos = $ticket->get_ticket_total();
        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output['TOTAL'] = $row['TOTAL'];
            }
            echo json_encode($output);
        }
        break;

    case "totalabierto":
        $datos = $ticket->get_ticket_totalabierto_id();
        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output['TOTAL'] = $row['TOTAL'];
            }
            echo json_encode($output);
        }
        break;

    case "totalcerrado":
        $datos = $ticket->get_ticket_totalcerrado_id();
        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output['TOTAL'] = $row['TOTAL'];
            }
            echo json_encode($output);
        }
        break;

    case "grafico":
        $datos = $ticket->get_total_categoria();
        echo json_encode($datos);
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
    $tick_id = $_POST['tick_id'];
    $jefe_id = $_SESSION['usu_id']; // El jefe que está aprobando

    // Obtenemos los datos del ticket para saber quién lo creó y de qué subcategoría es
    $datos_ticket = $ticket->listar_ticket_x_id($tick_id)[0];
    $usu_id_creador = $datos_ticket['usu_id'];
    $cats_id = $datos_ticket['cats_id'];

    // Obtenemos todos los datos del usuario creador
    $datos_creador = $usuario->get_usuario_x_id($usu_id_creador);
    $creador_car_id = $datos_creador['car_id'];
    $creador_reg_id = $datos_creador['reg_id'];

    // Buscamos el flujo y su primer paso
    $flujo = $flujoModel->get_flujo_por_subcategoria($cats_id);
    if ($flujo) {
        $paso_inicial = $flujoModel->get_paso_inicial_por_flujo($flujo['flujo_id']);
        if ($paso_inicial) {
            $primer_paso_id = $paso_inicial['paso_id'];
            $primer_cargo_id = $paso_inicial['cargo_id_asignado'];

            // Buscamos al agente que cumple con el primer cargo y la regional del creador
            $primer_agente_info = $usuario->get_usuario_por_cargo_y_regional($primer_cargo_id, $creador_reg_id);

            if ($primer_agente_info) {
                $primer_agente_id = $primer_agente_info['usu_id'];
                
                // Actualizamos el ticket: se lo asignamos al primer agente y le ponemos el primer paso
                $ticket->update_asignacion_y_paso($tick_id, $primer_agente_id, $primer_paso_id, $jefe_id);
            }
        }
    }
    break;    

   case "registrar_error":
    $tick_id = $_POST['tick_id'];
    $answer_id = $_POST['answer_id'];
    $usu_id = $_POST['usu_id']; // ID del usuario que reporta el error (el analista)
    $error_descrip = $_POST['error_descrip']; // Nueva descripción del error

    // Buscamos el nombre de la respuesta rápida
    require_once('../models/RespuestaRapida.php');
    $respuesta_rapida = new RespuestaRapida();
    $datos_respuesta = $respuesta_rapida->get_respuestarapida_x_id($answer_id);
    $nombre_respuesta = $datos_respuesta["answer_nom"];
    $es_error_proceso = $datos_respuesta["es_error_proceso"];

    // --- LÓGICA UNIFICADA Y CORREGIDA ---

    // 1. Buscamos el registro de la asignación ANTERIOR para saber a quién atribuirle el error.
    $asignacion_anterior = $ticket->get_penultima_asignacion($tick_id);
    
    $nombre_completo_responsable = null;
    if ($asignacion_anterior) {
        $nombre_completo_responsable = $asignacion_anterior['usu_nom'] . ' ' . $asignacion_anterior['usu_ape'];
        
        // 2. "Sellamos" ese registro de historial con el código del error y la descripción.
        $ticket->update_error_code_paso($asignacion_anterior['th_id'], $answer_id, $error_descrip);
    }

    // 3. Construimos el comentario para el historial visible.
    $comentario = "Se registró un evento: <b>" . $nombre_respuesta . "</b>.";
    if (!empty($error_descrip)) {
        $comentario .= "<br><b>Descripción:</b> " . htmlspecialchars($error_descrip);
    }
    if ($nombre_completo_responsable) {
        $comentario .= "<br><small class='text-muted'>Error atribuido al paso anterior, asignado a: <b>" . $nombre_completo_responsable . "</b></small>";
    }

    // 4. Marcamos el ticket con el código de error en la tabla principal (para la alerta visual).
    $ticket->update_error_proceso($tick_id, $answer_id);

    // 5. Insertamos el nuevo comentario detallado en el historial.
    $ticket->insert_ticket_detalle($tick_id, $usu_id, $comentario);

    // 6. Si es error de proceso, reasignamos al usuario anterior y retrocedemos el paso.
    if ($es_error_proceso && $asignacion_anterior) {
        $usuario_anterior_id = $asignacion_anterior['usu_asig'];
        $paso_anterior_id = $asignacion_anterior['paso_id'];
        $quien_asigno_id = $usu_id;

        $comentario_reasignacion = "Ticket devuelto por error de proceso.";
        $mensaje_notificacion = "Se te ha devuelto el Ticket #" . $tick_id . " por un error en el proceso.";

        $ticket->update_asignacion_y_paso($tick_id, $usuario_anterior_id, $paso_anterior_id, $quien_asigno_id, $comentario_reasignacion, $mensaje_notificacion);
    }

    echo json_encode(["status" => "success"]);
    break;

    // En controller/ticket.php

    case "verificar_inicio_flujo":
        $cats_id = $_POST['cats_id'];
        $output = ['requiere_seleccion' => false, 'usuarios' => []];

        // Buscamos el flujo asociado a la subcategoría
        $flujo = $flujoModel->get_flujo_por_subcategoria($cats_id);
        if ($flujo) {
            // Si hay flujo, buscamos su primer paso
            $primer_paso = $flujoModel->get_paso_inicial_por_flujo($flujo['flujo_id']);
            if ($primer_paso && $primer_paso['requiere_seleccion_manual'] == 1) {
                // Si el primer paso requiere selección manual, preparamos la respuesta
                $output['requiere_seleccion'] = true;
                $cargo_id_necesario = $primer_paso['cargo_id_asignado'];
                
                // Buscamos a TODOS los usuarios con ese cargo
                $usuarios = $usuario->get_usuarios_por_cargo($cargo_id_necesario);
                $output['usuarios'] = $usuarios;
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($output);
        break;
}
