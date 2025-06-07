<?php
require_once('../config/conexion.php');
require_once('../models/Ticket.php');
$ticket = new Ticket();

require_once('../models/Usuario.php');
$usuario = new Usuario();

require_once('../models/Documento.php');
$documento = new Documento();

require_once('../models/Email.php');
$correo = new Email();

switch ($_GET["op"]) {

    case "insert":

        $datos = $ticket->insert_ticket($_POST['usu_id'], $_POST['cat_id'], $_POST['cats_id'], $_POST['pd_id'], $_POST['tick_titulo'], $_POST['tick_descrip']);
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
            $sub_array[] = $row['tick_titulo'];

            if ($row['tick_estado'] == 'Abierto') {
                $sub_array[] = '<span class="label label-success">Abierto</span>';
            } else {
                $sub_array[] = '<a onClick="cambiarEstado(' . $row['tick_id'] . ')" ><span class="label label-danger">Cerrado</span></a>';
            }

            if ($row['pd_nom'] == 'Baja') {
                $sub_array[] = '<span class="label label-default">Baja</span>';
            } elseif ($row['pd_nom'] == 'Media') {
                $sub_array[] = '<span class="label label-warning">Media</span>';
            } else {
                $sub_array[] = '<span class="label label-danger">Alta</span>';
            }

            $sub_array[] = date("d/m/Y H:i:s", strtotime($row["fech_crea"]));

            if ($row['fech_asig'] == null) {
                $sub_array[] = '<span class="label label-danger">Sin asignar</span>';
            } else {
                $sub_array[] =  date("d/m/Y H:i:s", strtotime($row["fech_asig"]));
            }

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

    case "listar":

        $datos = $ticket->listar_ticket();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row['tick_id'];
            $sub_array[] = $row['cat_nom'];
            $sub_array[] = $row['tick_titulo'];

            if ($row['tick_estado'] == 'Abierto') {
                $sub_array[] = '<span class="label label-success">Abierto</span>';
            } else {
                $sub_array[] = '<a onClick="cambiarEstado(' . $row['tick_id'] . ')" ><span class="label label-danger">Cerrado</span></a>';
            }

            if ($row['pd_nom'] == 'Baja') {
                $sub_array[] = '<span class="label label-default">Baja</span>';
            } elseif ($row['pd_nom'] == 'Media') {
                $sub_array[] = '<span class="label label-warning">Media</span>';
            } else {
                $sub_array[] = '<span class="label label-danger">Alta</span>';
            }

            $sub_array[] = date("d/m/Y H:i:s", strtotime($row["fech_crea"]));


            if ($row['fech_asig'] == null) {
                $sub_array[] = '<span class="label label-danger">Sin asignar</span>';
            } else {
                $sub_array[] =  date("d/m/Y H:i:s", strtotime($row["fech_asig"]));
            }

            if ($row['usu_asig'] == null) {
                $sub_array[] = '<a onClick="asignar(' . $row['tick_id'] . ')" ><span class="label label-danger">Sin asignar</span></a>';
            } else {
                $datos = $usuario->get_usuario_x_id($row['usu_asig']);
                foreach ($datos as $row2) {
                    $sub_array[] = '<span class="label label-success">' . $row2['usu_nom'] . ' ' . $row2['usu_ape'] . '</span> ';
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

    case "mostrar":
        $datos = $ticket->listar_ticket_x_id($_POST['tick_id']);
        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output['tick_id'] = $row['tick_id'];
                $output['usu_id'] = $row['usu_id'];
                $output['cat_id'] = $row['cat_id'];
                $output['cats_id'] = $row['cats_id'];
                $output['pd_id'] = $row['pd_id'];
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

                if ($row['pd_nom'] == 'Baja') {
                    $output['pd_nom'] = '<span class="label label-default">Baja</span>';
                } elseif ($row['pd_nom'] == 'Media') {
                    $output['pd_nom'] = '<span class="label label-warning">Media</span>';
                } else {
                    $output['pd_nom'] = '<span class="label label-danger">Alta</span>';
                }
            }
            echo json_encode($output);
        }
        break;

    case "insertdetalle":
        $datos = $ticket->insert_ticket_detalle($_POST['tick_id'], $_POST['usu_id'], $_POST['tickd_descrip']);
            if (is_array($datos) == true and count($datos) > 0) {
                foreach ($datos as $row) {
                    $output['tickd_id'] = $row['tickd_id'];

                    if (empty($_FILES['files'])) {
                    } else {
                        $countfiles = is_countable($_FILES['files']['name'] ?? null) ? count($_FILES['files']['name']) : 0;
                        $ruta = '../public/document/detalle/' . $output['tickd_id'] . '/';
                        $files_arr = array();

                        if (!file_exists($ruta)) {
                            mkdir($ruta, 0777, true);
                        }

                        for ($index = 0; $index < $countfiles; $index++) {
                            $doc1 = $_FILES['files']['name'][$index];
                            $tmp_name = $_FILES['files']['tmp_name'][$index];
                            $destino = $ruta . $doc1;

                            $documento->insert_documento_detalle($output['tickd_id'], $doc1);

                            move_uploaded_file($tmp_name, $destino);
                        }
                    }
                }
            }
            echo json_encode($datos);
        break;

    case "update":
        $ticket->update_ticket($_POST['tick_id']);
        $correo->ticket_cerrado($_POST['tick_id']);
        $ticket->insert_ticket_detalle_cerrar($_POST['tick_id'], $_POST['usu_id']);
        break;

    case "reabrir":
        $ticket->reabrir_ticket($_POST['tick_id']);
        // $correo->ticket_cerrado($_POST['tick_id']);
        $ticket->insert_ticket_detalle_reabrir($_POST['tick_id'], $_POST['usu_id']);
        break;

    case "updateasignacion":
        $ticket->update_ticket_asignacion($_POST['tick_id'], $_POST['usu_asig'], $_POST['how_asig']);

        $correo->ticket_asignado($_POST["tick_id"]);

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
}
