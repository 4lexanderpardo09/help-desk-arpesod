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


switch ($_GET["op"]) {

    case "insert":

            // 2. Se recopilan todos los datos del formulario.
            $usu_id = $_POST['usu_id'];
            $cat_id = $_POST['cat_id'];
            $cats_id = $_POST['cats_id'];
            $pd_id = $_POST['pd_id'];
            $tick_titulo = $_POST['tick_titulo'];
            $tick_descrip = $_POST['tick_descrip'];
            $error_proceso = $_POST['error_proceso'];
            $usu_asig_manual = $_POST['usu_asig']; // La selección manual del formulario.

            // 3. Se busca la asignación automática que sugiere el flujo.
            $usu_asig_flujo = null;
            $paso_actual_id_final = null;
            $flujo = $flujoModel->get_flujo_por_subcategoria($cats_id);

            if ($flujo) {
                $paso_inicial = $flujoModel->get_paso_inicial_por_flujo($flujo['flujo_id']);
                if ($paso_inicial) {
                    $paso_actual_id_final = $paso_inicial["paso_id"];
                    $usu_asig_flujo = $paso_inicial["usu_asig"];
                }
            }

            // 4. Se toma la decisión final sobre a quién asignar el ticket.
            $usu_asig_final = $usu_asig_manual; // Por defecto, la selección manual tiene prioridad.

            if (empty($usu_asig_manual) && !empty($usu_asig_flujo)) {
                // PERO, si no se seleccionó a nadie manualmente Y el flujo sí sugiere a alguien,
                // entonces se usa la asignación del flujo.
                $usu_asig_final = $usu_asig_flujo;
            }

            // 5. Se crea el ticket UNA SOLA VEZ con los datos correctos.
            // Asegúrate de que tu función insert_ticket en el modelo acepte el parámetro $paso_actual_id.
            $datos = $ticket->insert_ticket(
                $usu_id, 
                $cat_id, 
                $cats_id, 
                $pd_id, 
                $tick_titulo, 
                $tick_descrip, 
                $error_proceso, 
                $usu_asig_final,
                $paso_actual_id_final
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
                foreach ($datos as $row2) {
                    $sub_array[] = '<span class="label label-success">' . $row2['usu_nom'] . ' ' . $row2['usu_ape'] . '</span>';
                }
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
                    foreach ($datos as $row2) {
                        $sub_array[] = '<span class="label label-primary">' . $row2['usu_nom'] . ' ' . $row2['usu_ape'] . '</span>';
                    }
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
                foreach ($datos as $row2) {
                    $sub_array[] = '<a onClick="asignar(' . $row['tick_id'] . ')" ><span class="label label-success">' . $row2['usu_nom'] . ' ' . $row2['usu_ape'] . '</span></a> ';
                }
            }
            if ($row['usu_id'] == null) {
                $sub_array[] = '<a><span class="label label-danger">Sin asignar</span></a>';
            } else {
                $datos = $usuario->get_usuario_x_id($row['usu_id']);
                foreach ($datos as $row2) {
                    $sub_array[] = '<a onClick="asignar(' . $row['tick_id'] . ')" ><span class="label label-success">' . $row2['usu_nom'] . ' ' . $row2['usu_ape'] . '</span></a> ';
                }
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
            // Se itera sobre los datos originales sin filtrar
            foreach ($datos as $row) {
                // Determina el estilo, icono y texto según el tipo de evento
                $box_class = '';
                $icon_class = '';
                $actor_name = $row['usu_nom'] . ' ' . $row['usu_ape'];
                $rol_text = '';

                if ($row['tipo'] == 'comentario') {
                    $box_class = 'box-typical';
                    $icon_class = 'fa fa-commenting';
                    $rol_text = ($row['rol_id'] == 1) ? 'Usuario' : 'Soporte';

                } elseif ($row['tipo'] == 'asignacion') {
                    $box_class = 'box-typical-blue';
                    $icon_class = 'fa fa-exchange';
                    // Si el que asigna es el sistema (primera vez), el rol es "Sistema".
                    // Si no, se muestra el rol del usuario que reasigna.
                    if ($row['usu_nom'] == 'Sistema') {
                        $rol_text = 'Sistema';
                    } else {
                        $rol_text = ($row['rol_id'] == 1) ? 'Usuario' : 'Soporte';
                    }

                } elseif ($row['tipo'] == 'cierre') {
                    $box_class = 'box-typical-green';
                    $icon_class = 'fa fa-check-square';
                    $rol_text = 'Sistema'; // El cierre es un evento final del sistema
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
                                        </p>
                                    <?php elseif ($row['tipo'] == 'cierre') : ?>
                                        <p><strong><?php echo $row['descripcion']; ?></strong></p>
                                    <?php else: // Es un comentario ?>
                                        <p><?php echo $row['descripcion']; ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php
                            // Muestra adjuntos solo para comentarios
                            if ($row['tipo'] == 'comentario' && $row['det_nom'] != null) {
                            ?>
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
            foreach ($datos as $row) {
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

                $output["siguiente_paso"] = null; // Valor por defecto
                if (!empty($row["paso_actual_id"])) {
                    // Instanciamos el modelo del flujo
                    
                    // Llamamos a la función que ya confirmamos que funciona
                    $siguiente_paso = $flujoPasoModel->get_siguiente_paso($row["paso_actual_id"]);
                    
                    // Añadimos el resultado al output
                    if ($siguiente_paso) {
                        $output["siguiente_paso"] = $siguiente_paso;
                    }
                }

                $output["timeline_steps"] = []; // Array vacío por defecto
                if (!empty($row["paso_actual_id"])) {
                    
                    // 1. Obtenemos el flujo_id del ticket actual
                    $flujo_id = $flujoPasoModel->get_flujo_id_from_paso($row["paso_actual_id"]);
                    
                    if ($flujo_id) {
                        // 2. Obtenemos TODOS los pasos de ese flujo
                        $todos_los_pasos = $flujoPasoModel->get_pasos_por_flujo($flujo_id);
                        
                        // 3. Obtenemos el orden del paso actual para comparar
                        $paso_actual_info = $flujoPasoModel->get_paso_por_id($row["paso_actual_id"]);
                        $orden_actual = $paso_actual_info['paso_orden'];

                        // 4. Añadimos el estado a cada paso (Completado, Actual, Pendiente)
                        foreach ($todos_los_pasos as $paso) {
                            $estado = '';
                            if ($paso['paso_orden'] < $orden_actual) {
                                $estado = 'Completado';
                            } elseif ($paso['paso_orden'] == $orden_actual) {
                                $estado = 'Actual';
                            } else {
                                $estado = 'Pendiente';
                            }
                            $paso['estado'] = $estado;
                            $output["timeline_steps"][] = $paso;
                        }
                    }
                }
            }
            echo json_encode($output);
        }
        break;

    case "insertdetalle":
    // 1. Obtenemos el tick_id directamente de $_POST para usarlo después.
    $tick_id = $_POST['tick_id'];

    // 2. Se guarda el comentario del agente.
    $datos = $ticket->insert_ticket_detalle($tick_id, $_POST['usu_id'], $_POST['tickd_descrip']);

    // 3. Se maneja la subida de archivos.
    if (is_array($datos) && count($datos) > 0) {
        $row = $datos[0];
        $tickd_id = $row['tickd_id']; // Se usa el ID del detalle recién creado.

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

    // 4. Se revisa si debemos avanzar en el flujo.
    if (isset($_POST["siguiente_paso_id"]) && !empty($_POST["siguiente_paso_id"])) {
        // 5. Instanciamos el modelo FlujoPaso aquí, solo si es necesario.
        require_once("../models/FlujoPaso.php");
        $flujoModel = new FlujoPaso();
        
        $siguiente_paso_id = $_POST["siguiente_paso_id"];
        $datos_siguiente_paso = $flujoModel->get_paso_por_id($siguiente_paso_id);

        if ($datos_siguiente_paso) {
            $nuevo_usuario_asignado = $datos_siguiente_paso["usu_asig"];
            $quien_asigno_id = $_SESSION["usu_id"];
            
            // 6. Se llama a la función para actualizar el ticket.
            $ticket->update_asignacion_y_paso($tick_id, $nuevo_usuario_asignado, $siguiente_paso_id, $quien_asigno_id);
        }
    }

    echo json_encode(["status" => "success"]);
    break;    

    case "update":
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
    
}
